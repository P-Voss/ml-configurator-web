<?php

namespace App\State\Modeltype;

use App\Entity\DecisiontreeConfiguration;
use App\Entity\Layer;
use App\Entity\LinRegConfiguration;
use App\Entity\LogRegConfiguration;
use App\Entity\Model;
use App\Entity\SvmConfiguration;
use App\Enum\ModelTypes;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

abstract class AbstractState implements StateInterface
{

    protected Model $model;
    protected readonly EntityManagerInterface $entityManager;

    public function __construct(Model $model, EntityManagerInterface $entityManager)
    {
        $this->model = $model;
        $this->entityManager = $entityManager;
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

    public function setName(string $name)
    {
        $this->model->setName($name)
            ->setUpdatedate(new \DateTime());
    }

    public function setDescription(string $description)
    {
        $this->model->setDescription($description)
            ->setUpdatedate(new \DateTime());
    }

    public function addLayer(Layer $layer)
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

    public function jsonSerialize(): array
    {
        $model = $this->getModel();
        return [
            ...$model->jsonSerialize(),
            'architectureType' => $this->getArchitectureType(),
            'validBaseData' => $this->validBaseData(),
            'validArchitecture' => $this->validArchitecture(),
        ];
    }

}