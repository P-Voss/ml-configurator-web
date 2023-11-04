<?php

namespace App\CodeGenerator;

use App\Entity\Model;
use App\Service\TrainingPathGenerator;

abstract class AbstractCodegenerator
{

    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    abstract public function generateTrainingScript(TrainingPathGenerator $pathGenerator): string;

    /**
     * @return string
     * @todo only for dev purposes, making function abstract when implementing in child classes
     */
    public function getExampleScript(): string
    {
        return "";
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

}