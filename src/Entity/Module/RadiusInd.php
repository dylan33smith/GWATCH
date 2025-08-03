<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="radius_ind")
 */
class RadiusInd
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $radius_ind;

    /**
     * @ORM\Column(type="string")
     */
    private $radius_type;

    /**
     * @ORM\Column(type="integer")
     */
    private $radius_val;

    public function getRadiusInd(): ?int
    {
        return $this->radius_ind;
    }

    public function setRadiusInd(int $radius_ind): self
    {
        $this->radius_ind = $radius_ind;
        return $this;
    }

    public function getRadiusType(): ?string
    {
        return $this->radius_type;
    }

    public function setRadiusType(string $radius_type): self
    {
        $this->radius_type = $radius_type;
        return $this;
    }

    public function getRadiusVal(): ?int
    {
        return $this->radius_val;
    }

    public function setRadiusVal(int $radius_val): self
    {
        $this->radius_val = $radius_val;
        return $this;
    }
}