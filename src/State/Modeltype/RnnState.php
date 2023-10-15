<?php

namespace App\State\Modeltype;

use App\Entity\Layer;
use App\Enum\LayerTypes;

class RnnState extends AbstractState
{

    /**
     * @throws \Exception
     */
    public function addLayer(Layer $layer): void
    {
        $type = LayerTypes::from($layer->getType());
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
        return "RNN";
    }

    protected function clearConfiguration(): void
    {
        foreach ($this->model->getLayers() as $layer) {
            $this->model->removeLayer($layer);
        }
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

}