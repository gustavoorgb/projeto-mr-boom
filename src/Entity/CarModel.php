<?php

namespace App\Entity;

use App\Repository\CarModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarModelRepository::class)]
class CarModel {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carModels')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CarMake $car_make = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, CarYear>
     */
    #[ORM\OneToMany(targetEntity: CarYear::class, mappedBy: 'car_model')]
    private Collection $carYears;

    public function __construct() {
        $this->carYears = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getCarMake(): ?CarMake {
        return $this->car_make;
    }

    public function setCarMake(?CarMake $car_make): static {
        $this->car_make = $car_make;

        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, CarYear>
     */
    public function getCarYears(): Collection {
        return $this->carYears;
    }

    public function addCarYear(CarYear $carYear): static {
        if (!$this->carYears->contains($carYear)) {
            $this->carYears->add($carYear);
            $carYear->setCarModel($this);
        }

        return $this;
    }

    public function removeCarYear(CarYear $carYear): static {
        if ($this->carYears->removeElement($carYear)) {
            // set the owning side to null (unless already changed)
            if ($carYear->getCarModel() === $this) {
                $carYear->setCarModel(null);
            }
        }

        return $this;
    }
}
