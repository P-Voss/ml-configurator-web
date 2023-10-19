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
    }

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
        $this->model->setLogRegConfiguration($configuration);
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