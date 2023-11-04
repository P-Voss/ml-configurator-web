<?php

namespace App\Entity;

use App\Repository\TrainingTaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrainingTaskRepository::class)]
class TrainingTask implements \JsonSerializable
{

    const STATE_OPEN = "STATE_OPEN";
    const STATE_PROGRESS = "STATE_PROGRESS";
    const STATE_COMPLETED = "STATE_COMPLETED";
    const STATE_ERROR = "STATE_ERROR";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'trainingTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(length: 1000)]
    private ?string $scriptPath = null;

    #[ORM\Column(length: 1000)]
    private ?string $reportPath = null;

    #[ORM\Column(length: 20)]
    private ?string $state = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDatetime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDatetime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endDatetime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $encodedModel = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $modelHash = null;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $logPath = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getScriptPath(): ?string
    {
        return $this->scriptPath;
    }

    public function setScriptPath(string $scriptPath): static
    {
        $this->scriptPath = $scriptPath;

        return $this;
    }

    public function getReportPath(): ?string
    {
        return $this->reportPath;
    }

    public function setReportPath(string $reportPath): static
    {
        $this->reportPath = $reportPath;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCreationDatetime(): ?\DateTimeInterface
    {
        return $this->creationDatetime;
    }

    public function setCreationDatetime(\DateTimeInterface $creationDatetime): static
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    public function getStartDatetime(): ?\DateTimeInterface
    {
        return $this->startDatetime;
    }

    public function setStartDatetime(?\DateTimeInterface $startDatetime): static
    {
        $this->startDatetime = $startDatetime;

        return $this;
    }

    public function getEndDatetime(): ?\DateTimeInterface
    {
        return $this->endDatetime;
    }

    public function setEndDatetime(?\DateTimeInterface $endDatetime): static
    {
        $this->endDatetime = $endDatetime;

        return $this;
    }

    public function getEncodedModel(): ?string
    {
        return $this->encodedModel;
    }

    public function setEncodedModel(?string $encodedModel): static
    {
        $this->encodedModel = $encodedModel;

        return $this;
    }

    public function getModelHash(): ?string
    {
        return $this->modelHash;
    }

    public function setModelHash(?string $modelHash): static
    {
        $this->modelHash = $modelHash;

        return $this;
    }

    public function getLogPath(): ?string
    {
        return $this->logPath;
    }

    public function setLogPath(?string $logPath): static
    {
        $this->logPath = $logPath;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $endDatetime = '';
        if ($this->endDatetime) {
            $endDatetime = $this->endDatetime->format('Y-m-d H:i:s');
        }
        $startDatetime = '';
        if ($this->startDatetime) {
            $startDatetime = $this->startDatetime->format('Y-m-d H:i:s');
        }
        $creationDatetime = '';
        if ($this->creationDatetime) {
            $creationDatetime = $this->creationDatetime->format('Y-m-d H:i:s');
        }
        $result = [];
        if ($this->reportPath) {
            $result = json_decode(file_get_contents($this->reportPath));
        }

        return [
            'id' => $this->id,
            'hasLog' => $this->logPath !== null,
            'state' => $this->state,
            'creationDatetime' => $creationDatetime,
            'startDatetime' => $startDatetime,
            'endDatetime' => $endDatetime,
            'result' => $result
        ];
    }


}
