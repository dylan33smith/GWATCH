<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tests")
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $stattest;

    /**
     * @ORM\Column(type="string")
     */
    private $model;

    /**
     * @ORM\Column(type="string")
     */
    private $statname;

    /**
     * @ORM\Column(type="string")
     */
    private $qss;

    /**
     * @ORM\Column(type="text")
     */
    private $object;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStattest(): ?string
    {
        return $this->stattest;
    }

    public function setStattest(string $stattest): self
    {
        $this->stattest = $stattest;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getStatname(): ?string
    {
        return $this->statname;
    }

    public function setStatname(string $statname): self
    {
        $this->statname = $statname;
        return $this;
    }

    public function getQss(): ?string
    {
        return $this->qss;
    }

    public function setQss(string $qss): self
    {
        $this->qss = $qss;
        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(string $object): self
    {
        $this->object = $object;
        return $this;
    }
}