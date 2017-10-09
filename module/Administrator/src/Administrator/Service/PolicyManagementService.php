<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administrator\Service;

use Administrator\Service\ServiceInterface\PolicyManagementServiceInterface;
use Application\Entity\MailContent;
use Application\Entity\Policy;
use Application\Utils\ApplicationConst;
use Application\Utils\CommonUtils;
use Application\Utils\DateHelper;
use Zend\Json\Json;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Application\Utils\UserHelper;

class PolicyManagementService implements PolicyManagementServiceInterface, ServiceLocatorAwareInterface
{

    const POLICY_EXPIRE_SOON_TIME = 7 * 24 * 60 * 60; // 7 days
    const POLICY_BEFORE_SOON_TIME = 14 * 24 * 60 * 60; // 14 days
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
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    public function collectParamsAttributes($params)
    {
        $attributes = array();
        if (isset($params['attributes']) && is_array($params['attributes'])) {
            foreach ($params['attributes'] as $singleAttributeArray) {
                $attributes = array_merge($attributes, array_values($singleAttributeArray));
            }
        }

        if (!empty($params['attributesSelect']) && is_array($params['attributesSelect'])) {
            foreach ($params['attributesSelect'] as $value) {
                if ($value != '' || intval($value) > 0) {
                    $attributes[] = $value;
                }
            }
        }
        return $attributes;
    }

    public function getPolicyBySearch($search, $page, $resultPerPage)
    {
        $em = $this->getEntityManager();
        $totalResults = $em->getRepository('\Application\Entity\Policy')->getTotalResultBySearch($search);

        $totalPages = ceil($totalResults / $resultPerPage);

        $data = $em->getRepository('\Application\Entity\Policy')->getPolicyBySearchAndPage($search, $page, $resultPerPage);
        foreach ($data as $policy) {
            $policyIds[] = $policy['id'];
        }
        if (isset($policyIds) && is_array($policyIds)) {
            $listRecruitmentTime = $this->getListRecruitmentTimeByMultiPolicy($policyIds);
            $listAttributes = $this->getListAttributeByMultiPolicy($policyIds);
        }
        foreach ($data as $key => $policy) {
            $recruitmentTime = isset($listRecruitmentTime[$policy['id']]) ? $listRecruitmentTime[$policy['id']] : array();
            $attributes = isset($listAttributes[$policy['id']]) ? $listAttributes[$policy['id']] : array();
            $typePolicy = $this->getStatusPolicy($policy);
            list($typeRecruitmentTime, $startDate, $endDate) = $this->getStatusRecruitmentTime($recruitmentTime, $attributes);
            if ($typePolicy) {
                $data[$key]['typePolicy'] = $typePolicy;
                $data[$key]['typeRecruitmentTime'] = $typeRecruitmentTime;
            }
            $totalUserResponse = $em->getRepository('\Application\Entity\Response')->getTotalUserByPolicy($policy['id']);
            $data[$key]['totalUserResponse'] = $totalUserResponse;
        }
        return array($data, $totalPages, $totalResults);
    }

    public function getListRecruitmentTimeByMultiPolicy($policyIds)
    {
        $listRecruitmentTime = array();
        $em = $this->getEntityManager();
        $dataRecruitmentTime = $em->getRepository('Application\Entity\RecruitmentTime')->getRecruitmentTimeByMultiPolicy($policyIds);
        if ($dataRecruitmentTime) {
            foreach ($dataRecruitmentTime as $value) {
                $listRecruitmentTime[$value['policyId']][] = $value;
            }
        }
        return $listRecruitmentTime;
    }

    public function getListAttributeByMultiPolicy($policyIds)
    {
        $listAttributes = array();
        $em = $this->getEntityManager();
        $dataAttributes = $em->getRepository('Application\Entity\PolicyAttributeMapping')->getAttributeByMultiPolicy($policyIds);
        if ($dataAttributes) {
            foreach ($dataAttributes as $value) {
                $listAttributes[$value['policyId']][] = $value['attributesPolicyId'];
            }
        }
        return $listAttributes;
    }

    public function getStatusPolicy($policy)
    {
        $policy['publishStartdate'] = intval($policy['publishStartdate']);
        $policy['publishEnddate'] = intval($policy['publishEnddate']);
        $dateTime = new \DateTime('now');
        if ($policy['isDraft'] == 1) {
            return ApplicationConst::POLICY_TYPE_EDITING;
        }

        if ($policy['publishStartdate'] == 0 && $policy['publishEnddate'] > 0 && $policy['publishEnddate'] > $dateTime->getTimestamp()) {
            return ApplicationConst::POLICY_TYPE_WAITING_PUBLIC;
        } else if ($policy['publishStartdate'] == 0 && $policy['publishEnddate'] == 0) {
            return ApplicationConst::POLICY_TYPE_WAITING_PUBLIC;
        } else if (($policy['publishStartdate'] > 0 && $policy['publishStartdate'] > $dateTime->getTimestamp())) {
            return ApplicationConst::POLICY_TYPE_WAITING_PUBLIC;
        }

        if ($policy['publishStartdate'] > 0 && $policy['publishEnddate'] > 0 && $policy['publishStartdate'] <= $dateTime->getTimestamp() && $dateTime->getTimestamp() <= $policy['publishEnddate']) {
            return ApplicationConst::POLICY_TYPE_PUBLIC;
        } else if ($policy['publishEnddate'] == 0 && $policy['publishStartdate'] != 0 && $policy['publishStartdate'] <= $dateTime->getTimestamp()) {
            return ApplicationConst::POLICY_TYPE_PUBLIC;
        }

        if ($policy['publishEnddate'] > 0 && $dateTime->getTimestamp() > $policy['publishEnddate']) {
            return ApplicationConst::POLICY_TYPE_PRIVATE;
        }
        return false;
    }

