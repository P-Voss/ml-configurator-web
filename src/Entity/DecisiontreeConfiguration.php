<?php

namespace App\Entity;

use App\Repository\DecisiontreeConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DecisiontreeConfigurationRepository::class)]
class DecisiontreeConfiguration implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'decisiontreeConfiguration', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column]
    private ?int $maxDepth = null;

    #[ORM\Column]
    private ?int $minSampleSplit = null;

    #[ORM\Column]
    private ?int $maxFeatures = null;

    #[ORM\Column]
    private ?int $minSamplesLeaf = null;

    #[ORM\Column(length: 255)]
    private ?string $missingValueHandling = null;

    #[ORM\Column(length: 255)]
    private ?string $qualityMeasure = null;

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

    public function getMaxDepth(): ?int
    {
        return $this->maxDepth;
    }

    public function setMaxDepth(int $maxDepth): static
    {
        $this->maxDepth = $maxDepth;

        return $this;
    }

    public function getMinSampleSplit(): ?int
    {
        return $this->minSampleSplit;
    }

    public function setMinSampleSplit(int $minSampleSplit): static
    {
        $this->minSampleSplit = $minSampleSplit;

        return $this;
    }

    public function getMaxFeatures(): ?int
    {
        return $this->maxFeatures;
    }

    public function setMaxFeatures(int $maxFeatures): static
    {
        $this->maxFeatures = $maxFeatures;

        return $this;
    }

    public function getMinSamplesLeaf(): ?int
    {
        return $this->minSamplesLeaf;
    }

    public function setMinSamplesLeaf(int $minSamplesLeaf): static
    {
        $this->minSamplesLeaf = $minSamplesLeaf;

        return $this;
    }

    public function getMissingValueHandling(): ?string
    {
        return $this->missingValueHandling;
    }

    public function setMissingValueHandling(string $missingValueHandling): static
    {
        $this->missingValueHandling = $missingValueHandling;

        return $this;
    }

    public function getQualityMeasure(): ?string
    {
        return $this->qualityMeasure;
    }

    public function setQualityMeasure(string $qualityMeasure): static
    {
        $this->qualityMeasure = $qualityMeasure;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'maxDepth' => $this->maxDepth,
            'maxFeatures' => $this->maxFeatures,
            'minSamplesLeaf' => $this->minSamplesLeaf,
            'minSampleSplit' => $this->minSampleSplit,
            'missingValueHandling' => $this->missingValueHandling,
            'qualityMeasure' => $this->qualityMeasure,
        ];
    }

    public function createCopy(): DecisiontreeConfiguration
    {
        $conf = new DecisiontreeConfiguration();
        $conf->setQualityMeasure($this->qualityMeasure)
            ->setMissingValueHandling($this->missingValueHandling)
            ->setMinSamplesLeaf($this->minSamplesLeaf)
            ->setMinSampleSplit($this->minSampleSplit)
            ->setMaxDepth($this->maxDepth)
            ->setMaxFeatures($this->maxFeatures);
        return $conf;
    }


}
