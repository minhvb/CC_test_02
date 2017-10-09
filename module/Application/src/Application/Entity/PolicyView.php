<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PolicyViewRepository")
 * @ORM\Table(name="policy_view")
 */
class PolicyView extends Common
{

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $userId;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $policyId;

    /** @ORM\Column(type="integer", length=4, nullable=true) */
    protected $viewDate;

    /**
     * @return mixed
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getPolicyId() {
        return $this->policyId;
    }

    /**
     * @param mixed $policyId
     */
    public function setPolicyId($policyId) {
        $this->policyId = $policyId;
    }

    /**
     * @return mixed
     */
    public function getViewDate() {
        return $this->viewDate;
    }

    /**
     * @param mixed $viewDate
     */
    public function setViewDate($viewDate) {
        $this->viewDate = $viewDate;
    }

}