<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# ranked p-values

/**
 * @ORM\Entity
 * @ORM\Table(name="rpval")
 */
class RPval
{
    /**
     * @ORM\ManyToOne(targetEntity="VInd")
     * @ORM\JoinColumn(name="v_ind", referencedColumnName="v_ind")
     */
    private $v_ind;

    /**
     * @ORM\Column(type="integer")
     */
    private $r_pval;

    public function getVInd()
    {
        return $this->v_ind;
    }

    public function setVInd($v_ind): self
    {
        $this->v_ind = $v_ind;
        return $this;
    }

    public function getRPval(): ?int
    {
        return $this->r_pval;
    }

    public function setRPval(int $r_pval): self
    {
        $this->r_pval = $r_pval;
        return $this;
    }
}