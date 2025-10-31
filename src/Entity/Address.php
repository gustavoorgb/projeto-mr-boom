<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\SoftDeleteable;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Address {
    use TimestampableEntity;
    use SoftDeleteableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 20)]
    private ?string $zip_code = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Store $store = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getStreet(): ?string {
        return $this->street;
    }

    public function setStreet(string $street): static {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string {
        return $this->number;
    }

    public function setNumber(?string $number): static {
        $this->number = $number;

        return $this;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(string $city): static {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string {
        return $this->zip_code;
    }

    public function setZipCode(string $zip_code): static {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getStoreId(): ?Store {
        return $this->store;
    }

    public function setStoreId(?Store $store): static {
        $this->store = $store;

        return $this;
    }
}
