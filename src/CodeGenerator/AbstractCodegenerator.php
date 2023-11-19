<?php

namespace App\CodeGenerator;

use App\Entity\Model;
use App\Service\Dataset;
use App\Service\TrainingPathGenerator;

abstract class AbstractCodegenerator
{

    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    abstract public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string;

    abstract public function getExampleScript(): string;

    abstract public function getExampleApplicationScript(): string;

    abstract public function generateApplicationScript(string $sourceFile, string $targetFile): string;

    final protected function getDataPath(TrainingPathGenerator $pathGenerator): string
    {
        $filename = Dataset::getFilename($this->model->getDataset());
        return $pathGenerator->getCsvFile($filename);
    }

    /**
     * @throws \Exception
     */
    protected function getActivationFunction(string $input): string {
        return match ($input) {
            'Relu' => 'relu',
            'relu' => 'relu',
            'Sigmoid' => 'sigmoid',
            'sigmoid' => 'sigmoid',
            'Tanh' => 'tanh',
            'tanh' => 'tanh',
            default => throw new \Exception("invalid input for activation function: " . $input),
        };
    }

    protected function getEarlystopMonitor(string $input): string {
        return match ($input) {
            'MSE' => 'val_mean_squared_error',
            'MAE' => 'val_mean_absolute_error',
            default => throw new \Exception("invalid input for loss function"),
        };
    }

    /**
     * @throws \Exception
     */
    protected function getOptimizer(string $input): string {
        return match ($input) {
            'Adam' => 'Adam',
            'SGD' => 'SGD',
            default => throw new \Exception("invalid input for optimizer"),
        };
    }

    /**
     * @throws \Exception
     */
    protected function getLossFunction(string $input): string {
        return match ($input) {
            'MSE' => 'mean_squared_error',
            'MAE' => 'mean_absolute_error',
            default => throw new \Exception("invalid input for loss function"),
        };
    }

    protected function getTargetName(): string
    {
        foreach ($this->model->getFieldConfigurations() as $fieldConfiguration) {
            if ($fieldConfiguration->isIsTarget()) {
                return $fieldConfiguration->getName();
            }
        }
        return '';
    }

    protected function getFeatures(): array
    {
        $features = [];
        foreach ($this->model->getFieldConfigurations() as $fieldConfiguration) {
            if ($fieldConfiguration->isIsTarget() or $fieldConfiguration->isIsIgnored()) {
                continue;
            }
            $features[] = $fieldConfiguration->getName();
        }
        return $features;
    }

    protected function getTextFeatures()
    {
        $features = [];
        foreach ($this->model->getFieldConfigurations() as $fieldConfiguration) {
            if ($fieldConfiguration->isIsTarget() or $fieldConfiguration->isIsIgnored()) {
                continue;
            }
            if ($fieldConfiguration->getType() !== "text") {
                continue;
            }
            $features[] = $fieldConfiguration->getName();
        }
        return $features;
    }


    protected function getNumericalFeatures()
    {
        $features = [];
        foreach ($this->model->getFieldConfigurations() as $fieldConfiguration) {
            if ($fieldConfiguration->isIsTarget() or $fieldConfiguration->isIsIgnored()) {
                continue;
            }
            if ($fieldConfiguration->getType() === "text") {
                continue;
            }
            $features[] = $fieldConfiguration->getName();
        }
        return $features;
    }

}