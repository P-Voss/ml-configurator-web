<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Model::class, orphanRemoval: true)]
    private Collection $models;

    #[ORM\OneToMany(mappedBy: 'executedBy', targetEntity: Event::class, orphanRemoval: true)]
    private Collection $events;

    #[ORM\Column]
    private ?bool $isDemoUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastActionDatetime = null;

    public function __construct()
    {
        $this->models = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Model>
     */
    public function getModels(): Collection
    {
        return $this->models;
    }

    public function addModel(Model $model): static
    {
        if (!$this->models->contains($model)) {
            $this->models->add($model);
            $model->setStudent($this);
        }

        return $this;
    }

    public function removeModel(Model $model): static
    {
        if ($this->models->removeElement($model)) {
            // set the owning side to null (unless already changed)
            if ($model->getStudent() === $this) {
                $model->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setExecutedBy($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getExecutedBy() === $this) {
                $event->setExecutedBy(null);
            }
        }

        return $this;
    }

    public function isIsDemoUser(): ?bool
    {
        return $this->isDemoUser;
    }

    public function setIsDemoUser(bool $isDemoUser): static
    {
        $this->isDemoUser = $isDemoUser;

        return $this;
    }

    public function getLastActionDatetime(): ?\DateTimeInterface
    {
        return $this->lastActionDatetime;
    }

    public function setLastActionDatetime(?\DateTimeInterface $lastActionDatetime): static
    {
        $this->lastActionDatetime = $lastActionDatetime;

        return $this;
    }

    public function hoursSinceLastActivity()
    {
        if (!$this->isDemoUser) {
            return 0;
        }
        $currentDatetime = new \DateTime();
        $dateDiff = date_diff($currentDatetime, $this->getLastActionDatetime());

        /**
         * coarse calculation, exact count of hours is not neccessary
         */
        $days = $dateDiff->y * 365 + $dateDiff->m * 30 + $dateDiff->d;
        return $days * 24 + $dateDiff->h;
    }

}
