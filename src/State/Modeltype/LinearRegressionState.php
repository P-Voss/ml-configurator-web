<?php

namespace App\State\Modeltype;

use App\Entity\LinRegConfiguration;

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

}