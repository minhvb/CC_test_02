<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\RecruitmentTimeTempRepository")
 * @ORM\Table(name="recruitment_time_temp")
 */
class RecruitmentTimeTemp extends Common
{
    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $policyId;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $startDate;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $deadline;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $endDate;

    /**
     * @return mixed
     */
    public function getPolicyId()
    {
        return $this->policyId;
    }

    /**
     * @param mixed $policyId
     */
    public function setPolicyId($policyId)
    {
        $this->policyId = $policyId;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param mixed $deadline
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    /**
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }


}