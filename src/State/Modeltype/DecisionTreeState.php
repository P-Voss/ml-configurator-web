<?php

namespace App\State\Modeltype;

use App\Entity\DecisiontreeConfiguration;
use App\Enum\ModelTypes;

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
    public function setHyperParameter(array $params = [])
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

        $this->model->setDecisiontreeConfiguration($configuration);
    }

}