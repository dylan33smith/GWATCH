<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="v_ind", indexes={
 *     @ORM\Index(name="idx_v_ind", columns={"v_ind"})
 * })
 */
class VInd
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $ind;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
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

    public function getInd(): ?int
    {
        return $this->ind;
    }

    public function setInd(int $ind): self
    {
        $this->ind = $ind;
        return $this;
    }

    public function getCol(): ?int
    {
        return $this->col;
    }

    public function setCol(int $col): self
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