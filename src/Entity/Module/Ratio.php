<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ratio")
 */
class Ratio
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $vInd;

    /**
     * @ORM\Column(type="float")
     */
    private $ratio;

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

    public function getRatio(): ?float
    {
        return $this->ratio;
    }

    public function setRatio(float $ratio): self
    {
        $this->ratio = $ratio;
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