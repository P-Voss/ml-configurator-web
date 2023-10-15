<?php

namespace App\Entity;

use App\Enum\ModelTypes;
use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
class Model implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $lookup = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $scaler = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationdate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedate = null;

    #[ORM\OneToOne(mappedBy: 'model', cascade: ['persist', 'remove'])]
    private ?Hyperparameters $hyperparameters = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Layer::class, orphanRemoval: true)]
    private Collection $layers;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'models')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\OneToOne(mappedBy: 'model', cascade: ['persist', 'remove'])]
    private ?DecisiontreeConfiguration $decisiontreeConfiguration = null;

    #[ORM\OneToOne(mappedBy: 'model', cascade: ['persist', 'remove'])]
    private ?LogRegConfiguration $logRegConfiguration = null;

    #[ORM\OneToOne(mappedBy: 'model', cascade: ['persist', 'remove'])]
    private ?SvmConfiguration $svmConfiguration = null;

    #[ORM\OneToOne(mappedBy: 'model', cascade: ['persist', 'remove'])]
    private ?LinRegConfiguration $linRegConfiguration = null;

    #[ORM\OneToOne(mappedBy: 'model', cascade: ['persist', 'remove'])]
    private ?UploadFile $uploadFile = null;

    public function __construct()
    {
        $this->layers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLookup(): ?string
    {
        return $this->lookup;
    }

    public function setLookup(string $lookup): static
    {
        $this->lookup = $lookup;

        return $this;
    }

    public function getScaler(): ?string
    {
        return $this->scaler;
    }

    public function setScaler(?string $scaler): static
    {
        $this->scaler = $scaler;

        return $this;
    }

    public function getCreationdate(): ?\DateTimeInterface
    {
        return $this->creationdate;
    }

    public function setCreationdate(\DateTimeInterface $creationdate): static
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    public function getUpdatedate(): ?\DateTimeInterface
    {
        return $this->updatedate;
    }

    public function setUpdatedate(?\DateTimeInterface $updatedate): static
    {
        $this->updatedate = $updatedate;

        return $this;
    }

    public function getHyperparameters(): ?Hyperparameters
    {
        return $this->hyperparameters;
    }

    public function setHyperparameters(Hyperparameters $hyperparameters): static
    {
        // set the owning side of the relation if necessary
        if ($hyperparameters->getModel() !== $this) {
            $hyperparameters->setModel($this);
        }

        $this->hyperparameters = $hyperparameters;

        return $this;
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

    /**
     * @return Collection<int, Layer>
     */
    public function getLayers(): Collection
    {
        return $this->layers;
    }

    public function addLayer(Layer $layer): static
    {
        if (!$this->layers->contains($layer)) {
            $this->layers->add($layer);
            $layer->setModel($this);
        }

        return $this;
    }

    public function removeLayer(Layer $layer): static
    {
        if ($this->layers->removeElement($layer)) {
            // set the owning side to null (unless already changed)
            if ($layer->getModel() === $this) {
                $layer->setModel(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(?User $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getDecisiontreeConfiguration(): ?DecisiontreeConfiguration
    {
        return $this->decisiontreeConfiguration;
    }

    public function setDecisiontreeConfiguration(DecisiontreeConfiguration $decisiontreeConfiguration): static
    {
        // set the owning side of the relation if necessary
        if ($decisiontreeConfiguration->getModel() !== $this) {
            $decisiontreeConfiguration->setModel($this);
        }

        $this->decisiontreeConfiguration = $decisiontreeConfiguration;

        return $this;
    }

    public function getLogRegConfiguration(): ?LogRegConfiguration
    {
        return $this->logRegConfiguration;
    }

    public function setLogRegConfiguration(LogRegConfiguration $logRegConfiguration): static
    {
        // set the owning side of the relation if necessary
        if ($logRegConfiguration->getModel() !== $this) {
            $logRegConfiguration->setModel($this);
        }

        $this->logRegConfiguration = $logRegConfiguration;

        return $this;
    }

    public function getSvmConfiguration(): ?SvmConfiguration
    {
        return $this->svmConfiguration;
    }

    public function setSvmConfiguration(SvmConfiguration $svmConfiguration): static
    {
        // set the owning side of the relation if necessary
        if ($svmConfiguration->getModel() !== $this) {
            $svmConfiguration->setModel($this);
        }

        $this->svmConfiguration = $svmConfiguration;

        return $this;
    }

    public function getLinRegConfiguration(): ?LinRegConfiguration
    {
        return $this->linRegConfiguration;
    }

    public function setLinRegConfiguration(LinRegConfiguration $linRegConfiguration): static
    {
        // set the owning side of the relation if necessary
        if ($linRegConfiguration->getModel() !== $this) {
            $linRegConfiguration->setModel($this);
        }

        $this->linRegConfiguration = $linRegConfiguration;

        return $this;
    }

    public function getUploadFile(): ?UploadFile
    {
        return $this->uploadFile;
    }

    public function setUploadFile(UploadFile $uploadFile): static
    {
        // set the owning side of the relation if necessary
        if ($uploadFile->getModel() !== $this) {
            $uploadFile->setModel($this);
        }

        $this->uploadFile = $uploadFile;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'layers' => array_map(function (Layer $layer) {
                return $layer->jsonSerialize();
            }, $this->layers->toArray()),
            'decisiontreeConfiguration' => $this->decisiontreeConfiguration,
            'logRegConfiguration' => $this->logRegConfiguration,
            'linRegConfiguration' => $this->linRegConfiguration,
            'svmConfiguration' => $this->svmConfiguration,
            'uploadFile' => $this->uploadFile,
        ];
    }


}
