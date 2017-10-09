<?php

/*
 * To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace Application\Service;

use Application\Service\ServiceInterface\SurveyServiceInterface;
use Application\Utils\DateHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Application\Utils\ApplicationConst;

class SurveyService implements SurveyServiceInterface, ServiceLocatorAwareInterface {

	use ServiceLocatorAwareTrait;

	/**
	 *
	 * @return \Zend\Mvc\I18n\Translator
	 */
	public function getTranslator() {
		return $this->getServiceLocator()->get('MVCTranslator');
	}

	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
	}

	public function getQuestionsByPolicy( $policyId, $page, $resultPerPage ){
		$em = $this->getEntityManager();
		$listSurveyId = array(ApplicationConst::SURVEY_POLICY_ID);
		if(!empty($listSurveyId)){
			$questions = $em->getRepository('Application\Entity\Questions');
			$totalResults = $questions->getTotalResultBySurvey( $listSurveyId );
			$totalPages = ceil($totalResults / $resultPerPage);
			
			$listQ = $questions->getQuestionsBySurvey( $listSurveyId, $page, $resultPerPage);
			
			$listQId = array();
			if($listQ && !empty($listQ)){
				$answers = $em->getRepository('Application\Entity\Answers');
				foreach ($listQ as $key => $value){
					$listQ[$key]['answers'] = $answers->getListAnswers($value['id']);
					$listQId[] = $value['id'];
				}
			}
		}
		return array(isset($listQ) ? $listQ : null, isset($totalPages) ? $totalPages : null, isset($totalResults) ? $totalResults : null, isset($listQId) ? $listQId : null, isset($listSurveyId) ? $listSurveyId : null );
	}
	
	public function insertResponseByPolicy( $data ){
		$em = $this->getEntityManager();
		$em->getConnection()->beginTransaction();
		try {
			$repos = $em->getRepository('Application\Entity\Response');
			$repos->insertMultipleResponseByPolicy($data);
			$em->getConnection()->commit();
			return true;
		}catch (\Exception $ex ){
			$em->getConnection()->rollback();
			return false;
		}
		 
	}
	
	
}
