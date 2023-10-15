<?php

namespace App\State\Modeltype;

use App\Entity\SvmConfiguration;

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
        $this->model->setSvmConfiguration($configuration);
    }

}