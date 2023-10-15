<?php

namespace App\Entity;

use App\Repository\UploadFileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UploadFileRepository::class)]
class UploadFile implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $hash = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $uploadDate = null;

    #[ORM\Column]
    private ?int $entryCount = null;

    #[ORM\Column(type: Types::BLOB, nullable: true)]
    private $content = null;

    #[ORM\Column]
    private ?bool $containsHeader = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $header = null;

    #[ORM\OneToOne(inversedBy: 'uploadFile', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $fieldConfigurations = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getUploadDate(): ?\DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(\DateTimeInterface $uploadDate): static
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getEntryCount(): ?int
    {
        return $this->entryCount;
    }

    public function setEntryCount(int $entryCount): static
    {
        $this->entryCount = $entryCount;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isContainsHeader(): ?bool
    {
        return $this->containsHeader;
    }

    public function setContainsHeader(bool $containsHeader): static
    {
        $this->containsHeader = $containsHeader;

        return $this;
    }

    public function getHeader(): ?string
    {
        return $this->header;
    }

    public function setHeader(?string $header): static
    {
        $this->header = $header;

        return $this;
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

    public function getFieldConfigurations(): ?string
    {
        return $this->fieldConfigurations;
    }

    public function setFieldConfigurations(?string $fieldConfigurations): static
    {
        $this->fieldConfigurations = $fieldConfigurations;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'filename' => $this->filename,
            'uploadDate' => $this->uploadDate->format('d.m.Y H:i:s'),
            'containsHeader' => $this->containsHeader,
            'header' => json_decode($this->header),
            'fieldConfigurations' => json_decode($this->fieldConfigurations) ?? '[]',
            'entryCount' => $this->entryCount,
            'hash' => $this->hash,
        ];
    }


}
