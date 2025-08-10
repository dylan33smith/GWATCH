<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# SNP index table (main SNP reference table)

/**
 * @ORM\Entity
 * @ORM\Table(name="ind", indexes={
 *     @ORM\Index(name="idx_ind", columns={"ind"})
 * })
 */
class Ind
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $chr;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $nrow;

    /**
     * @ORM\Column(type="integer")
     */
    private $ind;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $moduleId;

    public function getChr(): ?int
    {
        return $this->chr;
    }

    public function setChr(int $chr): self
    {
        $this->chr = $chr;
        return $this;
    }

    public function getNrow(): ?int
    {
        return $this->nrow;
    }

    public function setNrow(int $nrow): self
    {
        $this->nrow = $nrow;
        return $this;
    }

    public function getInd(): ?int
    {
        return $this->ind;
    }

    public function setInd(int $ind): self
    {
        $this->ind = $ind;
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