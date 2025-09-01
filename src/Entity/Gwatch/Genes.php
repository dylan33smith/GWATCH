<?php

namespace App\Entity\Gwatch;

use App\Repository\GenesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenesRepository::class)]
#[ORM\Table(name: 'genes', indexes: [
    new ORM\Index(name: 'idx_genes_chr', columns: ['chr'])
])]
class Genes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', length: 8, nullable: false)]
    private int $build;

    #[ORM\Column(type: 'integer', length: 8, nullable: false)]
    private int $chr;

    #[ORM\Column(type: 'integer', length: 11, nullable: false)]
    private int $posstart;

    #[ORM\Column(type: 'integer', length: 11, nullable: false)]
    private int $posend;

    #[ORM\Column(type: 'integer', length: 11, nullable: false)]
    private int $strand;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $gene;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuild(): int
    {
        return $this->build;
    }

    public function setBuild(int $build): self
    {
        $this->build = $build;
        return $this;
    }

    public function getChr(): int
    {
        return $this->chr;
    }

    public function setChr(int $chr): self
    {
        $this->chr = $chr;
        return $this;
    }

    public function getPosstart(): int
    {
        return $this->posstart;
    }

    public function setPosstart(int $posstart): self
    {
        $this->posstart = $posstart;
        return $this;
    }

    public function getPosend(): int
    {
        return $this->posend;
    }

    public function setPosend(int $posend): self
    {
        $this->posend = $posend;
        return $this;
    }

    public function getStrand(): int
    {
        return $this->strand;
    }

    public function setStrand(int $strand): self
    {
        $this->strand = $strand;
        return $this;
    }

    public function getGene(): string
    {
        return $this->gene;
    }

    public function setGene(string $gene): self
    {
        $this->gene = $gene;
        return $this;
    }
}
