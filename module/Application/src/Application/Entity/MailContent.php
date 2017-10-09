<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\MailContentRepository")
 * @ORM\Table(name="mail_content")
 */
class MailContent extends Common
{

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $userId;

    /** @ORM\Column(type="integer", length=11, nullable=true) */
    protected $policyId;

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


}