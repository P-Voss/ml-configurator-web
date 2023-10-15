<?php

namespace App\Entity;

use App\Repository\LayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LayerRepository::class)]
class Layer implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $neuronCount = null;

    #[ORM\Column(length: 255)]
    private ?string $activationFunction = null;

    #[ORM\Column]
    private ?int $dropoutQuote = null;

    #[ORM\ManyToOne(inversedBy: 'layers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(nullable: true)]
    private ?bool $returnSequences = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $regularizationType = null;

    #[ORM\Column(nullable: true)]
    private ?int $regularizationLambda = null;

    #[ORM\Column(nullable: true)]
    private ?int $recurrentDropoutRate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNeuronCount(): ?int
    {
        return $this->neuronCount;
    }

    public function setNeuronCount(int $neuronCount): static
    {
        $this->neuronCount = $neuronCount;

        return $this;
    }

    public function getActivationFunction(): ?string
    {
        return $this->activationFunction;
    }

    public function setActivationFunction(string $activationFunction): static
    {
        $this->activationFunction = $activationFunction;

        return $this;
    }

    public function getDropoutQuote(): ?int
    {
        return $this->dropoutQuote;
    }

    public function setDropoutQuote(int $dropoutQuote): static
    {
        $this->dropoutQuote = $dropoutQuote;

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(?Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function isReturnSequences(): ?bool
    {
        return $this->returnSequences;
    }

    public function setReturnSequences(?bool $returnSequences): static
    {
        $this->returnSequences = $returnSequences;

        return $this;
    }

    public function getRegularizationType(): ?string
    {
        return $this->regularizationType;
    }

    public function setRegularizationType(?string $regularizationType): static
    {
        $this->regularizationType = $regularizationType;

        return $this;
    }

    public function getRegularizationLambda(): ?int
    {
        return $this->regularizationLambda;
    }

    public function setRegularizationLambda(?int $regularizationLambda): static
    {
        $this->regularizationLambda = $regularizationLambda;

        return $this;
    }

    public function getRecurrentDropoutRate(): ?int
    {
        return $this->recurrentDropoutRate;
    }

    public function setRecurrentDropoutRate(?int $recurrentDropoutRate): static
    {
        $this->recurrentDropoutRate = $recurrentDropoutRate;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'neurons' => $this->neuronCount,
            'activationFunction' => $this->activationFunction,
            'dropoutQuote' => $this->dropoutQuote / 100,
            'recurrentDropoutRate' => $this->recurrentDropoutRate / 100,
            'regularizationType' => $this->regularizationType,
            'regularizationLambda' => $this->regularizationLambda / 100,
        ];
    }


}
