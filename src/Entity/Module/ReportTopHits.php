<?php
namespace App\Entity\Module;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="report_top_hits")
 */
class ReportTopHits
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bits;

    /**
     * @ORM\ManyToOne(targetEntity="RadiusInd")
     * @ORM\JoinColumn(name="radius_ind", referencedColumnName="radius_ind")
     */
    private $radius_ind;

    /**
     * @ORM\ManyToOne(targetEntity="VInd")
     * @ORM\JoinColumn(name="v_ind", referencedColumnName="v_ind")
     */
    private $v_ind;

    /**
     * @ORM\Column(type="integer")
     */
    private $r_density;

    /**
     * @ORM\Column(type="integer")
     */
    private $r_naive_p;

    /**
     * @ORM\Column(type="integer")
     */
    private $left_ind;

    /**
     * @ORM\Column(type="integer")
     */
    private $right_ind;

    /**
     * @ORM\Column(type="integer")
     */
    private $left_cnt;

    /**
     * @ORM\Column(type="integer")
     */
    private $right_cnt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $density;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $naive_p;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $adj_p;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cal_p;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBits(): ?bool
    {
        return $this->bits;
    }

    public function setBits(bool $bits): self
    {
        $this->bits = $bits;
        return $this;
    }

    public function getRadiusInd()
    {
        return $this->radius_ind;
    }

    public function setRadiusInd($radius_ind): self
    {
        $this->radius_ind = $radius_ind;
        return $this;
    }

    public function getVInd()
    {
        return $this->v_ind;
    }

    public function setVInd($v_ind): self
    {
        $this->v_ind = $v_ind;
        return $this;
    }

    public function getRDensity(): ?int
    {
        return $this->r_density;
    }

    public function setRDensity(int $r_density): self
    {
        $this->r_density = $r_density;
        return $this;
    }

    public function getRNaiveP(): ?int
    {
        return $this->r_naive_p;
    }

    public function setRNaiveP(int $r_naive_p): self
    {
        $this->r_naive_p = $r_naive_p;
        return $this;
    }

    public function getLeftInd(): ?int
    {
        return $this->left_ind;
    }

    public function setLeftInd(int $left_ind): self
    {
        $this->left_ind = $left_ind;
        return $this;
    }

    public function getRightInd(): ?int
    {
        return $this->right_ind;
    }

    public function setRightInd(int $right_ind): self
    {
        $this->right_ind = $right_ind;
        return $this;
    }

    public function getLeftCnt(): ?int
    {
        return $this->left_cnt;
    }

    public function setLeftCnt(int $left_cnt): self
    {
        $this->left_cnt = $left_cnt;
        return $this;
    }

    public function getRightCnt(): ?int
    {
        return $this->right_cnt;
    }

    public function setRightCnt(int $right_cnt): self
    {
        $this->right_cnt = $right_cnt;
        return $this;
    }

    public function getDensity(): ?float
    {
        return $this->density;
    }

    public function setDensity(?float $density): self
    {
        $this->density = $density;
        return $this;
    }

    public function getNaiveP(): ?float
    {
        return $this->naive_p;
    }

    public function setNaiveP(?float $naive_p): self
    {
        $this->naive_p = $naive_p;
        return $this;
    }

    public function getAdjP(): ?float
    {
        return $this->adj_p;
    }

    public function setAdjP(?float $adj_p): self
    {
        $this->adj_p = $adj_p;
        return $this;
    }

    public function getCalP(): ?float
    {
        return $this->cal_p;
    }

    public function setCalP(?float $cal_p): self
    {
        $this->cal_p = $cal_p;
        return $this;
    }
}