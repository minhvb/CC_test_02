<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Controller\BaseController;
use Application\Utils\ApplicationConst;
use Application\Service\ServiceInterface\SurveyServiceInterface;

use Doctrine\ORM\EntityManager;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class SurveyController extends BaseController {

	/**
	 * @var \Application\Service\SurveyService
	 */
	protected $surveyService;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(SurveyServiceInterface $surveyService, EntityManager $em) {
		$this->surveyService = $surveyService;
		$this->em = $em;
	}

	public function voteAction(){
		$params = $this->params()->fromRoute('id');
		$policyId = intval($params);
		$paramPage = $this->params()->fromQuery('page');
		$page =  intval( $paramPage );
		if(empty($page)) $page = 1;
		if (!$policyId || !$page) {
			return $this->notFoundAction();
		}
		$resultPerPage = ApplicationConst::RESULT_PER_PAGE;
		try{
			list( $questions, $totalPages, $totalResults, $listQId, $listSurveyId ) = $this->surveyService->getQuestionsByPolicy( $policyId, $page, $resultPerPage );
			$surveyId = isset($listSurveyId[0])? $listSurveyId[0] : null;
		}catch (\Exception $ex ){
			return $this->notFoundAction();
		}
		return $this->viewModel->setVariables(array(
			'surveyId' => $surveyId,
			'policyId'	=> $policyId,
			'listQId' => Json::encode($listQId),
			'questions' => $questions,
			'totalResults' => $totalResults,
			'resultPerPage' => $resultPerPage,
			'totalPages' => $totalPages,
			'page' => $page,
			'numQuestionsPage' => count($questions)
		));
	}

	public function addSvoteAction(){
		$data = $this->params()->fromPost();
		$jsonModel = new JsonModel();
		$jsonModel->setVariables(
			array(
				'success' => true,
				'errors' => array(),
			)
		);
		try {
			if( !isset($data['policyId']) || !isset($data['question']) || !isset($data['surveyId']) ){
				return $this->notFoundAction();
			}
			$data['userId'] = $this->userInfo["userId"];
			$this->surveyService->insertResponseByPolicy($data);
		}catch (\Exception $ex ){
			$jsonModel->setVariables(array(
				'success' => false,
				'errors' => array(),
			));
		}
		return $jsonModel;

	}


}
