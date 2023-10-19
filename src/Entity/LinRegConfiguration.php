<?php

namespace App\Entity;

use App\Repository\LinRegConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinRegConfigurationRepository::class)]
class LinRegConfiguration implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'linRegConfiguration', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $regularizationType = null;

    #[ORM\Column]
    private ?int $alpha = null;

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

    public function getRegularizationType(): ?string
    {
        return $this->regularizationType;
    }

    public function setRegularizationType(?string $regularizationType): static
    {
        $this->regularizationType = $regularizationType;

        return $this;
    }

    public function getAlpha(): ?int
    {
        return $this->alpha;
    }

    public function setAlpha(int $alpha): static
    {
        $this->alpha = $alpha;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'regularizationType' => $this->regularizationType,
            'alpha' => $this->alpha,
        ];
    }

}
