<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Application\Entity\Repository\PolicySurveyRepository")
 * @ORM\Table(name="policy_survey")
 */
class PolicySurvey extends Common
{

    /** @ORM\Column(type="integer", length=11, nullable=false) */
    protected $policyId;

    /** @ORM\Column(type="integer", length=11, nullable=false) */
    protected $surveyId;
	
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
    public function getSurveyId()
    {
    	return $this->surveyId;
    }
    
    /**
     * @param mixed $surveyId
     */
    public function setSurveyId($surveyId)
    {
    	$this->surveyId = $surveyId;
    }

}
