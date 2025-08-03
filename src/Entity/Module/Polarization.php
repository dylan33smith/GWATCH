<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="polarization")
 */
class Polarization
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $ind1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $ind2;

    /**
     * @ORM\Column(type="integer")
     */
    private $chr;

    /**
     * @ORM\Column(type="string")
     */
    private $Affy_SNP_ID;

    /**
     * @ORM\Column(type="string")
     */
    private $Affy_SNP_ID_2;

    /**
     * @ORM\Column(type="string")
     */
    private $LD;

    public function getInd1(): ?int
    {
        return $this->ind1;
    }

    public function setInd1(int $ind1): self
    {
        $this->ind1 = $ind1;
        return $this;
    }

    public function getInd2(): ?int
    {
        return $this->ind2;
    }

    public function setInd2(int $ind2): self
    {
        $this->ind2 = $ind2;
        return $this;
    }

    public function getChr(): ?int
    {
        return $this->chr;
    }

    public function setChr(int $chr): self
    {
        $this->chr = $chr;
        return $this;
    }

    public function getAffySnpId(): ?string
    {
        return $this->Affy_SNP_ID;
    }

    public function setAffySnpId(string $Affy_SNP_ID): self
    {
        $this->Affy_SNP_ID = $Affy_SNP_ID;
        return $this;
    }

    public function getAffySnpId2(): ?string
    {
        return $this->Affy_SNP_ID_2;
    }

    public function setAffySnpId2(string $Affy_SNP_ID_2): self
    {
        $this->Affy_SNP_ID_2 = $Affy_SNP_ID_2;
        return $this;
    }

    public function getLd(): ?string
    {
        return $this->LD;
    }

    public function setLd(string $LD): self
    {
        $this->LD = $LD;
        return $this;
    }
}