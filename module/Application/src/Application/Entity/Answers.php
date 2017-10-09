<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\AnswersRepository")
 * @ORM\Table(name="answers")
 */
class Answers extends Common
{
    /**
     * @ORM\Column(type="integer", length= 11, nullable=false)
     */
    protected $questionId;

    /**
     * @ORM\Column(type="string", length=500, nullable=false)
     */
    protected $content;

    /**
     * @return mixed
     */
    public function getQuestionId() {
        return $this->questionId;
    }

    /**
     * @param mixed $surveyId
     */
    public function setQuestionId($questionId) {
        $this->questionId = $questionId;
    }

    /**
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content) {
        $this->content = $content;
    }
}
