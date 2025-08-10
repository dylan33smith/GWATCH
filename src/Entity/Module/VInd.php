<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v_ind")
 */
class VInd
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
     * @ORM\ManyToOne(targetEntity="Col")
     * @ORM\JoinColumn(name="col", referencedColumnName="col")
     */
    private $col;

    /**
     * @ORM\Column(type="integer")
     */
    private $vInd;

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

    public function getCol()
    {
        return $this->col;
    }

    public function setCol($col): self
    {
        $this->col = $col;
        return $this;
    }

    public function getVInd(): ?int
    {
        return $this->vInd;
    }

    public function setVInd(int $vInd): self
    {
        $this->vInd = $vInd;
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