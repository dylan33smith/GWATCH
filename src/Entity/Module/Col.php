<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="col")
 */
class Col
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $col;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $test;

    /**
     * @ORM\Column(type="string")
     */
    private $refTable;

    /**
     * @ORM\Column(type="string")
     */
    private $refCol;

    /**
     * @ORM\Column(type="string")
     */
    private $moduleId;

    public function getCol(): ?int
    {
        return $this->col;
    }

    public function setCol(int $col): self
    {
        $this->col = $col;
        return $this;
    }

    public function getTest(): ?string
    {
        return $this->test;
    }

    public function setTest(?string $test): self
    {
        $this->test = $test;
        return $this;
    }

    public function getRefTable(): ?string
    {
        return $this->refTable;
    }

    public function setRefTable(string $refTable): self
    {
        $this->refTable = $refTable;
        return $this;
    }

    public function getRefCol(): ?string
    {
        return $this->refCol;
    }

    public function setRefCol(string $refCol): self
    {
        $this->refCol = $refCol;
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