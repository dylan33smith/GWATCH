<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# SNP position (relative location on chromosome)

/**
 * @ORM\Entity
 * @ORM\Table(name="pos")
 */
class Pos
{
    /**
     * @ORM\ManyToOne(targetEntity="Ind")
     * @ORM\JoinColumn(name="ind", referencedColumnName="ind")
     */
    private $ind;

    /**
     * @ORM\Column(type="integer")
     */
    private $pos;

    public function getInd()
    {
        return $this->ind;
    }

    public function setInd($ind): self
    {
        $this->ind = $ind;
        return $this;
    }

    public function getPos(): ?int
    {
        return $this->pos;
    }

    public function setPos(int $pos): self
    {
        $this->pos = $pos;
        return $this;
    }
}