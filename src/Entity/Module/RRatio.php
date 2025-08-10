<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# Ranked ratio/odds ratio

/**
 * @ORM\Entity
 * @ORM\Table(name="r_ratio")
 */
class RRatio
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $vInd;

    /**
     * @ORM\Column(type="integer")
     */
    private $rRatio;

    /**
     * @ORM\Column(type="string")
     */
    private $moduleId;

    public function getVInd(): ?int
    {
        return $this->vInd;
    }

    public function setVInd(int $vInd): self
    {
        $this->vInd = $vInd;
        return $this;
    }

    public function getRRatio(): ?int
    {
        return $this->rRatio;
    }

    public function setRRatio(int $rRatio): self
    {
        $this->rRatio = $rRatio;
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