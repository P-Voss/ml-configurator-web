<?php

namespace App\State\Modeltype;

use App\CodeGenerator\AbstractCodegenerator;
use App\CodeGenerator\Feedforward;
use App\Entity\DecisiontreeConfiguration;
use App\Entity\Layer;
use App\Entity\LinRegConfiguration;
use App\Entity\LogRegConfiguration;
use App\Entity\Model;
use App\Entity\SvmConfiguration;
use App\Entity\TrainingTask;
use App\Enum\ModelTypes;
use App\Event\SubjectTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class AbstractState implements StateInterface
{

    use SubjectTrait;

    protected Model $model;
    protected readonly EntityManagerInterface $entityManager;

    public function __construct(Model $model, EntityManagerInterface $entityManager)
    {
        $this->model = $model;
        $this->entityManager = $entityManager;
    }

    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function delete(): void
    {
        $this->clearConfiguration();
        foreach ($this->model->getFieldConfigurations() as $fieldConfiguration) {
            $this->model->removeFieldConfiguration($fieldConfiguration);
            $this->entityManager->remove($fieldConfiguration);
        }
        if (file_exists($this->model->getModelPath())) {
            unlink($this->model->getModelPath());
        }
        if (file_exists($this->model->getCheckpointPath())) {
            unlink($this->model->getCheckpointPath());
        }
        if (file_exists($this->model->getScalerPath())) {
            unlink($this->model->getScalerPath());
        }
        foreach ($this->model->getTrainingTasks() as $task) {
            if (file_exists($task->getReportPath())) {
                unlink($task->getReportPath());
            }
            if (file_exists($task->getLogPath())) {
                unlink($task->getLogPath());
            }
            if (file_exists($task->getScriptPath())) {
                unlink($task->getScriptPath());
            }
            $this->model->removeTrainingTask($task);
            $this->entityManager->remove($task);
        }
        $this->entityManager->remove($this->model);
    }

    public function getCodegenerator(): AbstractCodegenerator
    {
        return new Feedforward($this->model);
    }

    /**
     * @return int
     * @todo default implementation until specific implementations are completed
     */
    public function getBestTrainingId(): int
    {
        if ($this->model->getTrainingTasks()->count() === 0) {
            return 0;
        }
        return $this->model->getTrainingTasks()->last()->getId();
    }

    /**
     * @throws Exception
     */
    public static function getState(Model $model, EntityManagerInterface $entityManager): StateInterface {
        try {
            $type = ModelTypes::from($model->getType());
        } catch (Exception $exception) {
            dd($exception);
        }

        if ($type === ModelTypes::MODEL_TYPE_LOG_REGR) {
            return new LogisticRegressionState($model, $entityManager);
        }
        if ($type === ModelTypes::MODEL_TYPE_DTREE) {
            return new DecisionTreeState($model, $entityManager);
        }
        if ($type === ModelTypes::MODEL_TYPE_NEUR) {
            return new NeuralNetState($model, $entityManager);
        }
        if ($type === ModelTypes::MODEL_TYPE_SVM) {
            return new SvmState($model, $entityManager);
        }
        if ($type === ModelTypes::MODEL_TYPE_RNN) {
            return new RnnState($model, $entityManager);
        }
        if ($type === ModelTypes::MODEL_TYPE_LIN_REGR) {
            return new LinearRegressionState($model, $entityManager);
        }

        throw new Exception("invalid argument");
    }

    abstract public function getArchitectureType(): string;

    abstract public function validArchitecture(): bool;

    abstract protected function clearConfiguration();

    abstract public function setHyperParameter(array $params = []);


    public function validBaseData(): bool {
        if (mb_strlen(trim($this->model->getName())) < "3") {
            return false;
        }
        if (!ModelTypes::tryFrom($this->model->getType())) {
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     * @todo it is no longer to change modeltype after initialization, can remove some of the code
     */
    public function setModeltype(ModelTypes $type): StateInterface
    {
        try {
            $currrentType = ModelTypes::from($this->model->getType());
            if ($currrentType === $type) {
                return $this;
            }
        } catch (Exception $exception) {
            dd($exception);
        }

        $this->clearConfiguration();

        return self::getState($this->model, $this->entityManager);
    }

    public function setName(string $name): void
    {
        $this->model->setName($name)
            ->setUpdatedate(new \DateTime());
    }

    public function setDescription(string $description): void
    {
        $this->model->setDescription($description)
            ->setUpdatedate(new \DateTime());
    }

    public function addLayer(Layer $layer): StateInterface
    {
        throw new Exception("Invalid operation");
    }

    public function removeLayer(Layer $layer)
    {
        throw new Exception("Invalid operation");
    }

    public function setSvmConfiguration(SvmConfiguration $configuration)
    {
        throw new Exception("Invalid operation");
    }

    public function setLogRegConfiguration(LogRegConfiguration $configuration)
    {
        throw new Exception("Invalid operation");
    }

    public function setDecisiontreeConfiguration(DecisiontreeConfiguration $configuration)
    {
        throw new Exception("Invalid operation");
    }

    public function setLinRegConfiguration(LinRegConfiguration $configuration)
    {
        throw new Exception("Invalid operation");
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function validFieldConfiguration(): bool
    {
        $hasTarget = false;
        $hasFeatures = false;
        foreach ($this->model->getFieldConfigurations()->toArray() as $configuration) {
            if ($configuration->isIsTarget()) {
                $hasTarget = true;
            }
            if (!$configuration->isIsTarget() && !$configuration->isIsIgnored()) {
                $hasFeatures = true;
            }
        }
        if (!$hasTarget || !$hasFeatures) {
            return false;
        }

        return true;
    }


    public function addTrainingTask(TrainingTask $task): StateInterface
    {
        $this->model->addTrainingTask($task)
            ->setUpdatedate(new \DateTime());

        return $this;
    }

    public function jsonSerialize(): array
    {
        $model = $this->getModel();
        return [
            ...$model->jsonSerialize(),
            'architectureType' => $this->getArchitectureType(),
            'validBaseData' => $this->validBaseData(),
            'validArchitecture' => $this->validArchitecture(),
            'validFieldConfiguration' => $this->validFieldConfiguration(),
        ];
    }

}