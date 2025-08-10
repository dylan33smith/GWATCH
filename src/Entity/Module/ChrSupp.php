<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Chromosome support table

/**
 * @ORM\Entity
 * @ORM\Table(name="chrsupp", indexes={
 *     @ORM\Index(name="idx_chr", columns={"chr"})
 * })
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

    /**
     * @ORM\Column(type="string")
     */
    private $moduleId;

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

    public function getModuleId(): ?string
    {
        return $this->moduleId;
    }

    public function setModuleId(string $moduleId): self
    {
        $this->moduleId = $moduleId;
        return $this;
    }
}