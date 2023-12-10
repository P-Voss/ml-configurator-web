<?php

namespace App\State\Modeltype;

use App\CodeGenerator\AbstractCodegenerator;
use App\CodeGenerator\Rnn;
use App\Entity\Layer;
use App\Enum\LayerTypes;
use App\Service\TrainingPathGenerator;

class RnnState extends AbstractState
{

    /**
     * @throws \Exception
     * Can not add Dropout as first layer
     * Can only add GRU and LSTM as first layer, following layers need to be dropout or dense
     * Invalid GRU or LSTM layer for later layers will be turned into dense layer
     */
    public function addLayer(Layer $layer): StateInterface
    {
        $type = LayerTypes::from($layer->getType());
        if ($this->model->getLayers()->count() === 0 and $type === LayerTypes::LAYER_TYPE_DROPOUT) {
            throw new \Exception("can not set Dropout as first layer");
        }
        if ($this->model->getLayers()->count() === 0) {
            $this->entityManager->persist($layer);
            $this->model->addLayer($layer);

            return $this;
        }

        if ($type === LayerTypes::LAYER_TYPE_LSTM || $type === LayerTypes::LAYER_TYPE_GRU) {
            $denseLayer = new Layer();
            $denseLayer->setRegularizationType($layer->getRegularizationType())
                ->setReturnSequences(false)
                ->setType(LayerTypes::LAYER_TYPE_DENSE->value)
                ->setNeuronCount($layer->getNeuronCount())
                ->setDropoutQuote($layer->getDropoutQuote())
                ->setRecurrentDropoutRate(0)
                ->setRegularizationLambda($layer->getRegularizationLambda())
                ->setActivationFunction($layer->getActivationFunction());

            $this->entityManager->persist($denseLayer);
            $this->model->addLayer($denseLayer);
            return $this;
        }
        $this->entityManager->persist($layer);
        $this->model->addLayer($layer);

        return $this;
    }

    /**
     * @param Layer $layer
     * @return void
     * deletes all layers behind the layer to delete to prevent invalid layer configurations
     */
    public function removeLayer(Layer $layer)
    {
        foreach ($this->model->getLayers() as $existingLayer) {
            if ($existingLayer->getId() >= $layer->getId()) {
                $this->model->removeLayer($existingLayer);
            }
        }
    }

    public function getArchitectureType(): string
    {
        return "RNN";
    }

    protected function clearConfiguration(): void
    {
        foreach ($this->model->getLayers() as $layer) {
            $this->model->removeLayer($layer);
            $this->entityManager->remove($layer);
        }
    }

    /**
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
            'bidirectional',
            'sequenceLength',
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
            'sequenceLength' => max((int) $params['patience'], 0),
            'bidirectional' => $params['bidirectional'] === "true" || $params['bidirectional'] === true,
        ];
        $this->model->setHyperparameters($hyperParameters)
            ->setUpdatedate(new \DateTime());
    }

    /**
     * @return bool
     * @todo einzelne Layervalidierung abfragen
     * @todo Übergang Sequenz zu Dense abfragen
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

    public function getCodegenerator(): AbstractCodegenerator
    {
        return new Rnn($this->model);
    }

    public function setModelFile(TrainingPathGenerator $pathGenerator): StateInterface
    {
        $this->model->setModelPath($pathGenerator->getModelFile('h5'))
            ->setUpdatedate(new \DateTime());

        return $this;
    }

    public function setCheckpointFile(TrainingPathGenerator $pathGenerator): StateInterface
    {
        $this->model->setCheckpointPath($pathGenerator->getCheckpointFile('h5'))
            ->setUpdatedate(new \DateTime());

        return $this;
    }

    public function setScalerFile(TrainingPathGenerator $pathGenerator): StateInterface
    {
        $this->model->setScalerPath($pathGenerator->getScalerFile('pkl'))
            ->setUpdatedate(new \DateTime());

        return $this;
    }

    public function validTraining(): bool
    {
        if (!$this->model->getScalerPath()) {
            return false;
        }
        if (!file_exists($this->model->getScalerPath())) {
            return false;
        }
        if (!$this->model->getCheckpointPath()) {
            return false;
        }
        if (!file_exists($this->model->getCheckpointPath())) {
            return false;
        }
        if (!$this->model->getEncoderPath()) {
            return false;
        }
        if (!file_exists($this->model->getEncoderPath())) {
            return false;
        }
        return parent::validTraining();
    }

}