<?php

namespace App\State\Modeltype;

use App\Enum\ModelTypes;

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

    public function validArchitecture(): bool
    {
        return false;
    }

}