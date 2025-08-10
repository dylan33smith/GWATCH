<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Minor allele frequency

/**
 * @ORM\Entity
 * @ORM\Table(name="maf")
 */
class Maf
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
     * @ORM\Column(type="float")
     */
    private $maf;

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