    public function getStatusRecruitmentTime($dataRecruitment, $attributes, $hasDeadline = false)
    {
        $status = false;
        if (!$dataRecruitment) {
            return array($status, null, null);
        }
        $currentTime = DateHelper::getCurrentTimeStamp();
        $startRecruitDate = null;
        $endRecruitDate = null;
        foreach ($dataRecruitment as $value) {
            $arrStartDate[] = $value['startDate'];
            $arrEndDate[] = $value['endDate'];
            $arrEndDate[] = $value['deadline'];
            if (in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_1, $attributes)) {
                if ($currentTime >= $value['startDate']) {
                    if ($value['deadline'] && $currentTime <= $value['deadline']) {
                        $status = ApplicationConst::TYPE_IN_RECRUITMENT_TIME;
                        $startRecruitDate = $value['startDate'];
                        $endRecruitDate = $value['deadline'];
                        break;
                    } else if ($value['endDate'] && !$value['deadline'] && $currentTime <= $value['endDate']) {
                        $status = ApplicationConst::TYPE_IN_RECRUITMENT_TIME;
                        $startRecruitDate = $value['startDate'];
                        $endRecruitDate = $value['endDate'];
                        break;
                    }
                } else {
                    $startRecruitDate = !isset($startRecruitDate) ? $value['startDate'] : $startRecruitDate;
                    $endRecruitDate = !isset($endRecruitDate) ? ($value['deadline'] ? $value['deadline'] : $value['endDate']) : $endRecruitDate;
                }
            } else if (in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_2, $attributes)) {
                if ($currentTime >= $value['startDate'] && $currentTime <= $value['endDate']) {
                    $status = ApplicationConst::TYPE_IN_RECRUITMENT_TIME;
                    $startRecruitDate = $value['startDate'];
                    $endRecruitDate = $value['endDate'];
                    break;
                } else {
                    $startRecruitDate = !isset($startRecruitDate) ? $value['startDate'] : $startRecruitDate;
                    $endRecruitDate = !isset($endRecruitDate) ? $value['endDate'] : $endRecruitDate;
                }
            } else {
                if ($currentTime >= $value['startDate']) {
                    if ($value['deadline'] && $currentTime <= $value['deadline']) {
                        $status = ApplicationConst::TYPE_IN_RECRUITMENT_TIME;
                        $startRecruitDate = $value['startDate'];
                        $endRecruitDate = $value['deadline'];
                        break;
                    } else if ($value['endDate'] && !$value['deadline'] && $currentTime <= $value['endDate']) {
                        $status = ApplicationConst::TYPE_IN_RECRUITMENT_TIME;
                        $startRecruitDate = $value['startDate'];
                        $endRecruitDate = $value['endDate'];
                        break;
                    } else {
                        $startRecruitDate = !isset($startRecruitDate) ? $value['startDate'] : $startRecruitDate;
                        $endRecruitDate = !isset($endRecruitDate) ? ($value['deadline'] ? $value['deadline'] : $value['endDate']) : $endRecruitDate;
                    }
                }
            }

        }

        // recruitment time will expire around 7 days
        if ($hasDeadline && $status == ApplicationConst::TYPE_IN_RECRUITMENT_TIME
            && $endRecruitDate > 0 && ($endRecruitDate - $currentTime) <= self::POLICY_EXPIRE_SOON_TIME
        ) {
            $status = ApplicationConst::TYPE_RECRUITMENT_EXPIRE_SOON;
        }

        if ($status) {
            return array($status, $startRecruitDate, $endRecruitDate);
        }
        if (!$arrStartDate || !$arrEndDate) {
            return array($status, null, null);
        }
        if ($currentTime > max($arrEndDate)) {
            return array(ApplicationConst::TYPE_AFTER_RECRUITMENT_TIME, null, null);
        }
        return array(ApplicationConst::TYPE_BEFORE_RECRUITMENT_TIME, $startRecruitDate, $endRecruitDate);
    }

    public function getAllAttributes()
    {
        $attributes = array();
        $em = $this->getEntityManager();
        $data = $em->getRepository('Application\Entity\PolicyAttributes')->getAllAttributes();
        foreach ($data as $key => $value) {
            $attributes[$value['attributeType']][$value['id']] = $value;
        }
        return $attributes;
    }

    public function getErrorRequireInputPolicy($params)
    {
        $translator = $this->getTranslator();
        $arrFieldCheckEmpty = array(
            'ddlBureauId', 'txtShortName',
            'txtName', 'txtPurpose', 'txtDetailOfSupportArea', 'txtContent',
            'txtHomepage', 'txtContact'
        );
        $arrFieldCheckFullSize = array(
//            array('name' => 'txtShortName', 'length' => 20),
//            array('name' => 'txtName', 'length' => 50),
//            array('name' => 'txtPurpose', 'length' => 1000),
//            array('name' => 'txtDetailOfSupportArea', 'length' => 1000),
//            array('name' => 'txtContent', 'length' => 2000),
//            array('name' => 'txtContact', 'length' => 500),
        );
        foreach ($arrFieldCheckEmpty as $field) {
            if (empty($params[$field])) {
                $errors[$field] = $translator->translate('MSG_PO_001_Required_Field');
            }
        }

        foreach ($arrFieldCheckFullSize as $field) {
            if (!empty($params[$field['name']]) && !CommonUtils::validateFieldFullSizeAndLengh($params[$field['name']], $field['length'])) {
                $errors[$field['name']] = sprintf($translator->translate('MSG_PO_034_Error_FullSize_And_Length'), $field['length']);
            }
        }

        return isset($errors) ? $errors : array();
    }

    public function getErrorAttributes($params)
    {
        $glConfig = $this->getServiceLocator()->get('Config');
        $translator = $this->getTranslator();
        if (empty($params['attributes']) || !is_array($params['attributes']) || count($params['attributes']) < 1) {
            $errors['attributes'] = $translator->translate('MSG_PO_001_Required_Field');
        }
        foreach ($glConfig['attributePolicyType'] as $key => $value) {
            if (isset($value['displayCreate']) && in_array($value['displayCreate'], array(1, 2))) {
                if (isset($value['required']) && $value['required'] == 1) {
                    if (isset($value['templateCreate']) && $value['templateCreate'] == 2) {
                        if (empty($params['attributesSelect'][$key])) {
                            $errors['attributesSelect[' . $key . ']'] = $translator->translate('MSG_PO_001_Required_Field');
                            break;
                        }
                    } else {
                        if (empty($params['attributes'][$key])) {
                            $errors['attributes[' . $key . ']'] = $translator->translate('MSG_PO_001_Required_Field');
                            break;
                        }
                    }
                }
            }
        }
        return isset($errors) ? $errors : array();
    }

    public function getErrorFormatRecruitmentTime($params)
    {
        //$translator = $this->getTranslator();
        if (isset($params['cbRecruitmentFlag']) && $params['cbRecruitmentFlag'] == 1) {
            return array();
        } else if (in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_3, $params['attributes'])) {
            return array();
        } else if (in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_2, $params['attributes'])) {
            //todo validate server
        } else if (in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_1, $params['attributes'])) {
            //todo validate server
        }
        return isset($errors) ? $errors : array();
    }

    public function getErrorFileUpload($params)
    {
        $translator = $this->getTranslator();
        $acceptable = array(
            'pdf'
        );
        if (empty($params['pdfFile']) || !is_array($params['pdfFile']) || empty($params['pdfFile'][0]) || (count($params['pdfFile']) == 1 && $params['pdfFile'][0]['error'] == 4)) {
            return array();
        }

        foreach ($params['pdfFile'] as $key => $file) {
            $extension = isset($file["name"]) ? strtolower(pathinfo($file["name"], PATHINFO_EXTENSION)) : '';
            if (!in_array($extension, $acceptable)) {
                $errors['pdfFile'] = $translator->translate('MSG_PO_004_Error_File_Wrong_Format_PDF');
            }
            if ($file['size'] > ApplicationConst::FILE_PDF_UPLOAD_LIMIT || $file['error'] == UPLOAD_ERR_INI_SIZE) {
                $errors['pdfFile'] = $translator->translate('MSG_PO_003_Error_File_Too_Large');
            } else if ($file['size'] == 0) {
                $errors['pdfFile'] = $translator->translate('MSG_PO_035_Error_File_Too_Small');
            }
        }
        return isset($errors) ? $errors : array();
    }

    public function getErrorFormatPublishDate($params)
    {
        $translator = $this->getTranslator();

        if (!empty($params['datePublishStartdate'])) {
            if (!DateHelper::isCorrectFormatDate($params['datePublishStartdate'], DateHelper::DATE_TIME_FORMAT)) {
                $errors['datePublishStartdate'] = $translator->translate('MSG_PO_005_Error_Publish_Date_Format');
            }
        }
        if (!empty($params['datePublishEnddate'])) {
            if (!DateHelper::isCorrectFormatDate($params['datePublishEnddate'], DateHelper::DATE_TIME_FORMAT)) {
                $errors['datePublishEnddate'] = $translator->translate('MSG_PO_005_Error_Publish_Date_Format');
            }
        }
        if (!empty($params['datePublishStartdate']) && !empty($params['datePublishEnddate'])) {
            if (DateHelper::isCorrectFormatDate($params['datePublishStartdate'], DateHelper::DATE_TIME_FORMAT) && DateHelper::isCorrectFormatDate($params['datePublishEnddate'], DateHelper::DATE_TIME_FORMAT)) {
                if ($params['datePublishStartdate'] > $params['datePublishEnddate']) {
                    $errors['datePublishStartdate'] = $translator->translate('MSG_PO_006_Error_Publish_Start_Greater_End');
                }
            }
        }
        return isset($errors) ? $errors : array();
    }

    public function uploadAttachedFile($pdfFile, $timeCreate, $policyId = 0)
    {
        if ($policyId > 0) {
            $em = $this->getEntityManager();
            /* @var $policy \Application\Entity\Policy */
            $policy = $em->getRepository('Application\Entity\Policy')->find($policyId);
            if (!$policy) {
                return array('status' => 0, 'message' => 'Do Not Exist This Policy');
            }
            $timeCreate = $policy->getCreateDate();
        }
        $glConfig = $this->getServiceLocator()->get('Config');
        if (!is_array($pdfFile) || empty($pdfFile[0]) || (count($pdfFile) == 1 && $pdfFile[0]['error'] == 4)) {
            return array('status' => 0, 'message' => '', 'pdfFileArray' => array());
        }
        $pdfArray = array();
        try {
            foreach ($pdfFile as $key => $file) {
                $pathInfo = pathinfo($file['name']);
                $pathYearMonthDate = date('Y', $timeCreate) . '/' . date('md', $timeCreate);
                $dirUpload = $_SERVER['DOCUMENT_ROOT'] . '/' . $glConfig['folderUploadPdf'] . '/' . $pathYearMonthDate;
                if (!file_exists($dirUpload)) {
                    mkdir($dirUpload, 0777, true);
                }
                $baseFilename = str_replace(' ', '', $pathInfo['filename']) . '.' . $pathInfo['extension'];
                $fullPathFilename = $dirUpload . '/' . $baseFilename;
                @move_uploaded_file($file['tmp_name'], $fullPathFilename);
                $pdfArray[] = $pathYearMonthDate . '/' . $baseFilename;
            }
            return array('status' => 1, 'message' => 'success', 'pdfFileArray' => $pdfArray);
        } catch (\Exception $ex) {
            return array('status' => 0, 'message' => $ex->getMessage());
        }
    }

    public function savePolicyEntity($data, $userInfo)
    {
        try {
            $em = $this->getEntityManager();
            $policy = new Policy();
            $arrFilePdfFromDB = !empty($data['pdfClone']) ? $data['pdfClone'] : array();
            $pdfFileArray = isset($data['pdfFileArray']) ? array_merge($data['pdfFileArray'], $arrFilePdfFromDB) : $arrFilePdfFromDB;
            $policy->setBureauId($data['ddlBureauId']);
            $policy->setDepartmentId($data['ddlDepartmentId']);
            $policy->setDivisionId($data['ddlDivisionId']);
            $policy->setShortName($data['txtShortName']);
            $policy->setName($data['txtName']);
            $policy->setRecruitmentFlag(isset($data['cbRecruitmentFlag']) ? intval($data['cbRecruitmentFlag']) : NULL);
            $policy->setPurpose($data['txtPurpose']);
            $policy->setDetailOfSupportArea($data['txtDetailOfSupportArea']);
            $policy->setContent($data['txtContent']);
            $policy->setDetailRecruitmentTime(!empty($data['txtDetailRecruitmentTime']) ? $data['txtDetailRecruitmentTime'] : NULL);
            $policy->setHomepage($data['txtHomepage']);
            $policy->setPdfFile(Json::encode($pdfFileArray));
            $policy->setContact($data['txtContact']);
            $policy->setIsDraft(isset($data['isDraft']) ? intval($data['isDraft']) : 0);
            $policy->setCreateDate(isset($data['timeCreate']) ? intval($data['timeCreate']) : DateHelper::getCurrentTimeStamp());
            $policy->setUpdateDate(isset($data['timeCreate']) ? intval($data['timeCreate']) : DateHelper::getCurrentTimeStamp());
            $policy->setPublishStartdate(!empty($data['datePublishStartdate']) ? DateHelper::convertDateToNumber($data['datePublishStartdate']) : NULL);
            $policy->setPublishEnddate(!empty($data['datePublishEnddate']) ? DateHelper::convertDateToNumber($data['datePublishEnddate']) : NULL);
            if (isset($data['attributes']) && is_array($data['attributes']) && in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_1, $data['attributes'])) {
                $policy->setRecruitmentForm(1);
            }
            $policy->setAttentionFlag(isset($data['cbAttentionFlag']) ? intval($data['cbAttentionFlag']) : NULL);
            if (isset($data['cbEmailNotificationFlag']) && $data['cbEmailNotificationFlag'] == 1) {
                $policy->setEmailNotificationFlag(1);
                $policy->setEmailSettingDate(DateHelper::getCurrentTimeStamp());
            }
            $policy->setRoleId($userInfo['roleId']);
            $policy->setCreateBy($userInfo ? $userInfo['username'] : 'SYSTEM');
            $em->persist($policy);
            $em->flush();
            return $policy->getId();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function updatePolicyEntity($policyId, $data, $userInfo)
    {
        try {
            $em = $this->getEntityManager();
            /* @var $policy \Application\Entity\Policy */
            $policy = $em->getRepository('Application\Entity\Policy')->find($policyId);
            if (!$policy) {
                return false;
            }
            $arrFilePdfFromDB = !empty($policy->getPdfFile()) ? Json::decode($policy->getPdfFile(), true) : array();
            $pdfFileArray = isset($data['pdfFileArray']) ? array_merge($data['pdfFileArray'], $arrFilePdfFromDB) : $arrFilePdfFromDB;
            $policy->setBureauId($data['ddlBureauId']);
            $policy->setDepartmentId($data['ddlDepartmentId']);
            $policy->setDivisionId($data['ddlDivisionId']);
            $policy->setShortName($data['txtShortName']);
            $policy->setName($data['txtName']);
            $policy->setRecruitmentFlag(isset($data['cbRecruitmentFlag']) ? intval($data['cbRecruitmentFlag']) : NULL);
            $policy->setPurpose($data['txtPurpose']);
            $policy->setDetailOfSupportArea($data['txtDetailOfSupportArea']);
            $policy->setContent($data['txtContent']);
            $policy->setDetailRecruitmentTime(!empty($data['txtDetailRecruitmentTime']) ? $data['txtDetailRecruitmentTime'] : NULL);
            $policy->setHomepage($data['txtHomepage']);
            $policy->setPdfFile(Json::encode($pdfFileArray));
            $policy->setContact($data['txtContact']);
            $policy->setIsDraft(isset($data['isDraft']) ? intval($data['isDraft']) : 0);
            $policy->setSummaryUpdate(isset($data['txtSummaryUpdate']) ? $data['txtSummaryUpdate'] : '');
            $policy->setUpdateDateSchedule(!empty($data['updateDate']) ? DateHelper::convertDateToNumber($data['updateDate']) : NULL);
            $policy->setUpdateDateDisplay(!empty($data['updateDateDisplay']) ? DateHelper::convertDateToNumber($data['updateDateDisplay']) : NULL);
            if (isset($data['datePublishStartdate']) || isset($data['datePublishEnddate'])) {
                $policy->setPublishStartdate(!empty($data['datePublishStartdate']) ? DateHelper::convertDateToNumber($data['datePublishStartdate']) : NULL);
                $policy->setPublishEnddate(!empty($data['datePublishEnddate']) ? DateHelper::convertDateToNumber($data['datePublishEnddate']) : NULL);
            }
            if (isset($data['attributes']) && is_array($data['attributes']) && in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_1, $data['attributes'])) {
                $policy->setRecruitmentForm(1);
            } else {
                $policy->setRecruitmentForm(0);
            }
            $policy->setAttentionFlag(isset($data['cbAttentionFlag']) ? intval($data['cbAttentionFlag']) : NULL);
            if (isset($data['cbEmailNotificationFlag']) && $data['cbEmailNotificationFlag'] == 1 && $policy->getEmailNotificationFlag() != 1) {
                $policy->setEmailSettingDate(DateHelper::getCurrentTimeStamp());
            }
            $policy->setEmailNotificationFlag(isset($data['cbEmailNotificationFlag']) ? intval($data['cbEmailNotificationFlag']) : NULL);
            $policy->setRoleId($userInfo['roleId']);
            $policy->setUpdateBy($userInfo ? $userInfo['username'] : 'SYSTEM');
            $em->persist($policy);
            $em->flush();
            return $policy->getId();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function saveAttributesPolicyMapping($params, $policyId)
    {
        try {
            $data = isset($params['attributes']) ? $params['attributes'] : array();
            $isSentMail = isset($params['cbEmailNotificationFlag']) && $params['cbEmailNotificationFlag'] == 1 ? $params['cbEmailNotificationFlag'] : 0;
            $em = $this->getEntityManager();
            $attributes = $em->getRepository('Application\Entity\PolicyAttributes')->getAttributesByMultiIds($data);
            if (!$attributes) {
                return array('status' => 0, 'message' => 'FAIL');
            }
            $dataAttributes = array();
            foreach ($attributes as $values) {
                $dataAttributes[] = array(
                    'policyId' => $policyId,
                    'attributesPolicyId' => $values['id'],
                    'attributeType' => $values['attributeType'],
                    'isSentMail' => $isSentMail
                );
            }
            $em->getRepository('Application\Entity\PolicyAttributeMapping')->deleteAttributesByPolicy($policyId);
            $arrFields = array('policyId', 'attributesPolicyId', 'attributeType', 'isSentMail', 'createDate', 'updateDate');
            $em->getRepository('Application\Entity\PolicyAttributeMapping')->insertMultiRows($arrFields, $dataAttributes);
            return array('status' => 1, 'message' => 'SUCCESS');
        } catch (\Exception $ex) {
            return array('status' => 0, 'message' => $ex->getMessage());
        }
    }

    public function saveMailContent($policyId, $userInfo)
    {
        try {
            $em = $this->getEntityManager();
            /* @var $policy \Application\Entity\Policy */
            $policy = $em->getRepository('Application\Entity\Policy')->find($policyId);
            if (!$policy) {
                return false;
            }
            if ($policy->getEmailNotificationFlag() && $policy->getIsDraft() != 1) {
                $mailContent = $em->getRepository('Application\Entity\MailContent')->findOneBy(array(
                    'policyId' => $policyId,
                    'userId' => $userInfo['userId']
                ));
                if (!$mailContent) {
                    $mailContent = new MailContent();
                    $mailContent->setUserId($userInfo['userId']);
                    $mailContent->setPolicyId($policyId);
                    if (!empty($policy->getUpdateDate())) {
                        $createTime = $policy->getUpdateDate();
                    } else if (!empty($policy->getPublishStartdate())) {
                        $createTime = $policy->getUpdateDate();
                    } else {
                        $createTime = DateHelper::getCurrentTimeStamp();
                    }
                    $mailContent->setCreateDate($createTime);
                    $em->persist($mailContent);
                    $em->flush();
                }
            } else {
                return false;
            }
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function saveMailContentByListPolicy($policyIds)
    {
        $response = array('status' => 0, 'message' => 'Fail');
        if (!isset($policyIds) || !is_array($policyIds)) {
            return $response;
        }
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            /* @var $mailContentRepo \Application\Entity\Repository\MailContentRepository */
            $mailContentRepo = $em->getRepository('Application\Entity\MailContent');
            $mailContentRepo->deleteDataByArrayPolicy($policyIds);
            list($attrSearch, $attributesPolicy) = $this->getDataAttributeByListPolicy($policyIds);

            $settingsMail = $this->getDataSettingMailByListAttributes($attrSearch);

            $mailContentByAttribute = $this->collectMailContentByAttributeAndSettingMail($attributesPolicy, $settingsMail);
            $mailContentByFavourite = $this->collectMailContentByPolicyFavourite($policyIds);
            $mailContent = $this->generateDataMailContentToInsert($mailContentByAttribute, $mailContentByFavourite);

            $mailContentRepo->insertMultiData($mailContent);
            $em->getConnection()->commit();

            $response['status'] = 1;
            $response['message'] = 'Success';
        } catch (\Exception $ex) {
            $em->getConnection()->rollBack();
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    public function generateDataMailContentToInsert($mailContentByAttribute, $mailContentByFavourite)
    {
        $listUpdateDatePolicy = array();
        $em = $this->getEntityManager();
        $policyIds = array();
        if (!$mailContentByAttribute && !$mailContentByFavourite) {
            return false;
        }
        $mailContent = array_merge_recursive($mailContentByAttribute, $mailContentByFavourite);
        $mailContent = array_unique($mailContent, SORT_REGULAR);
        foreach ($mailContent as $key => $value) {
            $policyIds[] = $value['policyId'];
        }
        if (!$policyIds) {
            return false;
        }
        $listPolicy = $em->getRepository('Application\Entity\Policy')->getDataByArrayPolicy($policyIds);

        foreach ($listPolicy as $key => $value) {
            $listUpdateDatePolicy[$value['id']] = $value['updateDateSchedule'];
        }
        foreach ($mailContent as $key => $value) {
            $mailContent[$key]['createDate'] = !empty($listUpdateDatePolicy[$value['policyId']]) ? $listUpdateDatePolicy[$value['policyId']] : DateHelper::getCurrentTimeStamp();
            $mailContent[$key]['updateDate'] = !empty($listUpdateDatePolicy[$value['policyId']]) ? $listUpdateDatePolicy[$value['policyId']] : DateHelper::getCurrentTimeStamp();
        }
        return $mailContent;
    }

    public function collectMailContentByAttributeAndSettingMail($attributesPolicy, $settingsMail)
    {
        $mailContent = array();
        if (!$attributesPolicy || !$settingsMail) {
            return array();
        }
        foreach ($attributesPolicy as $policyId => $attribute) {
            foreach ($settingsMail as $userId => $setting) {
                $flagMatch = 0;
                foreach ($setting as $attrType => $listAttribute) {
                    if (!empty($attribute[$attrType])) {
                        $arrayCheckCorrect = array_intersect($attribute[$attrType], $setting[$attrType]);
                        if (is_array($arrayCheckCorrect) && count($arrayCheckCorrect) > 0) {
                            $flagMatch++;
                        }
                    }
                }
                if ($flagMatch == count($setting)) {
                    array_push($mailContent, array('userId' => $userId, 'policyId' => $policyId));
                }
            }
        }
        return $mailContent;
    }

    public function collectMailContentByPolicyFavourite($policyIds)
    {
        $mailContent = array();
        $userIds = array();
        $em = $this->getEntityManager();
        $settingFavourite = $em->getRepository('Application\Entity\SettingMail')->getDataByAttributeFavourite();
        if (!$settingFavourite) {
            return $mailContent;
        }
        foreach ($settingFavourite as $key => $setting) {
            $userIds[] = $setting['userId'];
        }
        $policysFavourite = $em->getRepository('Application\Entity\PolicyFavourite')->getDataByListPolicyAndListUser($policyIds, $userIds);
        if (!$policysFavourite) {
            return $mailContent;
        }
        foreach ($policysFavourite as $key => $value) {
            array_push($mailContent, array('userId' => $value['userId'], 'policyId' => $value['policyId']));
        }
        return $mailContent;
    }

    public function getDataAttributeByListPolicy($policyIds)
    {
        $attrSearch = array();
        $attributes = array();
        $em = $this->getEntityManager();
        $dataAttributes = $em->getRepository('Application\Entity\PolicyAttributeMapping')->getDataByArrayPolicy($policyIds);
        foreach ($dataAttributes as $value) {
            $text = $value['attributeType'] . '-' . $value['attributesPolicyId'];
            $attrSearch[$value['attributeType']][] = $value['attributesPolicyId'];
            $attributes[$value['policyId']][$value['attributeType']][$value['attributesPolicyId']] = $text;
        }
        return array($attrSearch, $attributes);
    }

    public function getDataSettingMailByListAttributes($attributes)
    {
        if (!$attributes) return array();
        $em = $this->getEntityManager();
        $dataSettingMail = $em->getRepository('Application\Entity\SettingMail')->getDataByListAttributes($attributes);
        foreach ($dataSettingMail as $key => $value) {
            $text = $value['attributeType'] . '-' . $value['attributesPolicyId'];
            $settingMail[$value['userId']][$value['attributeType']][$value['attributesPolicyId']] = $text;
        }
        return isset($settingMail) ? $settingMail : array();
    }

    public function saveRecruitmentTime($policyId, $cbRecruitmentFlag, $recruitTimes, $params)
    {
        try {
            $em = $this->getEntityManager();
            $em->getRepository('Application\Entity\RecruitmentTime')->deleteRecruitmentTimeByPolicy($policyId);
            if ($cbRecruitmentFlag != 1 && !in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_3, $params['attributes'])) {
                $dataRecruitmentTime = array();
                foreach ($recruitTimes['startDate'] as $key => $values) {
                    if (!empty($values)) {
                        $dataRecruitmentTime[] = array(
                            'policyId' => $policyId,
                            'startDate' => DateHelper::convertDateToNumber($recruitTimes['startDate'][$key]),
                            'deadline' => !empty($recruitTimes['deadline'][$key]) && !in_array(ApplicationConst::NOTICE_RECRUITMENT_FORM_TYPE_2, $params['attributes']) ? DateHelper::convertDateToNumber($recruitTimes['deadline'][$key] . ' 23:59:59') : 0,
                            'endDate' => !empty($recruitTimes['endDate'][$key]) ? DateHelper::convertDateToNumber($recruitTimes['endDate'][$key] . ' 23:59:59') : 0
                        );
                    }
                }
                $arrFields = array('policyId', 'startDate', 'deadline', 'endDate', 'createDate', 'updateDate');
                $em->getRepository('Application\Entity\RecruitmentTime')->insertMultiRows($arrFields, $dataRecruitmentTime);
            }
            return array('status' => 1, 'message' => 'SUCCESS');
        } catch (\Exception $ex) {
            return array('status' => 0, 'message' => $ex->getMessage());
        }

    }

    public function getAttributesByPolicy($policyId)
    {
        $em = $this->getEntityManager();
        $dataAtributesPolicy = $em->getRepository('Application\Entity\PolicyAttributeMapping')->findBy(array(
            'policyId' => $policyId
        ));
        if ($dataAtributesPolicy) {
            /* @var $attribute \Application\Entity\PolicyAttributeMapping */
            foreach ($dataAtributesPolicy as $attribute) {
                $attributesPolicy[] = $attribute->getAttributesPolicyId();
            }
        }

        return isset($attributesPolicy) ? $attributesPolicy : array();
    }

    public function getRecruitmentTimeByPolicy($policyId)
    {
        $em = $this->getEntityManager();
        $dataRecruitmentTime = $em->getRepository('Application\Entity\RecruitmentTime')->findBy(array(
            'policyId' => $policyId
        ), array('startDate' => 'ASC'));
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

    public function saveInfoPolicyTemp($policyId, $updateDateSchedule = NULL)
    {
        $em = $this->getEntityManager();
        $policy = $em->getRepository('Application\Entity\Policy')->find($policyId);
        if (!$policy) {
            return array('status' => 0, 'message' => 'Do Not Exist Policy');
        }
        /* @var $policyTemp \Application\Entity\Repository\PolicyTempRepository */
        $policyTemp = $em->getRepository('Application\Entity\PolicyTemp');

        try {
            $dataPolicyTem = $policyTemp->getDataHaveUpdateDateGreaterThanCurrentTime($policyId);
            if (!$dataPolicyTem) {
                $policyTemp->deletePolicyTempById($policyId);
                $policyTemp->deletePolicyAttributeMappingTempByPolicyId($policyId);
                $policyTemp->deleteRecruitmentTimeTempByPolicyId($policyId);

                $policyTemp->insertDataTempByPolicy($policyId, $updateDateSchedule);
                $policyTemp->insertDataAttributeTempByPolicy($policyId);
                $policyTemp->insertDataRecruitmentTimeTempByPolicy($policyId);
            } else {
                $policyTemp->updateScheduleDateByPolicy($policyId, $updateDateSchedule);
            }
            return array('status' => 1, 'message' => 'SUCCESS');
        } catch (\Exception $ex) {
            return array('status' => 0, 'message' => $ex->getMessage());
        }
    }

    public function deletePolicyTemp($policyId)
    {
        try {
            $em = $this->getEntityManager();
            /* @var $policyTemp \Application\Entity\Repository\PolicyTempRepository */
            $policyTemp = $em->getRepository('Application\Entity\PolicyTemp');

            $policyTemp->deletePolicyTempById($policyId);
            $policyTemp->deletePolicyAttributeMappingTempByPolicyId($policyId);
            $policyTemp->deleteRecruitmentTimeTempByPolicyId($policyId);
        } catch (\Exception $ex) {
            return array('status' => 0, 'message' => $ex->getMessage());
        }

    }

    public function deletePolicy($policyIds)
    {
        if (!is_array($policyIds)) {
            return false;
        }
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();
        try {
            /* @var $policyRepo \Application\Entity\Repository\PolicyRepository */
            $policyRepo = $em->getRepository('Application\Entity\Policy');

            /* @var $policyTempRepo \Application\Entity\Repository\PolicyTempRepository */
            $policyTempRepo = $em->getRepository('Application\Entity\PolicyTemp');

            /* @var $recruitmentTimeRepo \Application\Entity\Repository\RecruitmentTimeRepository */
            $recruitmentTimeRepo = $em->getRepository('Application\Entity\RecruitmentTime');

            /* @var $mailContent \Application\Entity\Repository\MailContentRepository */
            $mailContent = $em->getRepository('Application\Entity\MailContent');

            /* @var $policyFavourite \Application\Entity\Repository\PolicyFavouriteRepository */
            $policyFavourite = $em->getRepository('Application\Entity\PolicyFavourite');

            /* @var $policyAttributesRepo \Application\Entity\Repository\PolicyAttributeMappingRepository */
            $policyAttributesRepo = $em->getRepository('Application\Entity\PolicyAttributeMapping');

            $policyTempRepo->deleteDataRecruitmentTimeByArrayPolicy($policyIds);
            $policyTempRepo->deleteDataAttributesByArrayPolicy($policyIds);
            $policyTempRepo->deleteDataPolicyTempByArrayPolicy($policyIds);

            $policyAttributesRepo->deleteDataByArrayPolicy($policyIds);
            $recruitmentTimeRepo->deleteDataByArrayPolicy($policyIds);
            $mailContent->deleteDataByArrayPolicy($policyIds);
            $policyFavourite->deleteDataByArrayPolicy($policyIds);
            $policyRepo->deleteDataByArrayPolicy($policyIds);

            $em->getConnection()->commit();
            return true;
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();
            return false;
        }
    }

    public function publicPolicy($listPolicy)
    {
        if (!$listPolicy || !is_array($listPolicy)) {
            return array('status' => 0, 'message' => 'EMPTY');
        }
        try {
            $em = $this->getEntityManager();
            $policyIds = array_keys($listPolicy);
            /* @var $policyRepo \Application\Entity\Repository\PolicyRepository */
            $policyRepo = $em->getRepository('Application\Entity\Policy');
            $policyRepo->updatePublishDateByListPolicy($policyIds, 1);
            $policyRepo->updatePublishDateIsNullByListPolicy($policyIds);

            return array('status' => 1, 'message' => 'SUCCESS');
        } catch (\Exception $ex) {
            return array('status' => 0, 'message' => $ex->getMessage());
        }

    }

    public function privatePolicy($listPolicy)
    {
        if (!$listPolicy || !is_array($listPolicy)) {
            return array('status' => 0, 'message' => 'EMPTY');
        }
        $em = $this->getEntityManager();
        try {
            $policyIds = array_keys($listPolicy);
            /* @var $policyRepo \Application\Entity\Repository\PolicyRepository */
            $policyRepo = $em->getRepository('Application\Entity\Policy');
            $policyRepo->updatePublishDateByListPolicy($policyIds, 0, 1);

            return array('status' => 1, 'message' => 'SUCCESS');
        } catch (\Exception $ex) {
            return array('status' => 0, 'message' => $ex->getMessage());
        }

    }

    public function generateFileZipPdf($policyId)
    {
        $em = $this->getEntityManager();
        $config = $this->getServiceLocator()->get('Config');
        /* @var $policy \Application\Entity\Policy */
        $policy = $em->getRepository('Application\Entity\Policy')->find($policyId);
        if (!$policy) {
            return false;
        }
        $pdfFile = array();
        $pdfFileFromDb = $policy->getPdfFile() ? Json::decode($policy->getPdfFile(), true) : array();
        foreach ($pdfFileFromDb as $value) {
            $pdfFile[] = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['folderUploadPdf'] . '/' . $value;
        }

        if ($pdfFile) {
            $destination = $_SERVER['DOCUMENT_ROOT'] . '/' . $config['folderUploadPdf'] . '/' . date('Y/md', $policy->getCreateDate()) . '/' . 'all_policy_' . $policyId . '.zip';
            $overwrite = file_exists($destination) ? true : false;
            $result = CommonUtils::createFileZip($pdfFile, $destination, $overwrite);
            return $result;
        } else {
            return false;
        }
    }

    public function getMessages()
    {
        $translator = $this->getTranslator();
        return array(
            'MSG_PO_001_Required_Field' => $translator->translate('MSG_PO_001_Required_Field'),
            'MSG_PO_002_Error_Format_Date' => $translator->translate('MSG_PO_002_Error_Format_Date'),
            'MSG_PO_005_Error_Publish_Date_Format' => $translator->translate('MSG_PO_005_Error_Publish_Date_Format'),
            'MSG_PO_006_Error_Publish_Start_Greater_End' => $translator->translate('MSG_PO_006_Error_Publish_Start_Greater_End'),
            'MSG_PO_016_Error_Compare_StartDate_With_Input' => $translator->translate('MSG_PO_016_Error_Compare_StartDate_With_Input'),
            'MSG_PO_017_Error_Compare_EndDate_With_Input' => $translator->translate('MSG_PO_017_Error_Compare_EndDate_With_Input'),
            'MSG_PO_018_Error_Compare_Deadline_With_Input' => $translator->translate('MSG_PO_018_Error_Compare_Deadline_With_Input'),
            'MSG_PO_019_Error_Compare_UpdateDate_With_Input' => $translator->translate('MSG_PO_019_Error_Compare_UpdateDate_With_Input'),
            'MSG_PO_020_Error_Compare_StartDate_With_EndDate' => $translator->translate('MSG_PO_020_Error_Compare_StartDate_With_EndDate'),
            'MSG_PO_021_Error_Compare_StartDate_With_Deadline' => $translator->translate('MSG_PO_021_Error_Compare_StartDate_With_Deadline'),
            'MSG_PO_022_Error_Compare_StartDate_With_StartPublishDate' => $translator->translate('MSG_PO_022_Error_Compare_StartDate_With_StartPublishDate'),
            'MSG_PO_023_Error_Compare_StartDate_With_EndPublishDate' => $translator->translate('MSG_PO_023_Error_Compare_StartDate_With_EndPublishDate'),
            'MSG_PO_024_Error_Compare_StartDate_With_PublishDate' => $translator->translate('MSG_PO_024_Error_Compare_StartDate_With_PublishDate'),
            'MSG_PO_025_Error_Compare_EndDate_With_StartPublishDate' => $translator->translate('MSG_PO_025_Error_Compare_EndDate_With_StartPublishDate'),
            'MSG_PO_026_Error_Compare_EndDate_With_EndPublishDate' => $translator->translate('MSG_PO_026_Error_Compare_EndDate_With_EndPublishDate'),
            'MSG_PO_027_Error_Compare_EndDate_With_PublishDate' => $translator->translate('MSG_PO_027_Error_Compare_EndDate_With_PublishDate'),
            'MSG_PO_028_Error_Compare_Deadline_With_StartPublishDate' => $translator->translate('MSG_PO_028_Error_Compare_Deadline_With_StartPublishDate'),
            'MSG_PO_029_Error_Compare_Deadline_With_EndPublishDate' => $translator->translate('MSG_PO_029_Error_Compare_Deadline_With_EndPublishDate'),
            'MSG_PO_030_Error_Compare_Deadline_With_PublishDate' => $translator->translate('MSG_PO_030_Error_Compare_Deadline_With_PublishDate'),
            'MSG_PO_031_Error_Compare_UpdateDate_With_StartPublishDate' => $translator->translate('MSG_PO_031_Error_Compare_UpdateDate_With_StartPublishDate'),
            'MSG_PO_032_Error_Compare_UpdateDate_With_EndPublishDate' => $translator->translate('MSG_PO_032_Error_Compare_UpdateDate_With_EndPublishDate'),
            'MSG_PO_033_Error_Compare_UpdateDate_With_PublishDate' => $translator->translate('MSG_PO_033_Error_Compare_UpdateDate_With_PublishDate'),
            'MSG_PM_001_EmptyPolicyIds' => $translator->translate('MSG_PM_001_EmptyPolicyIds'),
            'MSG_PM_002_Title_Warning' => $translator->translate('MSG_PM_002_Title_Warning'),
            'MSG_PM_003_Content_Confirm_Warning' => $translator->translate('MSG_PM_003_Content_Confirm_Warning'),
            'MSG_PM_004_Title_Confirm_Save_Policy' => $translator->translate('MSG_PM_004_Title_Confirm_Save_Policy'),
            'MSG_PM_005_Content_Confirm_Save_Policy' => $translator->translate('MSG_PM_005_Content_Confirm_Save_Policy'),
            'MSG_PM_006_Title_Private_Policy' => $translator->translate('MSG_PM_006_Title_Private_Policy'),
            'MSG_PM_007_Content_Private_Policy' => $translator->translate('MSG_PM_007_Content_Private_Policy'),
            'MSG_PM_008_Title_Public_Policy' => $translator->translate('MSG_PM_008_Title_Public_Policy'),
            'MSG_PM_009_Content_Public_Policy' => $translator->translate('MSG_PM_009_Content_Public_Policy'),
            'MSG_PM_0010_Title_Delete_Policy' => $translator->translate('MSG_PM_0010_Title_Delete_Policy'),
            'MSG_PM_0011_Content_Delete_Policy' => $translator->translate('MSG_PM_0011_Content_Delete_Policy'),
            'MSG_PM_0012_Title_Private_Policy_Succeed' => $translator->translate('MSG_PM_0012_Title_Private_Policy_Succeed'),
            'MSG_PM_0013_Title_Public_Policy_Succeed' => $translator->translate('MSG_PM_0013_Title_Public_Policy_Succeed'),
            'MSG_PM_0014_Title_Delete_Policy_Succeed' => $translator->translate('MSG_PM_0014_Title_Delete_Policy_Succeed'),
            'MSG_PM_0015_Title_Delete_Row' => $translator->translate('MSG_PM_0015_Title_Delete_Row'),


        );
    }

    public function getArrayDivisionByCode($divisionId, $departmentId)
    {
        $glConfig = $this->getServiceLocator()->get('Config');
        foreach ($glConfig['divisionId'] as $key => $value) {
            if ($value['parentId'] == 'departmentId' . $departmentId && $value['code'] == $divisionId && $divisionId != '') {
                $division = array($key => $value);
                break;
            }
        }
        return isset($division) ? $division : $glConfig['divisionId'];
    }

    public function getPolicyByIdAndRole($policyId, $userInfo)
    {
        if (UserHelper::isInputRole($userInfo['roleId'])) {
            $searchByRole = array(
                'bureauId' => $userInfo['bureauId'],
                'departmentId' => $userInfo['departmentId'],
                'divisionId' => $userInfo['divisionId']
            );
        }
        $em = $this->getEntityManager();
        /* @var $policy \Application\Entity\Policy */
        $policy = $em->getRepository('Application\Entity\Policy')->getDataByIdAndDepartment(
            $policyId,
            isset($searchByRole) ? $searchByRole : array()
        );
        return $policy;
    }

    public function getPolicyByArrayIdsAndRole($policyIds, $userInfo)
    {
        if (!is_array($policyIds)) {
            return array();
        }
        if (UserHelper::isInputRole($userInfo['roleId'])) {
            $searchByRole = array(
                'bureauId' => $userInfo['bureauId'],
                'departmentId' => $userInfo['departmentId'],
                'divisionId' => $userInfo['divisionId']
            );
        }
        $em = $this->getEntityManager();
        $policys = $em->getRepository('Application\Entity\Policy')->getDataByArrayPolicy(
            $policyIds,
            isset($searchByRole) ? $searchByRole : array()
        );
        return $policys;
    }
}