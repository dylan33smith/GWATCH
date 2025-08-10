<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="alias", indexes={
 *     @ORM\Index(name="idx_alias", columns={"alias"})
 * })
 */
class Alias
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $ind;

    /**
     * @ORM\Column(type="string")
     */
    private $alias;

    /**
     * @ORM\Column(type="string")
     */
    private $moduleId;

    public function getInd(): ?int
    {
        return $this->ind;
    }

    public function setInd(int $ind): self
    {
        $this->ind = $ind;
        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;
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