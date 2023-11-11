<?php

namespace App\State\Modeltype;

use App\CodeGenerator\AbstractCodegenerator;
use App\CodeGenerator\LinearRegression;
use App\Entity\LinRegConfiguration;
use App\Service\TrainingPathGenerator;

class LinearRegressionState extends AbstractState
{

    public function getArchitectureType(): string
    {
        return "LinRegression";
    }


    protected function clearConfiguration(): void
    {
        $configuration = $this->model->getLinRegConfiguration();
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
            'learningRate',
            'maxIterations',
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
        if ((int) ($params['learningRate'] * 100) < 1) {
            throw new \Exception('learningRate too low');
        }
        if ((int) $params['maxIterations'] < 50) {
            throw new \Exception('trainingPercentage too low');
        }

        $hyperParameters = [
            'trainingPercentage' => (int) $params['trainingPercentage'],
            'validationPercentage' => max((int) $params['validationPercentage'], 0),
            'testPercentage' => 100 - (int) $params['trainingPercentage'] - (int) $params['validationPercentage'],
            'learningRate' => (float) $params['learningRate'],
            'maxIterations' => (int) $params['maxIterations'],
            'tolerance' => (float) $params['tolerance'],
        ];
        $this->model->setHyperparameters($hyperParameters)
            ->setUpdatedate(new \DateTime());
    }

    public function setLinRegConfiguration(LinRegConfiguration $configuration)
    {
        if ($configuration->getRegularizationType() === 'none') {
            $configuration->setAlpha(0);
        }
        if ($configuration->getAlpha() < 0) {
            throw new \Exception("alpha can not be negative");
        }
        if ($configuration->getRegularizationType() === 'l1' && $configuration->getAlpha() > 100) {
            throw new \Exception("alpha should be between 0 and 1");
        }

        $currentConfiguration = $this->model->getLinRegConfiguration();
        if ($currentConfiguration) {
            $this->entityManager->remove($currentConfiguration);
            $this->entityManager->flush();
        }
        $this->model->setLinRegConfiguration($configuration);
    }

    public function validArchitecture(): bool
    {
        $configuration = $this->model->getLinRegConfiguration();
        if (!$configuration) {
            return false;
        }

        if ($configuration->getRegularizationType() === 'none') {
            return false;
        }
        if ($configuration->getAlpha() < 0) {
            return false;
        }
        if ($configuration->getRegularizationType() === 'l1' && $configuration->getAlpha() > 100) {
            return false;
        }

        return true;
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

    public function getCodegenerator(): AbstractCodegenerator
    {
        return new LinearRegression($this->model);
    }


    public function getBestTrainingId(): int
    {
        return parent::getBestTrainingId();
    }

}