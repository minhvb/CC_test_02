<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PolicyFavouriteRepository")
 * @ORM\Table(name="policy_favourite")
 */
class PolicyFavourite extends Common
{

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $userId;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $policyId;

    /** @ORM\Column(type="boolean", length=11, nullable=true) */
    protected $isSentMail;

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

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
    public function getIsSentMail() {
        return $this->isSentMail;
    }

    /**
     * @param mixed $isSentMail
     */
    public function setIsSentMail($isSentMail) {
        $this->isSentMail = $isSentMail;
    }


}