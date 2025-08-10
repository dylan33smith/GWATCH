<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Allele information

/**
 * @ORM\Entity
 * @ORM\Table(name="allele")
 */
class Allele
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ind")
     * @ORM\JoinColumn(name="ind", referencedColumnName="ind")
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInd()
    {
        return $this->ind;
    }

    public function setInd($ind): self
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
