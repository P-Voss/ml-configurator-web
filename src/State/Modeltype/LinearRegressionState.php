<?php

namespace App\State\Modeltype;

use App\Enum\ModelTypes;

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

    public function validArchitecture(): bool
    {
        return false;
    }

}