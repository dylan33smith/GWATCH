<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# P-value table

/**
 * @ORM\Entity
 * @ORM\Table(name="pval")
 */
class Pval
{
    /**
     * @ORM\ManyToOne(targetEntity="VInd")
     * @ORM\JoinColumn(name="v_ind", referencedColumnName="v_ind")
     */
    private $v_ind;

    /**
     * @ORM\Column(type="float")
     */
    private $pval;

    public function getVInd()
    {
        return $this->v_ind;
    }

    public function setVInd($v_ind): self
    {
        $this->v_ind = $v_ind;
        return $this;
    }

    public function getPval(): ?float
    {
        return $this->pval;
    }

    public function setPval(float $pval): self
    {
        $this->pval = $pval;
        return $this;
    }
}