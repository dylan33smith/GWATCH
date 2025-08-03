<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Chromosome support table

/**
 * @ORM\Entity
 * @ORM\Table(name="chrsupp")
 */
class Chrsupp
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $chr;

    /**
     * @ORM\Column(type="integer")
     */
    private $chroff;

    /**
     * @ORM\Column(type="integer")
     */
    private $chrlen;

    public function getChr(): ?int
    {
        return $this->chr;
    }

    public function setChr(int $chr): self
    {
        $this->chr = $chr;
        return $this;
    }

    public function getChroff(): ?int
    {
        return $this->chroff;
    }

    public function setChroff(int $chroff): self
    {
        $this->chroff = $chroff;
        return $this;
    }

    public function getChrlen(): ?int
    {
        return $this->chrlen;
    }

    public function setChrlen(int $chrlen): self
    {
        $this->chrlen = $chrlen;
        return $this;
    }
}