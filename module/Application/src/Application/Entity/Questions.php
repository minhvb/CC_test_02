<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\QuestionsRepository")
 * @ORM\Table(name="questions")
 */
class Questions extends Common
{
    /**
     * @ORM\Column(type="integer", length= 11, nullable=false)
     */
    protected $surveyId;

    /**
     * @ORM\Column(type="string", length=1000, nullable=false)
     */
    protected $content;

    /**
     * @ORM\Column(type="smallint", length= 4, nullable=false)
     */
    protected $typeQuestion;


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
    public function getContent() {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    public function getTypeQuestion() {
        return $this->typeQuestion;
    }

    /**
     * @param mixed $description
     */
    public function setTypeQuestion($typeQuestion) {
        $this->typeQuestion = $typeQuestion;
    }
}
