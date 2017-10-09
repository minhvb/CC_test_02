<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Administrator\Controller;

use Application\Controller\BaseController;
use Administrator\Service\ServiceInterface\SurveyManagementServiceInterface;
use Administrator\Service\ServiceInterface\PolicyManagementServiceInterface;
use Application\Utils\ApplicationConst;
use Application\Utils\DateHelper;
use Application\Utils\UserHelper;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;

class SurveyManagementController extends BaseController {

    /**
     * @var \Administrator\Service\SurveyManagementService
     */
    protected $surveyService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(SurveyManagementServiceInterface $surveyService, EntityManager $em) {
        $this->surveyService = $surveyService;
        $this->em = $em;
    }

    public function indexAction() {
    	
    	$currentUrl = $this->getRequest()->getRequestUri();
    	$resultPerPage = ApplicationConst::RESULT_PER_PAGE;
    	$search = $this->params()->fromQuery();
    	$page = isset($search['page']) && intval($search['page']) > 1 ? intval($search['page']) : 1;
    	
    	$policyService = $this->getServiceLocator()->get('Administrator\Service\PolicyManagementServiceInterface');
    	
    	    	
    	if (UserHelper::isInputRole($this->userInfo['roleId'])) {
    		
    		$isDisable = 1;
    		$search['ddlBureauId'] = $this->userInfo['bureauId'];
    		$search['ddlDepartmentId'] = $this->userInfo['departmentId'];
    		if( !empty($this->userInfo['divisionId']) ) {
    			$search['ddlDivisionId'] = $this->userInfo['divisionId'];
    			$glConfig['divisionId'] = $policyService->getArrayDivisionByCode($search['ddlDivisionId'], $search['ddlDepartmentId']);
    		}else{
    			$glConfig['divisionId'] = $this->glConfig['divisionId'];
    		}
    		$glConfig['bureauId'] = array($search['ddlBureauId'] => $this->glConfig['bureauId'][$search['ddlBureauId']]);
    		$glConfig['departmentId'] = array($search['ddlDepartmentId'] => $this->glConfig['departmentId'][$search['ddlDepartmentId']]);
    		
    		$glConfig['typePolicy'] = $this->glConfig['typePolicy'];
    		$glConfig['typeRecruitmentTime'] = $this->glConfig['typeRecruitmentTime'];
    	
    	}
    	list($data, $totalPages, $totalResults) = $policyService->getPolicyBySearch($search, $page, $resultPerPage);
    	if ($page > 1) {
    		if (!$data) {
    			$previousPage = $page > $totalPages ? $totalPages : ($page - 1);
    			$urlRedirect = strpos($currentUrl, '?') !== false ? $currentUrl . '&' : $currentUrl . '?';
    			$urlRedirect = str_replace("?page=" . $page . "&", "?", $urlRedirect);
    			$urlRedirect = str_replace("&page=" . $page . "&", "&", $urlRedirect);
    			$urlRedirect .= 'page=' . $previousPage;
    			$this->redirect()->toUrl($urlRedirect);
    		}
    	}
    	
    	$surveyId = ApplicationConst::SURVEY_POLICY_ID;
    	$survey = $this->em->getRepository('Application\Entity\Survey')->findOneBy(array(
    			'id' => intval($surveyId) ));
    	
    	return $this->viewModel->setVariables(array(
    			'glConfig' => isset($glConfig) ? $glConfig : $this->glConfig,
    			'search' => $search,
    			'data' => $data,
    			'totalPages' => $totalPages,
    			'totalResults' => $totalResults,
    			'resultPerPage' => $resultPerPage,
    			'page' => $page,
    			'currentUrl' => $currentUrl,
    			'isDisable' => isset($isDisable) ? intval($isDisable) : 0,
    			'action' => !empty($survey) ? "edit" : "add" 
    	));
    }
    
    public function addAction(){
    	$surveyId = ApplicationConst::SURVEY_POLICY_ID;
    	$survey = $this->em->getRepository('Application\Entity\Survey')->findOneBy(array(
    				'id' => intval($surveyId) ));
    	if(!$survey){
	    	$questions = '';
	    	return $this->viewModel->setVariables(array(
	    			'survey' => $survey,
	    			'questions' => $questions ? $questions : ''
	    	));
    	}else{
    		$currentUrl = $this->getRequest()->getRequestUri();
    		$this->redirect()->toUrl('/administrator/survey-management');
    	}
    }
    
    public function editAction(){
    	$surveyId = ApplicationConst::SURVEY_POLICY_ID;
    	$survey = $this->em->getRepository('Application\Entity\Survey')->findOneBy(array(
    			'id' => intval($surveyId) ));
    	if($survey){
    		$questions = $this->surveyService->getSurveyPolicyDetail($surveyId);
    	}
    	return $this->viewModel->setVariables(array(
    			'survey' => $survey,
    			'questions' => $questions ? $questions : '',
    	));
    }
    
    public function saveAction(){
    	$surveyId = ApplicationConst::SURVEY_POLICY_ID;
    	$survey = $this->em->getRepository('Application\Entity\Survey')->findOneBy(array(
    			'id' => intval($surveyId) ));
    	$jsonModel = new JsonModel();
    	$params = $this->params()->fromPost();
    	if($survey){
	    	$dataSurvey = array(
	    		'surveyId' => $surveyId,
	    		'name' => ApplicationConst::SURVEY_NAME,
	    		'description' => ApplicationConst::SURVEY_DESCRIPT,
	    	);
	    	$this->surveyService->updateSurvey($dataSurvey);
    	}
    	if(!$survey){
    		$dataSurvey = array(
    			'surveyName' => ApplicationConst::SURVEY_NAME,
    			'surveyDescription' => ApplicationConst::SURVEY_DESCRIPT,
    		);
    		$this->surveyService->addSurvey($dataSurvey);
    	}
    	
    	if(isset($params['questions']) && isset($params['createDate'])){
    		$this->surveyService->addMultipleQuestionBySurveyPolicy($params['questions'], $surveyId );
    		$resultQuestion = $this->surveyService->addMultipleAnswersBySurveyPolicy($params['questions'], $surveyId);
    		if($resultQuestion){
    			$jsonModel->setVariables( array(
    					'success' => true,
    					'errors' => array()
    			));
    		}
    	}
    	return $jsonModel;
    	
    	
    }

}
