<?php

namespace App\Entity\Gwatch;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'user_id')]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    #[ORM\Column(length: 88)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $mail = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\Column(name: 'created_at')]
    private ?int $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: ModuleTracking::class)]
    private Collection $modules;

    public function __construct()
    {
        $this->createdAt = time();
        $this->modules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, ModuleTracking>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(ModuleTracking $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
            $module->setOwner($this);
        }

        return $this;
    }

    public function removeModule(ModuleTracking $ModuleTracking): static
    {
        if ($this->modules->removeElement($ModuleTracking)) {
            // set the owning side to null (unless already changed)
            if ($ModuleTracking->getOwner() === $this) {
                $ModuleTracking->setOwner(null);
            }
        }

        return $this;
    }
} 