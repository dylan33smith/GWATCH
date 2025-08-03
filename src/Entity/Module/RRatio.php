<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Ranked ratio/odds ratio

/**
 * @ORM\Entity
 * @ORM\Table(name="rratio")
 */
class RRatio
{
    /**
     * @ORM\ManyToOne(targetEntity="VInd")
     * @ORM\JoinColumn(name="v_ind", referencedColumnName="v_ind")
     */
    private $v_ind;

    /**
     * @ORM\Column(type="integer")
     */
    private $r_ratio;

    public function getVInd()
    {
        return $this->v_ind;
    }

    public function setVInd($v_ind): self
    {
        $this->v_ind = $v_ind;
        return $this;
    }

    public function getRRatio(): ?int
    {
        return $this->r_ratio;
    }

    public function setRRatio(int $r_ratio): self
    {
        $this->r_ratio = $r_ratio;
        return $this;
    }
}