<?php

namespace App\Entity;

use App\Repository\UserStoreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserStoreRepository::class)]
class UserStore {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userStores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userStores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Store $store = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $store_role = null;

    #[ORM\Column(nullable: true)]
    private ?array $permissions = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): static {
        $this->user = $user;

        return $this;
    }

    public function getStore(): ?Store {
        return $this->store;
    }

    public function setStore(?Store $store): static {
        $this->store = $store;

        return $this;
    }

    public function getStoreRole(): ?string {
        return $this->store_role;
    }

    public function setStoreRole(string $store_role): static {
        $this->store_role = $store_role;

        return $this;
    }

    public function getPermissions(): ?array {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): static {
        $this->permissions = $permissions;

        return $this;
    }
}
