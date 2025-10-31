<?php

namespace App\Entity;

use App\Repository\CarYearRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarYearRepository::class)]
class CarYear {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\ManyToOne(inversedBy: 'carYears')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CarModel $car_model = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getYear(): ?int {
        return $this->year;
    }

    public function setYear(int $year): static {
        $this->year = $year;

        return $this;
    }

    public function getCarModel(): ?CarModel {
        return $this->car_model;
    }

    public function setCarModel(?CarModel $car_model): static {
        $this->car_model = $car_model;

        return $this;
    }
}
