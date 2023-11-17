<?php

namespace App\State\Modeltype;

use App\CodeGenerator\AbstractCodegenerator;
use App\CodeGenerator\DecisionTree;
use App\Entity\DecisiontreeConfiguration;
use App\Entity\TrainingTask;
use App\Service\TrainingPathGenerator;

class DecisionTreeState extends AbstractState
{

    public function getArchitectureType(): string
    {
        return "DTREE";
    }

    protected function clearConfiguration(): void
    {
        $configuration = $this->model->getDecisiontreeConfiguration();
        if ($configuration) {
            $this->entityManager->remove($configuration);
        }
    }

    /**
     * @param array $params
     * @return void
     * @throws \Exception
     */
    public function setHyperParameter(array $params = []): void
    {
        if ($params['id']) {
            unset($params['id']);
        }
        $requiredParameters = [
            'trainingPercentage',
            'validationPercentage',
            'splitter',
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
            'splitter' => !in_array($params['splitter'], ['best', 'random']) ? 'best' : $params['splitter'],
        ];
        $this->model->setHyperparameters($hyperParameters)
            ->setUpdatedate(new \DateTime());
    }

    public function validArchitecture(): bool
    {
        $configuration = $this->model->getDecisiontreeConfiguration();
        if (!$configuration) {
            return false;
        }
        if ($configuration->getMaxDepth() < 1) {
            return false;
        }
        if ($configuration->getMaxFeatures() < 1) {
            return false;
        }
        if ($configuration->getMinSamplesLeaf() < 1) {
            return false;
        }
        if ($configuration->getMinSampleSplit() < 1) {
            return false;
        }
        if (!in_array($configuration->getMissingValueHandling(), ['mean', 'median', 'drop'])) {
            return false;
        }
        if (!in_array($configuration->getQualityMeasure(), ['gini', 'entropy'])) {
            return false;
        }

        return true;
    }

    public function setDecisiontreeConfiguration(DecisiontreeConfiguration $configuration): void
    {
        if ($configuration->getMaxDepth() < 1) {
            throw new \Exception("invalid argument: max depth");
        }
        if ($configuration->getMaxFeatures() < 1) {
            throw new \Exception("invalid argument: max features");
        }
        if ($configuration->getMinSamplesLeaf() < 1) {
            throw new \Exception("invalid argument: min samples per leaf");
        }
        if ($configuration->getMinSampleSplit() < 1) {
            throw new \Exception("invalid argument: min samples split");
        }
        if (!in_array($configuration->getMissingValueHandling(), ['mean', 'median', 'drop'])) {
            throw new \Exception("invalid argument: missing value handling");
        }
        if (!in_array($configuration->getQualityMeasure(), ['gini', 'entropy'])) {
            throw new \Exception("invalid argument: quality measure");
        }
        $currentConfiguration = $this->model->getDecisiontreeConfiguration();
        if ($currentConfiguration) {
            $this->entityManager->remove($currentConfiguration);
            $this->entityManager->flush();
        }
        $this->model->setDecisiontreeConfiguration($configuration)
            ->setUpdatedate(new \DateTime());
    }

    public function getCodegenerator(): AbstractCodegenerator
    {
        return new DecisionTree($this->model);
    }

    /**
     * @return int
     * @todo prioritize accuracy of test-split over validation-split when available
     */
    public function getBestTrainingId(): int
    {
        if ($this->model->getTrainingTasks()->count() === 0) {
            return 0;
        }
        $bestId = 0;
        $highestAccuracy = 0;
        foreach ($this->model->getTrainingTasks() as $task) {
            if ($task->getState() !== TrainingTask::STATE_COMPLETED || !$task->getReportPath()) {
                continue;
            }
            if (!file_exists($task->getReportPath())) {
                continue;
            }
            $report = json_decode(file_get_contents($task->getReportPath()));
            if (!$report) {
                continue;
            }
            if (!isset($report->accuracy_val)) {
                continue;
            }
            if ($report->accuracy_val > $highestAccuracy) {
                $highestAccuracy = $report->accuracy_val;
                $bestId = $task->getId();
            }
        }

        return $bestId;
    }


    public function setModelFile(TrainingPathGenerator $pathGenerator): StateInterface
    {
        $this->model->setModelPath($pathGenerator->getModelFile('joblib'))
            ->setUpdatedate(new \DateTime());

        return $this;
    }

    public function setCheckpointFile(TrainingPathGenerator $pathGenerator): StateInterface
    {
        return $this;
    }

    public function setScalerFile(TrainingPathGenerator $pathGenerator): StateInterface
    {
        return $this;
    }
}