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
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="VInd")
     * @ORM\JoinColumn(name="v_ind", referencedColumnName="v_ind")
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVInd()
    {
        return $this->vInd;
    }

    public function setVInd($vInd): self
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