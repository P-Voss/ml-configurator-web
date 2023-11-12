<?php

namespace App\Entity;

use App\Repository\LogRegConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRegConfigurationRepository::class)]
class LogRegConfiguration implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'logRegConfiguration', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $regularizerType = null;

    #[ORM\Column(length: 255)]
    private ?string $solver = null;

    #[ORM\Column(nullable: true)]
    private ?int $lambda = null;

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

    public function getRegularizerType(): ?string
    {
        return $this->regularizerType;
    }

    public function setRegularizerType(?string $regularizerType): static
    {
        $this->regularizerType = $regularizerType;

        return $this;
    }

    public function getSolver(): ?string
    {
        return $this->solver;
    }

    public function setSolver(string $solver): static
    {
        $this->solver = $solver;

        return $this;
    }

    public function getLambda(): ?int
    {
        return $this->lambda;
    }

    public function setLambda(?int $lambda): static
    {
        $this->lambda = $lambda;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'regularizerType' => $this->regularizerType,
            'solver' => $this->solver,
            'lambda' => $this->lambda,
        ];
    }

    public function createCopy(): LogRegConfiguration
    {
        $conf = new LogRegConfiguration();
        $conf->setLambda($this->lambda)
            ->setRegularizerType($this->regularizerType)
            ->setSolver($this->solver);
        return $conf;
    }

}
