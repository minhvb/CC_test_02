<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\ResponseRepository")
 * @ORM\Table(name="response")
 */
class Response extends Common
{
     /**
     * @ORM\Column(type="integer", length= 11, nullable=false)
     */
    protected $userId;

    /**
     * @ORM\Column(type="integer", length= 11, nullable=true)
     */
    protected $noticeId;

    /**
     * @ORM\Column(type="integer", length= 11, nullable=false)
     */
    protected $surveyId;

    /**
     * @ORM\Column(type="integer", length= 11, nullable=false)
     */
    protected $questionId;

    /**
     * @ORM\Column(type="integer", length= 11, nullable=true)
     */
    protected $answerId;

    /**
     * @ORM\Column(type="integer", length= 11, nullable=true)
     */
    protected $policyId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $answer;

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
    public function getSurveyId() {
        return $this->surveyId;
    }

    /**
     * @param mixed $surveyId
     */
    public function setSurveyId($surveyId) {
        $this->surveyId = $surveyId;
    }

    /**
     * @return mixed
     */
    public function getNoticeId() {
        return $this->noticeId;
    }

    /**
     * @param mixed $noticeId
     */
    public function setNoticeId($noticeId) {
        $this->noticeId = $noticeId;
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
    public function getAnswerId() {
        return $this->answerId;
    }

    /**
     * @param mixed $answerId
     */
    public function setAnswerId($answerId) {
        $this->answerId = $answerId;
    }

    /**
     * @return mixed
     */
    public function getPolicyId() {
        return $this->policyId;
    }

    /**
     * @param mixed $surveyId
     */
    public function setPolicyId($policyId) {
        $this->policyId = $policyId;
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
}
