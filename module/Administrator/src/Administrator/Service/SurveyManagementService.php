<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administrator\Service;

use Administrator\Service\ServiceInterface\SurveyManagementServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class SurveyManagementService implements SurveyManagementServiceInterface, ServiceLocatorAwareInterface {
    
    use ServiceLocatorAwareTrait;
    /**
     *
     * @return \Zend\Mvc\I18n\Translator
     */
    public function getTranslator(){
    	return $this->getServiceLocator()->get('MVCTranslator');
    }
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager(){
    	return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
    
    public function updateSurvey($data){
    	$em = $this->getEntityManager();
    	$em->getConnection()->beginTransaction();
    	try {
    		$repos = $em->getRepository('Application\Entity\Survey');
    		$repos->updateSurvey($data);
    		$em->getConnection()->commit();
    		return true;
    	}catch(\Exception $ex){
    		$em->getConnection()->rollback();
    		return false;
    	}
    }
    
    public function addMultipleQuestionBySurveyPolicy($data, $surveyId){
    	$em = $this->getEntityManager();
    	$em->getConnection()->beginTransaction();
    	try {
    		$repos = $em->getRepository('Application\Entity\Questions');
    		$repos->deleteQuestionBySurvey($surveyId);
    		$repos->addMultipleQuestionBySurveyPolicy($data, $surveyId);
    		$em->getConnection()->commit();
    		return true;
    	}catch(\Exception $ex){
    		$em->getConnection()->rollback();
    		return false;
    	}
    }
    
    public function addMultipleAnswersBySurveyPolicy($data, $surveyId){
    	$em = $this->getEntityManager();
    	$repos = $em->getRepository('Application\Entity\Questions');
    	$ansRepos = $em->getRepository('Application\Entity\Answers');
    	$em->getConnection()->beginTransaction();
    	try {
	    	$questions = $repos->getListQuestionBySurvey($surveyId);
	    	$questions = array_column($questions, 'questionId');
	    	$questions = array_combine($questions, $data);
	    	$ansRepos->addMultipleAnswerByMultipleQuestion($questions);
	    	$em->getConnection()->commit();
    		return true;
    	}catch(\Exception $ex){
    		$em->getConnection()->rollback();
    		return false;
    	}
    }
    
    public function getSurveyPolicyDetail($surveyId) {
    	$em = $this->getEntityManager();
    	$questionsRepo = $em->getRepository('Application\Entity\Questions');
    	$answersRepo = $em->getRepository('Application\Entity\Answers');
    	$questions = $questionsRepo->getListQuestionBySurvey($surveyId);
    	foreach ($questions as $key => $question) {
    		$answers = $answersRepo->getListAnswers($question['questionId']);
    		$questions[$key]['answers'] = $answers;
    	}
    	return $questions;
    }
    
}