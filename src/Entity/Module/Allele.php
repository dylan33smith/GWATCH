<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Allele information

/**
 * @ORM\Entity
 * @ORM\Table(name="allele", indexes={
 *     @ORM\Index(name="idx_allele", columns={"allele"})
 * })
 */
class Allele
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $ind;

    /**
     * @ORM\Column(type="string")
     */
    private $allele;

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

    public function getAllele(): ?string
    {
        return $this->allele;
    }

    public function setAllele(string $allele): self
    {
        $this->allele = $allele;
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
