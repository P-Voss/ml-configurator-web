<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $executiondate = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $executedBy = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $executedOn = null;

    #[ORM\Column(nullable: true)]
    private ?array $content = null;

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

    public function getExecutiondate(): ?\DateTimeInterface
    {
        return $this->executiondate;
    }

    public function setExecutiondate(\DateTimeInterface $executiondate): static
    {
        $this->executiondate = $executiondate;

        return $this;
    }

    public function getExecutedBy(): ?User
    {
        return $this->executedBy;
    }

    public function setExecutedBy(?User $executedBy): static
    {
        $this->executedBy = $executedBy;

        return $this;
    }

    public function getExecutedOn(): ?Model
    {
        return $this->executedOn;
    }

    public function setExecutedOn(?Model $executedOn): static
    {
        $this->executedOn = $executedOn;

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): static
    {
        $this->content = $content;

        return $this;
    }
}
