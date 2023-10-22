<?php

namespace App\State\Modeltype;


use App\Entity\Layer;
use App\Enum\LayerTypes;
use App\Enum\ModelTypes;

class NeuralNetState extends AbstractState
{

    /**
     * @throws \Exception
     */
    public function addLayer(Layer $layer): void
    {
        $type = LayerTypes::from($layer->getType());
        if ($type === LayerTypes::LAYER_TYPE_LSTM || $type === LayerTypes::LAYER_TYPE_GRU) {
            throw new \Exception("invalid layer type for this model");
        }

        if ($this->model->getLayers()->count() === 0 and $type === LayerTypes::LAYER_TYPE_DROPOUT) {
            throw new \Exception("can not set Dropout as first layer");
        }

        $this->model->addLayer($layer);
    }

    public function removeLayer(Layer $layer)
    {
        $this->model->removeLayer($layer);
    }

    public function getArchitectureType(): string
    {
        return "FNN";
    }

    protected function clearConfiguration(): void
    {
        foreach ($this->model->getLayers() as $layer) {
            $this->model->removeLayer($layer);
        }
    }

    public function setHyperParameter(array $params = [])
    {
        if ($params['id']) {
            unset($params['id']);
        }
        $requiredParameters = [
            'trainingPercentage',
            'validationPercentage',
            'learningRate',
            'optimizer',
            'batchSize',
            'epochs',
            'costFunction',
            'momentum',
            'patience',
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

        $hyperParameters = [
            'trainingPercentage' => (int) $params['trainingPercentage'],
            'validationPercentage' => max((int) $params['validationPercentage'], 0),
            'testPercentage' => 100 - (int) $params['trainingPercentage'] - (int) $params['validationPercentage'],
            'learningRate' => max((float) $params['learningRate'], 0.01),
            'optimizer' => !in_array($params['optimizer'], ['SGD', 'Adam', 'RMSprop']) ? 'SGD' : $params['optimizer'],
            'batchSize' => max((int) $params['batchSize'], 1),
            'epochs' => max((int) $params['epochs'], 1),
            'costFunction' => !in_array($params['costFunction'], ['MSE', 'MAE']) ? 'MSE' : $params['costFunction'],
            'momentum' => max((float) $params['momentum'], 0),
            'patience' => max((int) $params['patience'], 0),
        ];
        $this->model->setHyperparameters($hyperParameters)
            ->setUpdatedate(new \DateTime());
    }

    /**
     * @return bool
     * @todo einzelne Layervalidierung abfragen
     */
    public function validArchitecture(): bool
    {
        if ($this->model->getLayers()->count() < 1) {
            return false;
        }
        if ($this->model->getLayers()->first()->getType() === LayerTypes::LAYER_TYPE_DROPOUT) {
            return false;
        }

        return true;
    }


}