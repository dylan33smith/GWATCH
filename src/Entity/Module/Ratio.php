<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Ration/odds ratio data

/**
 * @ORM\Entity
 * @ORM\Table(name="ratio")
 */
class Ratio
{
    /**
     * @ORM\ManyToOne(targetEntity="VInd")
     * @ORM\JoinColumn(name="v_ind", referencedColumnName="v_ind")
     */
    private $v_ind;

    /**
     * @ORM\Column(type="float")
     */
    private $ratio;

    public function getVInd()
    {
        return $this->v_ind;
    }

    public function setVInd($v_ind): self
    {
        $this->v_ind = $v_ind;
        return $this;
    }

    public function getRatio(): ?float
    {
        return $this->ratio;
    }

    public function setRatio(float $ratio): self
    {
        $this->ratio = $ratio;
        return $this;
    }
}