<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# SNP position (relative location on chromosome)

/**
 * @ORM\Entity
 * @ORM\Table(name="pos", indexes={
 *     @ORM\Index(name="idx_pos", columns={"pos"})
 * })
 */
class Pos
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
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

    public function getInd(): ?int
    {
        return $this->ind;
    }

    public function setInd(int $ind): self
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