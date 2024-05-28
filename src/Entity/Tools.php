<?php

namespace App\Entity;

use App\Repository\ToolsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ToolsRepository::class)]
class Tools
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;


    #[ORM\Column(nullable: true)]
    private ?bool $maintenance = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isMaintenance(): ?bool
    {
        return $this->maintenance;
    }

    public function setMaintenance(?bool $maintenance): static
    {
        $this->maintenance = $maintenance;

        return $this;
    }
}
