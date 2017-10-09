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
use Administrator\Service\ServiceInterface\PolicyManagementServiceInterface;
use Application\Service\PrivateSession;
use Application\Utils\ApplicationConst;
use Application\Utils\DateHelper;
use Application\Utils\UserHelper;
use Doctrine\ORM\EntityManager;
use Zend\Json\Json;

class PolicyManagementController extends BaseController
{
    /**
     * @var \Administrator\Service\PolicyManagementService
     */
    protected $policyService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(PolicyManagementServiceInterface $policyService, EntityManager $em)
    {
        $this->policyService = $policyService;
        $this->em = $em;
    }

    public function saveRefererLink()
    {
        $url = PrivateSession::getData(ApplicationConst::REFERER_PAGE);
        if (empty($url)) {
            if ($this->getRequest()->getHeader('Referer') != false) {
                $uri = $this->getRequest()->getHeader('Referer')->uri()->getPath();
                $query = $this->getRequest()->getHeader('Referer')->uri()->getQuery();
                $url = $uri . (!empty($query) ? '?' . $query : '');
            } else {
                $url = $this->url()->fromRoute('policy-management/default', array('action' => 'index'));
            }
            PrivateSession::setData(ApplicationConst::REFERER_PAGE, $url);
        }
    }

    public function indexAction()
    {
        PrivateSession::clear(ApplicationConst::REFERER_PAGE);
        $currentUrl = $this->getRequest()->getRequestUri();
        $resultPerPage = ApplicationConst::RESULT_PER_PAGE;
        $search = $this->params()->fromQuery();
        $page = isset($search['page']) && intval($search['page']) > 1 ? intval($search['page']) : 1;
        if (UserHelper::isInputRole($this->userInfo['roleId'])) {
            $isDisable = 1;
            $search['ddlBureauId'] = $this->userInfo['bureauId'];
            $search['ddlDepartmentId'] = $this->userInfo['departmentId'];
            if (!empty($this->userInfo['divisionId'])) {
                $search['ddlDivisionId'] = $this->userInfo['divisionId'];
                $glConfig['divisionId'] = $this->policyService->getArrayDivisionByCode($search['ddlDivisionId'], $search['ddlDepartmentId']);
            } else {
                $glConfig['divisionId'] = $this->glConfig['divisionId'];
            }
            $glConfig['bureauId'] = array($search['ddlBureauId'] => $this->glConfig['bureauId'][$search['ddlBureauId']]);
            $glConfig['departmentId'] = array($search['ddlDepartmentId'] => $this->glConfig['departmentId'][$search['ddlDepartmentId']]);

            $glConfig['typePolicy'] = $this->glConfig['typePolicy'];
            $glConfig['typeRecruitmentTime'] = $this->glConfig['typeRecruitmentTime'];
        }

        list($data, $totalPages, $totalResults) = $this->policyService->getPolicyBySearch($search, $page, $resultPerPage);
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
            'messages' => $this->policyService->getMessages(),
        ));
    }

    public function addAction()
    {
        //$this->saveRefererLink();
        $refererUrl = PrivateSession::getData(ApplicationConst::REFERER_PAGE);
        if (UserHelper::isInputRole($this->userInfo['roleId'])) {
            $isDisable = 1;
            $glConfig['bureauId'] = array($this->userInfo['bureauId'] => $this->glConfig['bureauId'][$this->userInfo['bureauId']]);
            $glConfig['departmentId'] = array($this->userInfo['departmentId'] => $this->glConfig['departmentId'][$this->userInfo['departmentId']]);
            if (!empty($this->userInfo['divisionId'])) {
                $glConfig['divisionId'] = $this->policyService->getArrayDivisionByCode($this->userInfo['divisionId'], $this->userInfo['departmentId']);
            } else {
                $glConfig['divisionId'] = $this->glConfig['divisionId'];
            }
            $glConfig['attributePolicyType'] = $this->glConfig['attributePolicyType'];
            $glConfig['supportArea'] = $this->glConfig['supportArea'];
        }
        $attributes = $this->policyService->getAllAttributes();
        return $this->viewModel->setVariables(array(
            'glConfig' => isset($glConfig) ? $glConfig : $this->glConfig,
            'refererUrl' => $refererUrl,
            'attributes' => $attributes,
            'isDisable' => isset($isDisable) ? intval($isDisable) : 0,
            'messages' => $this->policyService->getMessages()
        ));
    }

    public function saveAction()
    {
        $locationLog = getcwd() . "/public/static/performance/save-policy-" . $this->userInfo["username"] . date("Y-m-d") . ".log";
        $messageLog = " \n ======================================== \n";
        $messageLog .= "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Start save policy]\n";
        if (ApplicationConst::DEBUG) @error_log($messageLog, 3, $locationLog);

        $response = array('status' => 0, 'errors' => array(), 'message' => '');
        $params = array_merge_recursive($this->params()->fromPost(), $this->params()->fromFiles());

        if (UserHelper::isInputRole($this->userInfo['roleId'])) {
            $params['ddlBureauId'] = $this->userInfo['bureauId'];
            $params['ddlDepartmentId'] = $this->userInfo['departmentId'];
            $params['ddlDivisionId'] = !empty($params['ddlDivisionId']) ? $params['ddlDivisionId'] : $this->userInfo['divisionId'];
        }
        if (isset($params['policyId'])) {
            $policyData = $this->policyService->getPolicyByIdAndRole(intval($params['policyId']), $this->userInfo);
            if (!$policyData) {
                $response['errors'] = array('system' => $this->translator->translate('MSG_PO_037_Not_Exist_Record'));
                return $this->getResponse()->setContent(Json::encode($response));
            }
        }
        $errorAttributes = $this->policyService->getErrorAttributes($params);
        $params['attributes'] = $this->policyService->collectParamsAttributes($params);
        $errors = array_merge(
            $this->policyService->getErrorRequireInputPolicy($params),
            $errorAttributes,
            $this->policyService->getErrorFormatRecruitmentTime($params),
            $this->policyService->getErrorFileUpload($params),
            $this->policyService->getErrorFormatPublishDate($params)
        );
        if ($errors) {
            $response['errors'] = $errors;
            return $this->getResponse()->setContent(Json::encode($response));
        }

        $this->em->getConnection()->beginTransaction();
        try {
            $params['timeCreate'] = DateHelper::getCurrentTimeStamp();
            $resultUpload = $this->policyService->uploadAttachedFile($params['pdfFile'], $params['timeCreate'], (isset($params['policyId']) ? intval($params['policyId']) : 0));
            if ($resultUpload['status'] == 1) {
                $params['pdfFileArray'] = $resultUpload['pdfFileArray'];
            }
            if (isset($params['policyId'])) {
                if (!empty($params['updateDate']) && DateHelper::convertDateToNumber($params['updateDate']) > $params['timeCreate']) {
                    $this->policyService->saveInfoPolicyTemp(intval($params['policyId']), $params['updateDate']);
                } else {
                    $this->policyService->deletePolicyTemp(intval($params['policyId']));
                }
                $policyId = $this->policyService->updatePolicyEntity(intval($params['policyId']), $params, $this->userInfo);
            } else {
                $policyId = $this->policyService->savePolicyEntity($params, $this->userInfo);
            }
            if ($policyId) {
                $this->policyService->saveAttributesPolicyMapping($params, $policyId);
                $cbRecruitmentFlag = isset($params['cbRecruitmentFlag']) ? intval($params['cbRecruitmentFlag']) : 0;
                $recruitTimes = array(
                    'startDate' => isset($params['startDate']) ? $params['startDate'] : array(),
                    'endDate' => isset($params['endDate']) ? $params['endDate'] : array(),
                    'deadline' => isset($params['deadline']) ? $params['deadline'] : array(),
                );
                $this->policyService->saveRecruitmentTime($policyId, $cbRecruitmentFlag, $recruitTimes, $params);

            }
            $this->em->getConnection()->commit();
            $this->policyService->saveMailContentByListPolicy(array($policyId));
            $this->policyService->generateFileZipPdf($policyId);
            $message = (isset($params['policyId']) && intval($params['policyId']) > 0) ? $this->translator->translate('MSG_PO_014_Edit_Policy_Success') : $this->translator->translate('MSG_PO_014_Add_Policy_Success');
            $response = array('status' => 1, 'message' => $message, 'policyId' => $policyId);

            $messageLog = "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Stop save policy]\n";
            if (ApplicationConst::DEBUG) @error_log($messageLog, 3, $locationLog);

            return $this->getResponse()->setContent(Json::encode($response));
        } catch (\Exception $ex) {
            $this->em->getConnection()->rollback();
            $response['errors'] = array('system' => $ex->getMessage());
            return $this->getResponse()->setContent(Json::encode($response));
        }
    }

    public function successAction()
    {
        $refererUrl = PrivateSession::getData(ApplicationConst::REFERER_PAGE);
        $params = $this->params()->fromRoute();
        /* @var $policy \Application\Entity\Policy */
        $policy = $this->policyService->getPolicyByIdAndRole(
            !empty($params['policyId']) ? intval($params['policyId']) : 0,
            $this->userInfo
        );
        if (!$policy) {
            return $this->notFoundAction();
        }
        $policyArrayData = $this->appService->convertObjectEntityToArray($this->em, 'Application\Entity\Policy', $policy);
        $typePolicy = $this->policyService->getStatusPolicy($policyArrayData);
        $policyId = intval($params['policyId']);

        $attributesPolicy = $this->policyService->getAttributesByPolicy($policyId);
        $recruitmentTime = $this->policyService->getRecruitmentTimeByPolicy($policyId);
        $ddlBureauId = $policy->getBureauId();
        $ddlDepartmentId = $policy->getDepartmentId();
        $ddlDivisionId = $policy->getDivisionId();
        if (UserHelper::isInputRole($this->userInfo['roleId'])) {
            $glConfig['bureauId'] = array($ddlBureauId => $this->glConfig['bureauId'][$ddlBureauId]);
            $glConfig['departmentId'] = array($ddlDepartmentId => $this->glConfig['departmentId'][$ddlDepartmentId]);
            $glConfig['divisionId'] = $this->policyService->getArrayDivisionByCode($ddlDivisionId, $ddlDepartmentId);
            $glConfig['attributePolicyType'] = $this->glConfig['attributePolicyType'];
            $glConfig['supportArea'] = $this->glConfig['supportArea'];
            $glConfig['folderUploadPdf'] = $this->glConfig['folderUploadPdf'];
        }
        $attributes = $this->policyService->getAllAttributes();
        return $this->viewModel->setVariables(array(
            'glConfig' => isset($glConfig) ? $glConfig : $this->glConfig,
            'attributes' => $attributes,
            'attributesPolicy' => $attributesPolicy,
            'recruitmentTime' => $recruitmentTime,
            'policy' => $policy,
            'refererUrl' => $refererUrl,
            'typePolicy' => $typePolicy,
            'messages' => $this->policyService->getMessages()
        ));
    }

    public function editAction()
    {
        $this->saveRefererLink();
        $refererUrl = PrivateSession::getData(ApplicationConst::REFERER_PAGE);
        $params = $this->params()->fromRoute();
        $policy = $this->policyService->getPolicyByIdAndRole(
            !empty($params['policyId']) ? intval($params['policyId']) : 0,
            $this->userInfo
        );
        if (!$policy) {
            return $this->notFoundAction();
        }
        $policyId = intval($params['policyId']);

        $policyArrayData = $this->appService->convertObjectEntityToArray($this->em, 'Application\Entity\Policy', $policy);
        $typePolicy = $this->policyService->getStatusPolicy($policyArrayData);

        $attributesPolicy = $this->policyService->getAttributesByPolicy($policyId);
        $recruitmentTime = $this->policyService->getRecruitmentTimeByPolicy($policyId);
        if (UserHelper::isInputRole($this->userInfo['roleId'])) {
            $isDisable = 1;
            $glConfig['bureauId'] = array($this->userInfo['bureauId'] => $this->glConfig['bureauId'][$this->userInfo['bureauId']]);
            $glConfig['departmentId'] = array($this->userInfo['departmentId'] => $this->glConfig['departmentId'][$this->userInfo['departmentId']]);
            if (!empty($this->userInfo['divisionId'])) {
                $glConfig['divisionId'] = $this->policyService->getArrayDivisionByCode($this->userInfo['divisionId'], $this->userInfo['departmentId']);
            } else {
                $glConfig['divisionId'] = $this->glConfig['divisionId'];
            }
            $glConfig['attributePolicyType'] = $this->glConfig['attributePolicyType'];
            $glConfig['supportArea'] = $this->glConfig['supportArea'];
            $glConfig['folderUploadPdf'] = $this->glConfig['folderUploadPdf'];
        }
        $attributes = $this->policyService->getAllAttributes();
        return $this->viewModel->setVariables(array(
            'glConfig' => isset($glConfig) ? $glConfig : $this->glConfig,
            'refererUrl' => $refererUrl,
            'attributes' => $attributes,
            'attributesPolicy' => $attributesPolicy,
            'recruitmentTime' => $recruitmentTime,
            'policy' => $policy,
            'typePolicy' => $typePolicy,
            'isDisable' => isset($isDisable) ? intval($isDisable) : 0,
            'messages' => $this->policyService->getMessages()
        ));
    }

    public function cloneAction()
    {
        //$this->saveRefererLink();
        $refererUrl = PrivateSession::getData(ApplicationConst::REFERER_PAGE);
        $params = $this->params()->fromRoute();
        $policy = $this->policyService->getPolicyByIdAndRole(
            !empty($params['policyId']) ? intval($params['policyId']) : 0,
            $this->userInfo
        );
        if (!$policy) {
            return $this->notFoundAction();
        }
        $policyId = intval($params['policyId']);

        $attributesPolicy = $this->policyService->getAttributesByPolicy($policyId);
        $recruitmentTime = $this->policyService->getRecruitmentTimeByPolicy($policyId);
        if (UserHelper::isInputRole($this->userInfo['roleId'])) {
            $isDisable = 1;
            $glConfig['bureauId'] = array($this->userInfo['bureauId'] => $this->glConfig['bureauId'][$this->userInfo['bureauId']]);
            $glConfig['departmentId'] = array($this->userInfo['departmentId'] => $this->glConfig['departmentId'][$this->userInfo['departmentId']]);
            if (!empty($this->userInfo['divisionId'])) {
                $glConfig['divisionId'] = $this->policyService->getArrayDivisionByCode($this->userInfo['divisionId'], $this->userInfo['departmentId']);
            } else {
                $glConfig['divisionId'] = $this->glConfig['divisionId'];
            }
            $glConfig['attributePolicyType'] = $this->glConfig['attributePolicyType'];
            $glConfig['supportArea'] = $this->glConfig['supportArea'];
            $glConfig['folderUploadPdf'] = $this->glConfig['folderUploadPdf'];
        }
        $attributes = $this->policyService->getAllAttributes();
        return $this->viewModel->setVariables(array(
            'glConfig' => isset($glConfig) ? $glConfig : $this->glConfig,
            'refererUrl' => $refererUrl,
            'attributes' => $attributes,
            'attributesPolicy' => $attributesPolicy,
            'recruitmentTime' => $recruitmentTime,
            'policy' => $policy,
            'isDisable' => isset($isDisable) ? intval($isDisable) : 0,
            'messages' => $this->policyService->getMessages()
        ));
    }

    public function deleteAction()
    {
        $response = array('status' => 0, 'message' => $this->translator->translate('MSG_PO_037_Not_Exist_Record'));
        $params = $this->params()->fromPost();
        if (empty($params['policyIds'])) {
            return $this->getResponse()->setContent(Json::encode($response));
        }
        $listPolicy = $this->policyService->getPolicyByArrayIdsAndRole($params['policyIds'], $this->userInfo);
        if (!$listPolicy) {
            return $this->getResponse()->setContent(Json::encode($response));
        }
        $policyNames = array();
        foreach ($listPolicy as $policy) {
            $policyNames[] = $policy['name'];
        }
        $result = $this->policyService->deletePolicy($params['policyIds']);
        if ($result) {
            $response['status'] = 1;
            $response['message'] = sprintf($this->translator->translate('MSG_PO_007_Delete_Success'), implode('<br/>- ', $policyNames));
        } else {
            $response['message'] = $this->translator->translate('MSG_PO_000_System_Error');
        }
        return $this->getResponse()->setContent(Json::encode($response));
    }

    public function publicAction()
    {
        $response = array('status' => 0, 'message' => $this->translator->translate('MSG_PO_037_Not_Exist_Record'));
        $params = $this->params()->fromPost();
        if (empty($params['policyIds'])) {
            return $this->getResponse()->setContent(Json::encode($response));
        }
        $listPolicy = $this->policyService->getPolicyByArrayIdsAndRole($params['policyIds'], $this->userInfo);
        if (!$listPolicy) {
            return $this->getResponse()->setContent(Json::encode($response));
        }
        foreach ($listPolicy as $policy) {
            $typePolicy = $this->policyService->getStatusPolicy($policy);
            if (in_array($typePolicy, array(2, 4))) {
                $policyCanPublic[$policy['id']] = $policy['name'];
            } else {
                $policyNotPublic[$policy['id']] = $policy['name'];
            }
        }
        if (!isset($policyCanPublic)) {
            $response['message'] = $this->translator->translate('MSG_PO_008_Error_Not_Exist_Policy_Can_Public');
            return $this->getResponse()->setContent(Json::encode($response));
        }
        $result = $this->policyService->publicPolicy($policyCanPublic);
        if ($result['status']) {
            if ($policyCanPublic) {
                $policyIds = array_keys($policyCanPublic);
                $this->policyService->saveMailContentByListPolicy($policyIds);
            }

            $response['status'] = 1;
            $messages = array();
            $messages[] = sprintf($this->translator->translate('MSG_PO_009_Publish_Policy_Success'), implode('<br/>- ', $policyCanPublic));
            if (isset($policyNotPublic)) {
                $messages[] = sprintf($this->translator->translate('MSG_PO_010_Publish_Policy_Fail'), implode('<br/>- ', $policyNotPublic));
            }
            $response['message'] = implode('<br/>', $messages);
        } else {
            $response['message'] = $this->translator->translate("MSG_PO_000_System_Error");
        }

        return $this->getResponse()->setContent(Json::encode($response));

    }

    public function privateAction()
    {
        $response = array('status' => 0, 'message' => $this->translator->translate('MSG_PO_037_Not_Exist_Record'));
        $params = $this->params()->fromPost();
        if (empty($params['policyIds'])) {
            return $this->getResponse()->setContent(Json::encode($response));
        }
        $listPolicy = $this->policyService->getPolicyByArrayIdsAndRole($params['policyIds'], $this->userInfo);
        if (!$listPolicy) {
            return $this->getResponse()->setContent(Json::encode($response));
        }
        foreach ($listPolicy as $policy) {
            $typePolicy = $this->policyService->getStatusPolicy($policy);
            if (in_array($typePolicy, array(3))) {
                $policyCanPrivate[$policy['id']] = $policy['name'];
            } else {
                $policyNotPrivate[$policy['id']] = $policy['name'];
            }
        }
        if (!isset($policyCanPrivate)) {
            $response['message'] = $this->translator->translate('MSG_PO_011_Error_Not_Exist_Policy_Can_Private');
            return $this->getResponse()->setContent(Json::encode($response));
        }
        $result = $this->policyService->privatePolicy($policyCanPrivate);
        if ($result['status']) {
            $response['status'] = 1;
            $messages = array();
            $messages[] = sprintf($this->translator->translate('MSG_PO_012_Private_Policy_Success'), implode('<br/>- ', $policyCanPrivate));
            if (isset($policyNotPrivate)) {
                $messages[] = sprintf($this->translator->translate('MSG_PO_013_Private_Policy_Fail'), implode('<br/>- ', $policyNotPrivate));
            }
            $response['message'] = implode('<br/>', $messages);
        } else {
            $response['message'] = $this->translator->translate("MSG_PO_000_System_Error");
        }

        return $this->getResponse()->setContent(Json::encode($response));

    }

}
