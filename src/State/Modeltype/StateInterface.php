<?php

namespace App\State\Modeltype;

use App\CodeGenerator\AbstractCodegenerator;
use App\Entity\DecisiontreeConfiguration;
use App\Entity\Layer;
use App\Entity\LinRegConfiguration;
use App\Entity\LogRegConfiguration;
use App\Entity\Model;
use App\Entity\SvmConfiguration;
use App\Entity\TrainingTask;
use App\Enum\ModelTypes;
use App\Service\TrainingPathGenerator;

interface StateInterface extends \JsonSerializable, \SplSubject
{


    public function delete();

    public function setModeltype(ModelTypes $type): StateInterface;

    public function getCodegenerator(): AbstractCodegenerator;

    public function getArchitectureType(): string;

    public function getBestTrainingId(): int;


    public function setName(string $name);


    public function setDescription(string $description);

    public function addLayer(Layer $layer): StateInterface;

    public function removeLayer(Layer $layer);

    public function setSvmConfiguration(SvmConfiguration $configuration);

    public function setLogRegConfiguration(LogRegConfiguration $configuration);

    public function setDecisiontreeConfiguration(DecisiontreeConfiguration $configuration);

    public function setLinRegConfiguration(LinRegConfiguration $configuration);

    public function setHyperParameter(array $params = []);

    public function getModel(): Model;

    public function validBaseData(): bool;

    public function validArchitecture(): bool;

    public function validFieldConfiguration(): bool;

    public function validTraining(): bool;

    public function addTrainingTask(TrainingTask $task): StateInterface;

    public function setModelFile(TrainingPathGenerator $pathGenerator): StateInterface;

    public function setCheckpointFile(TrainingPathGenerator $pathGenerator): StateInterface;

    public function setScalerFile(TrainingPathGenerator $pathGenerator): StateInterface;

}