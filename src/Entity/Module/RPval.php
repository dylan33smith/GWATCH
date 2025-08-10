<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="r_pval")
 */
class RPval
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $vInd;

    /**
     * @ORM\Column(type="integer")
     */
    private $rPval;

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

    public function getRPval(): ?int
    {
        return $this->rPval;
    }
    
    public function setRPval(int $rPval): self
    {
        $this->rPval = $rPval;
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