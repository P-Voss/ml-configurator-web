<?php

namespace App\Entity;

use App\Repository\HyperparametersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HyperparametersRepository::class)]
class Hyperparameters
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'hyperparameters', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gradientStrategy = null;

    #[ORM\Column(nullable: true)]
    private ?int $batchsize = null;

    #[ORM\Column]
    private ?int $epochs = null;

    #[ORM\Column]
    private ?bool $earlyStop = null;

    #[ORM\Column]
    private ?int $earlyStopTreshold = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $earlyStopFunction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getGradientStrategy(): ?string
    {
        return $this->gradientStrategy;
    }

    public function setGradientStrategy(?string $gradientStrategy): static
    {
        $this->gradientStrategy = $gradientStrategy;

        return $this;
    }

    public function getBatchsize(): ?int
    {
        return $this->batchsize;
    }

    public function setBatchsize(?int $batchsize): static
    {
        $this->batchsize = $batchsize;

        return $this;
    }

    public function getEpochs(): ?int
    {
        return $this->epochs;
    }

    public function setEpochs(int $epochs): static
    {
        $this->epochs = $epochs;

        return $this;
    }

    public function isEarlyStop(): ?bool
    {
        return $this->earlyStop;
    }

    public function setEarlyStop(bool $earlyStop): static
    {
        $this->earlyStop = $earlyStop;

        return $this;
    }

    public function getEarlyStopTreshold(): ?int
    {
        return $this->earlyStopTreshold;
    }

    public function setEarlyStopTreshold(int $earlyStopTreshold): static
    {
        $this->earlyStopTreshold = $earlyStopTreshold;

        return $this;
    }

    public function getEarlyStopFunction(): ?string
    {
        return $this->earlyStopFunction;
    }

    public function setEarlyStopFunction(?string $earlyStopFunction): static
    {
        $this->earlyStopFunction = $earlyStopFunction;

        return $this;
    }
}
