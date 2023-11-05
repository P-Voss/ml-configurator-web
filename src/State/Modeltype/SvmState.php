<?php

namespace App\State\Modeltype;

use App\CodeGenerator\AbstractCodegenerator;
use App\CodeGenerator\Svm;
use App\Entity\SvmConfiguration;
use App\Entity\TrainingTask;

class SvmState extends AbstractState
{

    public function getArchitectureType(): string
    {
        return "SVM";
    }

    protected function clearConfiguration(): void
    {
        $configuration = $this->model->getSvmConfiguration();
        if ($configuration) {
            $this->entityManager->remove($configuration);
        }
    }

    /**
     * @param array $params
     * @return void
     * @throws \Exception
     */
    public function setHyperParameter(array $params = [])
    {
        if ($params['id']) {
            unset($params['id']);
        }
        $requiredParameters = [
            'trainingPercentage',
            'validationPercentage',
            'shrinking',
            'tolerance',
        ];
        $keys = array_keys($params);
        foreach ($requiredParameters as $requiredParameter) {
            if (!in_array($requiredParameter, $keys)) {
                throw new \Exception('Missing required parameter: ' . $requiredParameter);
            }
        }
        if ((int) $params['trainingPercentage'] < 50 || (int) $params['trainingPercentage'] > 100) {
            throw new \Exception('trainingPercentage too low');
        }
        if (((int) $params['trainingPercentage'] + (int) $params['validationPercentage']) > 100) {
            throw new \Exception('trainingPercentage too high');
        }

        $hyperParameters = [
            'trainingPercentage' => (int) $params['trainingPercentage'],
            'validationPercentage' => max((int) $params['validationPercentage'], 0),
            'testPercentage' => 100 - (int) $params['trainingPercentage'] - (int) $params['validationPercentage'],
            'shrinking' => $params['shrinking'] === "true" || $params['shrinking'] === true,
            'tolerance' => (float) $params['tolerance'],
        ];
        $this->model->setHyperparameters($hyperParameters)
            ->setUpdatedate(new \DateTime());
    }

    public function validArchitecture(): bool
    {
        $configuration = $this->model->getSvmConfiguration();
        if (!$configuration) {
            return false;
        }
        if (!in_array($configuration->getKernel(), ['linear', 'poly', 'rbf', 'sigmoid'])) {
            return false;
        }
        if ($configuration->getC() < 0) {
            return false;
        }
        if ($configuration->getC() > 100000) {
            return false;
        }
//        if ($configuration->getDegree() < 1) {
//            return false;
//        }

        return true;
    }

    /**
     * @throws \Exception
     */
    public function setSvmConfiguration(SvmConfiguration $configuration): void
    {
        if (!in_array($configuration->getKernel(), ['linear', 'poly', 'rbf', 'sigmoid'])) {
            throw new \Exception("invalid argument: Kernel");
        }
        $currentConfiguration = $this->model->getSvmConfiguration();
        if ($currentConfiguration) {
            $this->entityManager->remove($currentConfiguration);
            $this->entityManager->flush();
        }
        $this->model->setSvmConfiguration($configuration)
            ->setUpdatedate(new \DateTime());
    }

    public function getCodegenerator(): AbstractCodegenerator
    {
        return new Svm($this->model);
    }

    public function getBestTrainingId(): int
    {
        if ($this->model->getTrainingTasks()->count() === 0) {
            return 0;
        }
        $bestId = 0;
        $lowestScore = PHP_INT_MAX;
        foreach ($this->model->getTrainingTasks() as $task) {
            if ($task->getState() !== TrainingTask::STATE_COMPLETED || !$task->getReportPath()) {
                continue;
            }
            if (!file_exists($task->getReportPath())) {
                continue;
            }
            $report = json_decode(file_get_contents($task->getReportPath()));
            if ($report->mse < $lowestScore) {
                $lowestScore = $report->mse;
                $bestId = $task->getId();
            }
        }

        return $bestId;
    }

}