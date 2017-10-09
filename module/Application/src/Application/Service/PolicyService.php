<?php

/*
 * To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace Application\Service;

use Application\Service\ServiceInterface\PolicyServiceInterface;
use Application\Entity\PolicyFavourite;
use Application\Entity\PolicyView;
use Application\Entity\PolicyViewStatistic;
use Application\Utils\DateHelper;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Application\Utils\ApplicationConst;

class PolicyService implements PolicyServiceInterface, ServiceLocatorAwareInterface {

	use ServiceLocatorAwareTrait;

	/**
	 *
	 * @return \Zend\Mvc\I18n\Translator
	 */
	public function getTranslator()
	{
		return $this->getServiceLocator()->get('MVCTranslator');
	}

	/**
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager() {
		return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
	}

	public function getAttributesByPolicy( $policyId, $updateDate = null ){
		$em = $this->getEntityManager();
		$currentTime = DateHelper::getCurrentTimeStamp();
		if( !empty($updateDate) && ( $updateDate > $currentTime ) ){
			$dataAtributesPolicy = $em->getRepository('Application\Entity\PolicyAttributeMappingTemp')->findBy(array(
					'policyId' => $policyId
			));
		}else{
			$dataAtributesPolicy = $em->getRepository('Application\Entity\PolicyAttributeMapping')->findBy(array(
					'policyId' => $policyId
			));
		}
		if ($dataAtributesPolicy) {
			/* @var $attribute \Application\Entity\PolicyAttributeMapping */
			foreach ($dataAtributesPolicy as $attribute) {
				$attributesPolicy[] = $attribute->getAttributesPolicyId();
			}
		}
		return isset($attributesPolicy) ? $attributesPolicy : array();
	}

	public function getRecruitmentTimeByPolicy( $policyId, $updateDate = null ) {
		$em = $this->getEntityManager();
		$currentTime = DateHelper::getCurrentTimeStamp();
		if( !empty($updateDate) && ( $updateDate > $currentTime ) ){
			$dataRecruitmentTime = $em->getRepository('Application\Entity\RecruitmentTimeTemp')->findBy(array(
					'policyId' => $policyId
			), array('startDate' => 'ASC'));
		}else{
			$dataRecruitmentTime = $em->getRepository('Application\Entity\RecruitmentTime')->findBy(array(
					'policyId' => $policyId
			), array('startDate' => 'ASC'));
		}
		if ($dataRecruitmentTime) {
			/* @var $recruitment \Application\Entity\RecruitmentTime */
			foreach ($dataRecruitmentTime as $key => $recruitment) {
				$recruitmentsTime[$key]['startDate'] = $recruitment->getStartDate();
				$recruitmentsTime[$key]['endDate'] = $recruitment->getEndDate();
				$recruitmentsTime[$key]['deadline'] = $recruitment->getDeadline();
			}
		}
		return isset($recruitmentsTime) ? $recruitmentsTime : array();
	}

	public function getAttributesByMultiIds( $attributesPolicy ) {
		$attributes = array();
		$em = $this->getEntityManager();
		$data = $em->getRepository('Application\Entity\PolicyAttributes')->getAttributesByMultiIds($attributesPolicy);
		if(!empty($data)){
			foreach ($data as $key => $value) {
				$attributes[$value['attributeType']][$value['id']] = $value;
			}
		}
		return $attributes;
	}

	public function updateFpolicyAction($data, $userId ){
		$em = $this->getEntityManager();
		$policyFEm = $em->getRepository('Application\Entity\PolicyFavourite');
		$policyFavourite = $policyFEm->findBy(array(
				'userId' => intval($data['userId']),
				'policyId' => intval($data['policyId'])));
		$numPolicyFavouriteUser = $policyFEm->getListFavouritePolicies( $userId );
		$flag = array();
		if ( $policyFavourite == null && count($numPolicyFavouriteUser) < ApplicationConst::MAX_FAVOURITE_POLICY ) {
			$policyFavourite = new PolicyFavourite();
			$policyFavourite->setUserId($data['userId']);
			$policyFavourite->setPolicyId($data['policyId']);
			$em->persist($policyFavourite);
			$em->flush();
			$flag['add'] = 1; 
		}elseif ($policyFavourite == null && count($numPolicyFavouriteUser) >= ApplicationConst::MAX_FAVOURITE_POLICY ) {
			$flag['add'] = 0;
		} else {
			$em->remove($policyFavourite[0]);
			$em->flush();
			$flag['remove'] = 1;
		}
		return $flag;
	}
	
	public function updatePolicyTotalPrint($policyId, $roleId ){
		$em = $this->getEntityManager();
		$policyViewStatistic = $em->getRepository('Application\Entity\PolicyViewStatistic');
		$viewStatistic = $policyViewStatistic->findBy( array(
				'policyId' => $policyId,
				'date' => DateHelper::getCurrentDateTime()
				));
		if( is_array($viewStatistic) && ( count($viewStatistic) > 0 ) && isset($viewStatistic[0])){
			$policyViewStatistic->updatePolicyViewStatisticPrint( $policyId, $viewStatistic[0]->getTotalPrint(), $roleId );
		}else{
			$policyViewStatisticObj = new PolicyViewStatistic();
			$policyViewStatisticObj->setPolicyId( $policyId );
			$policyViewStatisticObj->setTotalPrint( 1 );
			$policyViewStatisticObj->setRoleId( $roleId );
			$policyViewStatisticObj->setDate( DateHelper::getCurrentDateTime() );
			$em->persist($policyViewStatisticObj);
			$em->flush();
		}
		return true;
	}
	
	public function updatePolicyView( $policyId, $userId, $roleId ){
		$currentTime = DateHelper::getCurrentTimeStamp();
		$em = $this->getEntityManager();
		$policyView = $em->getRepository('Application\Entity\PolicyView');
		$policyViewCurrent = $policyView->findBy( array(
				'policyId' => $policyId,
				'userId' => $userId ));
		if( !empty($policyViewCurrent) ){
			$policyView->updatePolicyViewCurrent($policyId, $userId);
		}else{
			$policyViewAdd = new PolicyView();
			$policyViewAdd->setUserId( $userId );
			$policyViewAdd->setPolicyId( $policyId );
			$policyViewAdd->setViewDate( $currentTime );
			$em->persist($policyViewAdd);
			$em->flush();
		}
		$viewCurrent = $policyView->getPolicyViewCurrentByUser( $userId );
		if(!empty($viewCurrent) && is_array($viewCurrent)){
			$viewCurrentSlice = array_slice($viewCurrent, 0, ApplicationConst::NUMBER_POLICY_VIEW );
			foreach($viewCurrentSlice as $key => $value){
				$listIdView[] = $value['id'];
			}
		}
		if(isset($listIdView) && count($viewCurrent) > ApplicationConst::NUMBER_POLICY_VIEW ){
			$policyView->deleteMultiplePolicyView($listIdView, $userId);
		}
		$policyViewStatistic = $em->getRepository('Application\Entity\PolicyViewStatistic');
		$viewStatistic = $policyViewStatistic->findBy( array(
				'policyId' => $policyId,
				'date' => DateHelper::getCurrentDateTime()
				));
		if( is_array($viewStatistic) && ( count($viewStatistic) > 0 ) && isset($viewStatistic[0])){
			$policyViewStatistic->updatePolicyViewStatistic( $policyId, $viewStatistic[0]->getTotalView(), $roleId );
		}else{
			$policyViewStatisticObj = new PolicyViewStatistic();
			$policyViewStatisticObj->setPolicyId( $policyId );
			$policyViewStatisticObj->setTotalView( 1 );
			$policyViewStatisticObj->setRoleId( $roleId );
			$policyViewStatisticObj->setDate( DateHelper::getCurrentDateTime() );
			$em->persist($policyViewStatisticObj);
			$em->flush();
		}
		return true;
	}
}
