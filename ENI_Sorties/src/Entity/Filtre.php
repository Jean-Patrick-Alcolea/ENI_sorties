<?php

namespace App\Entity;

use App\Repository\FiltreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiltreRepository::class)]
class Filtre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idUser = null;

    #[ORM\Column(nullable: true)]
    private ?int $choixIdCampus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stringMotSearch = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebutSearch = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinSearch = null;

    #[ORM\Column]
    private ?bool $checkUserOrganise = null;

    #[ORM\Column]
    private ?bool $checkUserInscrit = null;

    #[ORM\Column]
    private ?bool $checkSortiePassee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFiltre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getChoixIdCampus(): ?int
    {
        return $this->choixIdCampus;
    }

    public function setChoixIdCampus(?int $choixIdCampus): static
    {
        $this->choixIdCampus = $choixIdCampus;

        return $this;
    }

    public function getStringMotSearch(): ?string
    {
        return $this->stringMotSearch;
    }

    public function setStringMotSearch(?string $stringMotSearch): static
    {
        $this->stringMotSearch = $stringMotSearch;

        return $this;
    }

    public function getDateDebutSearch(): ?\DateTimeInterface
    {
        return $this->dateDebutSearch;
    }

    public function setDateDebutSearch(?\DateTimeInterface $dateDebutSearch): static
    {
        $this->dateDebutSearch = $dateDebutSearch;

        return $this;
    }

    public function getDateFinSearch(): ?\DateTimeInterface
    {
        return $this->dateFinSearch;
    }

    public function setDateFinSearch(?\DateTimeInterface $dateFinSearch): static
    {
        $this->dateFinSearch = $dateFinSearch;

        return $this;
    }

    public function isCheckUserOrganise(): ?bool
    {
        return $this->checkUserOrganise;
    }

    public function setCheckUserOrganise(bool $checkUserOrganise): static
    {
        $this->checkUserOrganise = $checkUserOrganise;

        return $this;
    }

    public function isCheckUserInscrit(): ?bool
    {
        return $this->checkUserInscrit;
    }

    public function setCheckUserInscrit(bool $checkUserInscrit): static
    {
        $this->checkUserInscrit = $checkUserInscrit;

        return $this;
    }

    public function isCheckSortiePassee(): ?bool
    {
        return $this->checkSortiePassee;
    }

    public function setCheckSortiePassee(bool $checkSortiePassee): static
    {
        $this->checkSortiePassee = $checkSortiePassee;

        return $this;
    }

    public function getDateFiltre(): ?\DateTimeInterface
    {
        return $this->dateFiltre;
    }

    public function setDateFiltre(\DateTimeInterface $dateFiltre): static
    {
        $this->dateFiltre = $dateFiltre;

        return $this;
    }
}
