<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pval")
 */
class Pval
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
     * @ORM\Column(type="float")
     */
    private $pval;

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

    public function getPval(): ?float
    {
        return $this->pval;
    }

    public function setPval(float $pval): self
    {
        $this->pval = $pval;
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