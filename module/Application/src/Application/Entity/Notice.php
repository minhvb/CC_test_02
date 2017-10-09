<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\NoticeRepository")
 * @ORM\Table(name="notice")
 */
class Notice extends Common
{
    /**
     * @ORM\Column(type="integer", length= 11, nullable=true)
     */
    protected $surveyId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $firstPublicDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lastPublicDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $no;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isRead;

    /**
     * @return mixed
     */
    public function getSurveyId() {
        return $this->surveyId;
    }

    /**
     * @param mixed $roleId
     */
    public function setSurveyId($surveyId) {
        $this->surveyId = $surveyId;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param mixed $userName
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $bureauId
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $departmentId
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getFirstPublicDate() {
        return $this->firstPublicDate;
    }

    /**
     * @param mixed $divisionId
     */
    public function setFirstPublicDate($firstPublicDate) {
        $this->firstPublicDate = $firstPublicDate;
    }

    /**
     * @return mixed
     */
    public function getLastPublicDate() {
        return $this->lastPublicDate;
    }

    /**
     * @param mixed $email
     */
    public function setLastPublicDate($lastPublicDate) {
        $this->lastPublicDate = $lastPublicDate;
    }

    /**
     * @return mixed
     */
    public function getNo() {
        return $this->no;
    }

    /**
     * @param mixed $password
     */
    public function setNo($no) {
        $this->no = $no;
    }

    /**
     * @return mixed
     */
    public function getIsRead() {
        return $this->isRead;
    }

    /**
     * @param mixed $business
     */
    public function setIsRead($isRead) {
        $this->isRead = $isRead;
    }
}
