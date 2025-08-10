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
     * @ORM\Column(type="integer")
     */
    private $pos;

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

    public function getPos(): ?int
    {
        return $this->pos;
    }

    public function setPos(int $pos): self
    {
        $this->pos = $pos;
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