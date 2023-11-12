<?php

namespace App\Entity;

use App\Repository\FieldConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FieldConfigurationRepository::class)]
class FieldConfiguration implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $isTarget = null;

    #[ORM\Column]
    private ?bool $isIgnored = null;

    #[ORM\ManyToOne(inversedBy: 'fieldConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

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

    public function isIsTarget(): ?bool
    {
        return $this->isTarget;
    }

    public function setIsTarget(bool $isTarget): static
    {
        $this->isTarget = $isTarget;

        return $this;
    }

    public function isIsIgnored(): ?bool
    {
        return $this->isIgnored;
    }

    public function setIsIgnored(bool $isIgnored): static
    {
        $this->isIgnored = $isIgnored;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'isIgnored' => $this->isIgnored,
            'isTarget' => $this->isTarget,
        ];
    }

    public function createCopy(): FieldConfiguration
    {
        $conf = new FieldConfiguration();
        $conf->setType($this->type)
            ->setIsTarget($this->isTarget)
            ->setIsIgnored($this->isIgnored)
            ->setName($this->name);
        return $conf;
    }

}
