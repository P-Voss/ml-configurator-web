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
     */
    public function addLayer(Layer $layer): StateInterface
    {
        $type = LayerTypes::from($layer->getType());
        if ($this->model->getLayers()->count() === 0 and $type === LayerTypes::LAYER_TYPE_DROPOUT) {
            throw new \Exception("can not set Dropout as first layer");
        }

        $this->model->addLayer($layer);
        return $this;
    }

    public function removeLayer(Layer $layer)
    {
        $this->model->removeLayer($layer);
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

}