<?php

namespace App\State\Modeltype;

use App\Entity\DecisiontreeConfiguration;
use App\Entity\Layer;
use App\Entity\LinRegConfiguration;
use App\Entity\LogRegConfiguration;
use App\Entity\Model;
use App\Entity\SvmConfiguration;
use App\Enum\ModelTypes;

interface StateInterface extends \JsonSerializable
{

    public function setModeltype(ModelTypes $type): StateInterface;


    public function getArchitectureType(): string;


    public function setName(string $name);


    public function setDescription(string $description);

    public function addLayer(Layer $layer);

    public function removeLayer(Layer $layer);

    public function setSvmConfiguration(SvmConfiguration $configuration);

    public function setLogRegConfiguration(LogRegConfiguration $configuration);

    public function setDecisiontreeConfiguration(DecisiontreeConfiguration $configuration);

    public function setLinRegConfiguration(LinRegConfiguration $configuration);

    public function getModel(): Model;

    public function validBaseData(): bool;

    public function validArchitecture(): bool;

}