<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends Common
{
    /**
     * Foreign key reference to Role
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $roleId;

    /**
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    protected $userName;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $bureauId;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $departmentId;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $divisionId;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $nextEmail;

    /**
     * encrypted by md5
     * @ORM\Column(name="authenticationString", type="string", length=256, nullable=true, options={"comment":"encrypted by md5"})
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $business;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     */
    protected $companySize;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $region;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $passwordUpdateDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lastLoginFail;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default"=0})
     */
    protected $totalLoginFail;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $passwordHistory;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $questionId;

    /**
     * @ORM\Column(type="string", length=20,nullable=true)
     */
    protected $answer;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    protected $token;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tokenExpireDate;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"=0})
     */
    protected $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"=0})
     */
    protected $isSettingMail;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lastWrongQuestion;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default"=0})
     */
    protected $totalWrongQuestion;

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
    public function getUserName() {
        return $this->userName;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName) {
        $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getBureauId() {
        return $this->bureauId;
    }

    /**
     * @param mixed $bureauId
     */
    public function setBureauId($bureauId) {
        $this->bureauId = $bureauId;
    }

    /**
     * @return mixed
     */
    public function getDepartmentId() {
        return $this->departmentId;
    }

    /**
     * @param mixed $departmentId
     */
    public function setDepartmentId($departmentId) {
        $this->departmentId = $departmentId;
    }

    /**
     * @return mixed
     */
    public function getDivisionId() {
        return $this->divisionId;
    }

    /**
     * @param mixed $divisionId
     */
    public function setDivisionId($divisionId) {
        $this->divisionId = $divisionId;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getBusiness() {
        return $this->business;
    }

    /**
     * @param mixed $business
     */
    public function setBusiness($business) {
        $this->business = $business;
    }

    /**
     * @return mixed
     */
    public function getCompanySizeId() {
        return $this->companySizeId;
    }

    /**
     * @param mixed $companySizeId
     */
    public function setCompanySizeId($companySizeId) {
        $this->companySizeId = $companySizeId;
    }

    /**
     * @return mixed
     */
    public function getRegion() {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region) {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getPasswordUpdateDate() {
        return $this->passwordUpdateDate;
    }

    /**
     * @param mixed $passwordUpdateDate
     */
    public function setPasswordUpdateDate($passwordUpdateDate) {
        $this->passwordUpdateDate = $passwordUpdateDate;
    }

    /**
     * @return mixed
     */
    public function getLastLoginFail() {
        return $this->lastLoginFail;
    }

    /**
     * @param mixed $lastLoginFail
     */
    public function setLastLoginFail($lastLoginFail) {
        $this->lastLoginFail = $lastLoginFail;
    }

    /**
     * @return mixed
     */
    public function getTotalLoginFail() {
        return $this->totalLoginFail;
    }

    /**
     * @param mixed $totalLoginFail
     */
    public function setTotalLoginFail($totalLoginFail) {
        $this->totalLoginFail = $totalLoginFail;
    }

    /**
     * @return mixed
     */
    public function getPasswordHistory() {
        return $this->passwordHistory;
    }

    /**
     * @param mixed $passwordHistory
     */
    public function setPasswordHistory($passwordHistory) {
        $this->passwordHistory = $passwordHistory;
    }

    /**
     * @return mixed
     */
    public function getQuestionId() {
        return $this->questionId;
    }

    /**
     * @param mixed $questionId
     */
    public function setQuestionId($questionId) {
        $this->questionId = $questionId;
    }

    /**
     * @return mixed
     */
    public function getAnswer() {
        return $this->answer;
    }

    /**
     * @param mixed $answer
     */
    public function setAnswer($answer) {
        $this->answer = $answer;
    }

    /**
     * @return mixed
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getTokenExpireDate() {
        return $this->tokenExpireDate;
    }

    /**
     * @param mixed $tokenExpireDate
     */
    public function setTokenExpireDate($tokenExpireDate) {
        $this->tokenExpireDate = $tokenExpireDate;
    }

    /**
     * @return mixed
     */
    public function getIsActive() {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }
}
