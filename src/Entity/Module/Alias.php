<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;


# SNP aliases/names

/**
 * @ORM\Entity
 * @ORM\Table(name="alias")
 */
class Alias
{
    /**
     * @ORM\ManyToOne(targetEntity="Ind")
     * @ORM\JoinColumn(name="ind", referencedColumnName="ind")
     */
    private $ind;

    /**
     * @ORM\Column(type="string")
     */
    private $alias;

    public function getInd()
    {
        return $this->ind;
    }

    public function setInd($ind): self
    {
        $this->ind = $ind;
        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;
        return $this;
    }
}