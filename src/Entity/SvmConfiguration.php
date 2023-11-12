<?php

namespace App\Entity;

use App\Repository\SvmConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SvmConfigurationRepository::class)]
class SvmConfiguration implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'kernel', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Model $model = null;

    #[ORM\Column(length: 20)]
    private ?string $kernel = null;

    #[ORM\Column]
    private ?int $c = null;

    #[ORM\Column]
    private ?int $degree = null;

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

    public function getKernel(): ?string
    {
        return $this->kernel;
    }

    public function setKernel(string $kernel): static
    {
        $this->kernel = $kernel;

        return $this;
    }

    public function getC(): ?int
    {
        return $this->c;
    }

    public function setC(int $c): static
    {
        $this->c = $c;

        return $this;
    }

    public function getDegree(): ?int
    {
        return $this->degree;
    }

    public function setDegree(int $degree): static
    {
        $this->degree = $degree;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'kernel' => $this->kernel,
            'c' => $this->c / 100,
            'degree' => $this->degree,
        ];
    }

    public function createCopy(): SvmConfiguration
    {
        $conf = new SvmConfiguration();
        $conf->setC($this->c)
            ->setDegree($this->degree)
            ->setKernel($this->kernel);
        return $conf;
    }

}
