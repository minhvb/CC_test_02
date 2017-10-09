<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\UserHistoryRepository")
 * @ORM\Table(name="user_history")
 */
class UserHistory
{
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;
    
    /** @ORM\Column(type="integer", nullable=false) */
    protected $userId;
    
    /**
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    protected $username;

    /** @ORM\Column(type="integer", nullable=false) */
    protected $roleId;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $loginDate;

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
    public function getLoginDate() {
        return $this->loginDate;
    }

    /**
     * @param mixed $loginDate
     */
    public function setLoginDate($loginDate) {
        $this->loginDate = $loginDate;
    }

    /**
     * @return mixed
     */
    public function getRoleId() {
        return $this->roleId;
    }

    /**
     * @param mixed $roleId
     */
    public function setRoleId($roleId) {
        $this->roleId = $roleId;
    }

    /**
     * @return mixed
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

}