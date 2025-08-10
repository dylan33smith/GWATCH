<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Minor allele frequency

/**
 * @ORM\Entity
 * @ORM\Table(name="maf", indexes={
 *     @ORM\Index(name="idx_maf", columns={"maf"})
 * })
 */
class Maf
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $ind;

    /**
     * @ORM\Column(type="float")
     */
    private $maf;

    /**
     * @ORM\Column(type="string")
     */
    private $moduleId;

    public function getInd(): ?int
    {
        return $this->ind;
    }

    public function setInd(int $ind): self
    {
        $this->ind = $ind;
        return $this;
    }

    public function getMaf(): ?float
    {
        return $this->maf;
    }

    public function setMaf(float $maf): self
    {
        $this->maf = $maf;
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