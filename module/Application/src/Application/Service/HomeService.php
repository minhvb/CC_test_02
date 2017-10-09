<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service;

use Administrator\Service\PolicyManagementService;
use Application\Entity\Repository\PolicyAttributesRepository;
use Application\Entity\Repository\PolicyFavouriteRepository;
use Application\Entity\Repository\PolicyRepository;
use Application\Entity\Repository\RecruitmentTimeRepository;
use Application\Entity\Repository\SearchHistoryRepository;
use Application\Entity\PolicyViewStatistic;
use Application\Entity\SearchHistory;
use Application\Exception\ValidationException;
use Application\Service\ServiceInterface\HomeServiceInterface;
use Application\Utils\ApplicationConst;
use Application\Utils\BrowserHelper;
use Application\Utils\CharsetConverter;
use Application\Utils\CommonUtils;
use Application\Utils\DateHelper;
use Application\Utils\PHPExcel;
use Doctrine\ORM\NoResultException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class HomeService implements HomeServiceInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function checkUserCurrentPassExist($username, $currentPaword) {
        $userRepo = $this->getUserRepository();
        $userInfo = $userRepo->getUserInfo($username);
        $encryptedPassword = CommonUtils::encrypt($currentPaword);
        if ($encryptedPassword != $userInfo['password']) {
            return 0;
        } else return ApplicationConst::LOGIN_SUCCESS;
    }

    public function getUserRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\User');
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    public function updateChangePaword($newPaword) {

    }

    public function queryPublishPolicies($search, $filter = 'new', $page, $resultPerPage, $userId) {
        $policyRepo = $this->getPolicyRepository();
        $policyMntService = $this->getPolicyMntService();

        if ($page < 1) $page = 1;
        $firstResult = ($page - 1) * $resultPerPage;
        // convert policyType
        if(!empty($search['searchPolicyType'])){
            $search['searchPolicyType'] = CommonUtils::convertRecruitmentTimeStatus($search['searchPolicyType']);
        }
        $totalResults = $policyRepo->getNumberPublishPoliciesBySearch($search, $filter, $userId);
        $totalPages = ceil($totalResults / $resultPerPage);

        $policies = $policyRepo->getPublishPoliciesBySearchPaging($search, $firstResult, $resultPerPage, $filter, $userId);
        $policyIds = array_column($policies, 'id');

        $listRecruitmentTime = $policyMntService->getListRecruitmentTimeByMultiPolicy($policyIds);
        $listAttributes = $policyMntService->getListAttributeByMultiPolicy($policyIds);
        foreach ($policies as $key => $policy) {
            if(isset($listRecruitmentTime[$policy['id']]) && $policy['recruitmentStatus'] != ApplicationConst::TYPE_AFTER_RECRUITMENT_TIME) {
                $recruitmentTime = isset($listRecruitmentTime[$policy['id']]) ? $listRecruitmentTime[$policy['id']] : array();
                $attributes = isset($listAttributes[$policy['id']]) ? $listAttributes[$policy['id']] : array();
                list($status, $startRecruitDate, $endRecruitDate) = $policyMntService->getStatusRecruitmentTime($recruitmentTime, $attributes);
                $policies[$key]['startRecruitmentDate'] = $startRecruitDate;
                $policies[$key]['endRecruitmentDate'] = $endRecruitDate;
            }else{
                $policies[$key]['startRecruitmentDate'] = null;
                $policies[$key]['endRecruitmentDate'] = null;
            }
        }
        return array($policies, $totalPages, $totalResults);
    }

    /**
     *
     * @return PolicyRepository
     */
    public function getPolicyRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Policy');
    }

    /**
     *
     * @return PolicyManagementService
     */
    public function getPolicyMntService() {
        return $this->getServiceLocator()->get('Administrator\Service\PolicyManagementServiceInterface');
    }

    /**
     *
     * @return RecruitmentTimeRepository
     */
    public function getRecruitmentRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\RecruitmentTime');
    }

    public function getAllAttributes() {
        return $this->getPolicyAttributesRepository()->getAllAttributes($order = true);
    }

    /**
     *
     * @return PolicyAttributesRepository
     */
    public function getPolicyAttributesRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\PolicyAttributes');
    }

    public function addFavouriteMultiPolicies($userId, $policyIds) {
        if (empty($policyIds)) {
            throw new ValidationException('MSG_HO_002_EmptyPolicyIds');
        }
        $listCurrentFavourite = $this->getPolicyFavouriteRepository()->getListFavouritePolicies($userId);
        $listCurrentFavouriteIds = array_column($listCurrentFavourite, 'policyId');

        $listExistIds = array_intersect($policyIds, $listCurrentFavouriteIds);
        $listNewIds = array_diff($policyIds, $listCurrentFavouriteIds);
        if (empty($listNewIds)) {
            return array($listNewIds, $listExistIds);
        }
        if ((count($listNewIds) + count($listCurrentFavouriteIds)) > ApplicationConst::MAX_FAVOURITE_POLICY) {
            throw new ValidationException('MSG_HO_001_Max20Favourite');
        }

        $this->getPolicyFavouriteRepository()->insertMultiFavourite($userId, $listNewIds);

        return array($listNewIds, $listExistIds);
    }

    public function removeFavouritePolicies($userId, $policyIds) {
        if (empty($policyIds)) {
            throw new ValidationException('MSG_HO_002_EmptyPolicyIds');
        }

        $this->getPolicyFavouriteRepository()->deleteMultiFavourite($userId, $policyIds);

        return true;
    }

    public function addSearchHistory($userId, $name, $searchParams, $policyId = null) {
        if (empty($userId) || empty($name) || empty($searchParams)) {
            throw new ValidationException('MSG_HO_005_HistoryNameEmpty');
        }

        $listHistory = $this->getSearchHistoryRepository()->getListHistories($userId);
        if(!empty($listHistory) && in_array($name, array_column($listHistory, 'name'))){
            throw new ValidationException('MSG_HO_007_DuplicateHistoryName');
        }

        if(count($listHistory) == 5 && !empty($policyId) && in_array($policyId, array_column($listHistory, 'id'))){
            $this->getSearchHistoryRepository()->deleteHistory($userId, $policyId);
            unset($listHistory[$policyId]);
        }

        if(count($listHistory) >= 5){
            throw new ValidationException('MSG_HO_008_MaxRecordHistory');
        }

        $em = $this->getEntityManager();
        $history = new SearchHistory();
        $history->setUserId($userId);
        $history->setName($name);
        $history->setContent($searchParams);
        $em->persist($history);
        $em->flush();
        $em->clear();

        return true;
    }

    public function getListHistories($userId){
        return $this->getSearchHistoryRepository()->getListHistories($userId);
    }

    public function getHistory($userId, $historyId){
        return $this->getSearchHistoryRepository()->getHistoryById($userId,$historyId);
    }

    /**
     *
     * @return PolicyFavouriteRepository
     */
    public function getPolicyFavouriteRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\PolicyFavourite');
    }

    /**
     *
     * @return SearchHistoryRepository
     */
    public function getSearchHistoryRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\SearchHistory');
    }

    public function getDataComparePolicy($response, $policyIds, $baseUrl){
        if(empty($policyIds) || !is_array($policyIds)){
            throw new ValidationException('Chưa chọn policy compare');
        }

        if(count($policyIds) > ApplicationConst::MAX_COMPARE_POLICY || count($policyIds) < ApplicationConst::MIN_COMPARE_POLICY){
            throw new ValidationException('MSG_HO_010_MaxMinCompare');
        }

        $data = $this->getPolicyRepository()->getPoliciesForCompare($policyIds);

        $exportData =  array();
        $glConfig = $this->getServiceLocator()->get('Config');
        $attributeConfig = $glConfig['attributePolicyType'];
        $headers = $glConfig['exportComparePoliciesHeader'];
        foreach ($data as $policy){
            if(!isset($exportData[$policy['id']])){
                $exportData[$policy['id']] = $this->createPolicyTemplate($policy, $headers, $baseUrl);
            }
            $attributeName = $attributeConfig[$policy['attributeType']]['name'];
            $exportData[$policy['id']][$attributeName] = $policy['attributeValue'];
            foreach ($exportData[$policy['id']] as $key => $value){
                if(!in_array($key, $headers)){
                    unset($exportData[$policy['id']][$key]);
                }
            }
            $exportData[$policy['id']] = array_replace(array_flip(array_values($headers)), $exportData[$policy['id']]);
        }

        $objFileName = new CharsetConverter();
        $filename = "施策比較表_" . date('Ymd') . '.xls';
        $browser = BrowserHelper::getBrowserName($_SERVER['HTTP_USER_AGENT']);
        $filename = $browser != BrowserHelper::INTERNET_EXPLORER ? $objFileName->toUtf8($filename) : $objFileName->utf8ToShiftJis($filename);

        PHPExcel::exportCompare($exportData, $filename, 'exportCompare', 1, '', 'xls', array(), $setBorder = true);
        return $response;
    }

    private function createPolicyTemplate($policy, $headers, $baseUrl){
        $exportPolicy = array();
        $urlTemplate = "$baseUrl/policy/detail/";
        foreach ($headers as $key => $header){
            if($header == 'shortName'){
                $exportPolicy[$header] = array(
                    'url' => $urlTemplate . $policy['id'],
                    'value' => isset($policy[$header]) ? $policy[$header] : ''
                );
            }else{
                $exportPolicy[$header] = isset($policy[$header]) ? $policy[$header] : '';
            }
        }
        return $exportPolicy;
    }
    
    public function updateDownloadPolicyStatistic($policyId, $roleId){
    	$em = $this->getEntityManager();
    	$policyViewStatistic = $em->getRepository('Application\Entity\PolicyViewStatistic');
    	$viewStatistic = $policyViewStatistic->findBy( array(
    			'policyId' => $policyId,
    			'date' => DateHelper::getCurrentDateTime()
    	));
    	if( is_array($viewStatistic) && ( count($viewStatistic) > 0 ) && isset($viewStatistic[0])){
    		$policyViewStatistic->updatePolicyViewStatisticDownload( $policyId, $viewStatistic[0]->getTotalDownloadPDF(), $roleId );
    	}else{
    		$policyViewStatisticObj = new PolicyViewStatistic();
    		$policyViewStatisticObj->setPolicyId( $policyId );
    		$policyViewStatisticObj->setTotalDownloadPDF( 1 );
    		$policyViewStatisticObj->setRoleId( $roleId );
    		$policyViewStatisticObj->setDate( new \DateTime(date('Y-m-d')) );
    		$em->persist($policyViewStatisticObj);
    		$em->flush();
    	}
    	return true;
    }
}
