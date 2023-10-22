<?php

namespace App\State\Modeltype;

use App\Entity\LogRegConfiguration;

class LogisticRegressionState extends AbstractState
{

    public function getArchitectureType(): string
    {
        return "LogRegression";
    }


    protected function clearConfiguration(): void
    {
        $configuration = $this->model->getLogRegConfiguration();
        if ($configuration) {
            $this->entityManager->remove($configuration);
        }
        $this->model->setHyperparameters([])
            ->setUpdatedate(new \DateTime());
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

    /**
     * @throws \Exception
     */
    public function setLogRegConfiguration(LogRegConfiguration $configuration)
    {
        if ($configuration->getRegularizerType() !== 'none' && !($configuration->getLambda() > 0)) {
            throw new \Exception('need to set regularization factor');
        }
        $currentConfiguration = $this->model->getLogRegConfiguration();
        if ($currentConfiguration) {
            $this->entityManager->remove($currentConfiguration);
            $this->entityManager->flush();
        }
        $this->model->setLogRegConfiguration($configuration)
            ->setUpdatedate(new \DateTime());
    }

    public function validArchitecture(): bool
    {
        $configuration = $this->model->getLogRegConfiguration();
        if (!$configuration) {
            return false;
        }

        if ($configuration->getRegularizerType() !== 'none' && !($configuration->getLambda() > 0)) {
            return false;
        }
        return true;
    }

}