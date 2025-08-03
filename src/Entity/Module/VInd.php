<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Variant index (links SNPs to tests)

/**
 * @ORM\Entity
 * @ORM\Table(name="val")
 */
class VInd
{
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
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $v_ind;

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
        return $this->v_ind;
    }

    public function setVInd(int $v_ind): self
    {
        $this->v_ind = $v_ind;
        return $this;
    }
}