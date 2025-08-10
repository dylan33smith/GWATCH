<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

# chromosome information

/**
 * @ORM\Entity
 * @ORM\Table(name="chr", indexes={
 *     @ORM\Index(name="idx_chrname", columns={"chrname"})
 * })
 */
class Chr
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $chr;

    /**
     * @ORM\Column(type="string")
     */
    private $chrname;

    /**
     * @ORM\Column(type="integer")
     */
    private $len;

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

    public function getChrname(): ?string
    {
        return $this->chrname;
    }

    public function setChrname(string $chrname): self
    {
        $this->chrname = $chrname;
        return $this;
    }

    public function getLen(): ?int
    {
        return $this->len;
    }

    public function setLen(int $len): self
    {
        $this->len = $len;
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