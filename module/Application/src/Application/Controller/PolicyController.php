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
use Application\Service\ServiceInterface\PolicyServiceInterface;
use Application\Utils\DateHelper;
use Doctrine\ORM\EntityManager;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class PolicyController extends BaseController {

	/**
	 * @var \Application\Service\PolicyService
	 */
	protected $policyService;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	public function __construct(PolicyServiceInterface $policyService, EntityManager $em) {
		$this->policyService = $policyService;
		$this->em = $em;
	}

	public function indexAction() {
		return new ViewModel();
	}

	public function detailAction(){
			
		$params = $this->params()->fromRoute('id');
		$currentTime = DateHelper::getCurrentTimeStamp();
		$policyId = intval($params);
		if (!$policyId) {
			return $this->notFoundAction();
		}
		$policy = $this->em->getRepository('Application\Entity\Policy')->findOneBy(array(
				'id' => $policyId ));
		if (!$policy) {
			return $this->notFoundAction();
		}
		if( !empty($policy->getUpdateDateSchedule()) && ( $policy->getUpdateDateSchedule() > $currentTime ) ){
			$policy = $this->em->getRepository('Application\Entity\PolicyTemp')->findOneBy(array(
					'id' => $policyId ));
			if (!$policy) {
				return $this->notFoundAction();
			}
		}
		if( empty($policy->getPublishStartdate()) || (!empty($policy->getPublishStartdate()) && $policy->getPublishStartdate() > $currentTime) || (!empty($policy->getPublishEnddate()) && $policy->getPublishEnddate() < $currentTime) ){
			return $this->notFoundAction();
		}
		$attributesPolicy = $this->policyService->getAttributesByPolicy($policyId, $policy->getUpdateDateSchedule());
		$attrArray = in_array(55, $attributesPolicy);
		if($attrArray || $policy->getIsDraft() == 1){
			return $this->notFoundAction();
		}
		$recruitmentTime = $this->policyService->getRecruitmentTimeByPolicy($policyId, $policy->getUpdateDateSchedule());
			
		$glConfig['attributePolicyType'] = $this->glConfig['attributePolicyType'];
		$glConfig['folderUploadPdf'] = $this->glConfig['folderUploadPdf'];
		$attributes = $this->policyService->getAttributesByMultiIds($attributesPolicy);
		
		$policy_favourite = $this->em->getRepository('Application\Entity\PolicyFavourite')->findBy(array(
				'userId' => $this->userInfo["userId"],
				'policyId' => $policyId ));
		try {
			$this->policyService->updatePolicyView($policyId, $this->userInfo["userId"], $this->userInfo["roleId"]);
		}catch (\Exception $ex ){
			return $this->notFoundAction();
		}
		$errorMsg = $this->flashMessenger()->hasErrorMessages() ? $this->flashMessenger()->getErrorMessages()[0] : null;
		return $this->viewModel->setVariables(array(
				'policy' => $policy,
				'recruitmentTime' => $recruitmentTime,
				'glConfig' => $glConfig,
				'attributes' => $attributes,
				'policy_favourite'	=>	$policy_favourite,
				'errorMsg' => $errorMsg
		));
		
	}

	public function confirmPrintAction(){
		$params = $this->params()->fromRoute('id');
		$currentTime = DateHelper::getCurrentTimeStamp();
		$policyId = intval($params);
		if (!$policyId) {
			return $this->notFoundAction();
		}
		$policy = $this->em->getRepository('Application\Entity\Policy')->findOneBy(array(
				'id' => $policyId ));
		if (!$policy) {
			return $this->notFoundAction();
		}
		if( !empty($policy->getUpdateDateSchedule()) && ( $policy->getUpdateDateSchedule() > $currentTime ) ){
			$policy = $this->em->getRepository('Application\Entity\PolicyTemp')->findOneBy(array(
					'id' => $policyId ));
			if (!$policy) {
				return $this->notFoundAction();
			}
		}
		$attributesPolicy = $this->policyService->getAttributesByPolicy($policyId, $policy->getUpdateDateSchedule());
		$recruitmentTime = $this->policyService->getRecruitmentTimeByPolicy($policyId, $policy->getUpdateDateSchedule());
			
		$glConfig['attributePolicyType'] = $this->glConfig['attributePolicyType'];
		$glConfig['folderUploadPdf'] = $this->glConfig['folderUploadPdf'];
		$attributes = $this->policyService->getAttributesByMultiIds($attributesPolicy);
		
		$errorMsg = $this->flashMessenger()->hasErrorMessages() ? $this->flashMessenger()->getErrorMessages()[0] : null;
		$this->viewModel->setTerminal(true);
		return $this->viewModel->setVariables(array(
				'policy' => $policy,
				'recruitmentTime' => $recruitmentTime,
				'glConfig' => $glConfig,
				'attributes' => $attributes,
				'errorMsg' => $errorMsg
		));
	}

	public function addFpolicyAction(){
		$data = $this->params()->fromPost();
		$jsonModel = new JsonModel();
			
		try {
			if(!isset($data['userId']) || !isset($data['policyId'])){
				return $this->notFoundAction();
			}
			$flag = $this->policyService->updateFpolicyAction($data, $this->userInfo["userId"]);
			
			$jsonModel->setVariables(
					array(
							'flag' => $flag,
							'success' => true,
							'errors' => array()
					)
			);
		}catch (\Exception $ex ){
			$jsonModel->setVariables(array(
					'success' => false,
					'errors' => array(),
			));
		}
		return $jsonModel;
	}
	
	public function addTotalPrintAction(){
		$data = $this->params()->fromPost();
		$jsonModel = new JsonModel();
		try {
			if( !isset($data['policyId']) ){
				return $this->notFoundAction();
			}
			$flag = $this->policyService->updatePolicyTotalPrint($data["policyId"], $this->userInfo["userId"]);
			
			$jsonModel->setVariables(
					array(
							'flag' => $flag,
							'success' => true,
							'errors' => array()
					)
			);
		}catch (\Exception $ex ){
			$jsonModel->setVariables(array(
					'success' => false,
					'errors' => array(),
			));
		}
		return $jsonModel;
	}

}
