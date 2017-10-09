<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Service\ServiceInterface\MyPageServiceInterface;
use Application\Service\ServiceInterface\LoginServiceInterface;
use Application\Utils\ApplicationConst;
use Application\Utils\MailHelper;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MyPageController extends BaseController
{   
    /**
     * @var \Application\Service\myPageService
     */
    protected $myPageService;
    protected $loginService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(MyPageServiceInterface $myPageService, LoginServiceInterface $loginService, EntityManager $em) {
        $this->myPageService = $myPageService;
        $this->loginService = $loginService;
        $this->em = $em;
    }

    public function updateUserInfoAction(){
        try {
            $data = array();
            $data["userId"] = (int) $this->params()->fromPost('userId', 0);
            $data["roleId"] = (int) $this->params()->fromPost('roleId', 0);            
            $data["nextEmail"] = trim(strip_tags($this->params()->fromPost('email', "")));            
            
            $data["securityQuestionId"] = (int) $this->params()->fromPost('securityQuestionId', 0);
            $data["securityAnswer"] = trim(strip_tags($this->params()->fromPost('securityAnswer', "")));
            
            $attributes = trim(strip_tags($this->params()->fromPost('attributes', "")));
            $attributes = json_decode($attributes, true);
            $data["attributes"] = array();
            foreach($attributes as $valueId){
                $data["attributes"][] = intval($valueId);  
            }

            $sendEmail = array();
            
            $userCanShowSQ = \Application\Utils\ApplicationConst::USER_CAN_SHOW_SQ;
            if (!empty($data["userId"]) && !empty($data["roleId"])) {
                
                $userInfo = $this->myPageService->getUserInfoById($data["userId"]);
                
                $error = "";
                if($userInfo["email"]!=$data["nextEmail"]){
                    if(empty($data["nextEmail"])){
                        $error .= "<p>" .  $this->translate('MSG_MP_023_CannotUpdateEmailToEmpty') . "</p>";    
                    } else {
                        if(!\Application\Utils\CommonUtils::validateEmail($data["nextEmail"])){
                            $error .= "<p>" .  $this->translate('MSG_MP_005_EmailWrong') . "</p>";    
                        } else {
                            // check email exist
                            $checkEmailExist = (int) $this->myPageService->checkEmailExist($data["userId"], $data["nextEmail"]);
                            $error .= $checkEmailExist>0 ? "<p>" .  $this->translate('MSG_MP_020_EmailExistInSystem') . "</p>" : "";
                        }   
                    }
                }  
                
                if(in_array($data["roleId"], $userCanShowSQ)){
                    if(empty($data["securityQuestionId"])){
                        $error .= "<p>" .  $this->translate('MSG_MP_006_SecurityQuestionCannotEmpty') . "</p>";
                    }
                    
                    if(empty($data["securityAnswer"])){
                        $error .= "<p>" .  $this->translate('MSG_MP_007_SecurityAnswerCannotEmpty') . "</p>";
                    } else {
                        if(!\Application\Utils\CommonUtils::validateSecurityAnswer($data["securityAnswer"])){
                            $error .= "<p>" .  $this->translate('MSG_MP_021_SecurityAnswerWrongFormat') . "</p>";   
                        }   
                    }  
                }
                
                if(empty($error)){
                    $isSendMail = false;
                    if($userInfo["email"]==$data["nextEmail"]){
                        // case not update email
                        $data["email"] = $userInfo["email"];
                        $data["nextEmail"] = $userInfo["nextEmail"];
                        $data["token"] = $userInfo["token"];
                    } else {
                        // case update email
                        $data["token"] = \Application\Utils\CommonUtils::generateToken();
                        $isSendMail = true;
                        if(empty($userInfo["email"])){
                            //case update from email email
                            $data["email"] = $data["nextEmail"];
                        } else {
                            if($userInfo["email"]==$userInfo["nextEmail"]){
                                // Case update from first email not active
                                $data["email"] = $data["nextEmail"];
                            } else {
                                $data["email"] = $userInfo["email"];    
                            }
                        } 
                    }                    
                    
                    $this->em->getConnection()->beginTransaction();
                    try{
                        $results = $this->myPageService->updateUserInfo($data);
                        if ($results >= 0) {
                            if($isSendMail){
                                // send mail
                                $mailer = new MailHelper($this->getServiceLocator());
                                $mailer->sendEmail($data["nextEmail"], array("userId"=>$userInfo["username"], "token"=>$data["token"]), MailHelper::TEMPLATE_UPDATE_USER_INFO);   
                            }
                            $this->em->getConnection()->commit();
                            return new JsonModel(array(
                                'status' => true
                                , 'message' => $this->translate('MSG_MP_008_SuccessUpdateInfo')
                            ));    
                        } else {
                            return new JsonModel(array(
                                'status' => false
                                , 'message' => $this->translate('MSG_MP_002_FailUpdateInfoUser')
                            )); 
                        }   
                    } catch (\Exception $e) {
                        $this->em->getConnection()->rollback();
                        throw $e;
                    }    
                } else {
                    return new JsonModel(array(
                        'status' => false
                        , 'message' => $error
                    ));   
                }
            }
            
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_MP_002_FailUpdateInfoUser')
            ));  
        } catch (\Exception $e){
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_CM_001_ServerError')
            ));   
        }
    }
    
    public function changePasswordAction(){
        try {
            
            $data = array();
            $data["userId"] = (int) $this->params()->fromPost('userId', 0);           
            $data["oldPassword"] = trim(strip_tags($this->params()->fromPost('oldPassword', "")));            
            $data["newPassword"] = trim(strip_tags($this->params()->fromPost('newPassword', "")));            
            $data["newPasswordRetype"] = trim(strip_tags($this->params()->fromPost('newPasswordRetype', "")));            
             
            if (!empty($data["userId"])) {
                
                $userInfo = $this->myPageService->getUserInfoById($data["userId"]); 
                $passwordHistory = !empty($userInfo["passwordHistory"]) ? json_decode($userInfo["passwordHistory"], true) : array();
                
                $error = array();
                if(empty($data["oldPassword"])){
                    $error[] = $this->translate('MSG_MP_009_OldPasswordCannotEmpty');
                } else {
                    // get old password
                    if(\Application\Utils\CommonUtils::encrypt($data["oldPassword"])!= $userInfo["password"]){
                        $error[] =  $this->translate('MSG_MP_012_OldPasswordIncorrect');   
                    }    
                }
                
                if(empty($data["newPassword"]) || empty($data["newPasswordRetype"])){
                    $error[] = $this->translate('MSG_MP_010_NewPasswordCannotEmpty');
                } else {
                    $newPassword = \Application\Utils\CommonUtils::encrypt($data["newPassword"]);
                    if($data["newPassword"]!=$data["newPasswordRetype"]){
                        $error[] = $this->translate('MSG_MP_013_NewPasswordNotMatch');   
                    } else if(!\Application\Utils\CommonUtils::validatePassword($data["newPassword"])) {
                        $error[] = $this->translate('MSG_MP_003_WrongPassFormat');   
                    } 
                }
                
                if(\Application\Utils\CommonUtils::encrypt($data["newPassword"])==$userInfo["password"]){
                    $error[] = $this->translate('MSG_MP_017_PasswordCanNotEqCurrentPassword');   
                } else {
                    if(in_array($userInfo["roleId"], \Application\Utils\ApplicationConst::USER_CHECK_PASSWORD_HISTORY) && in_array($newPassword, $passwordHistory)){
                        $error[] = $this->translate('MSG_MP_014_PasswordCanNotInHistory');                   
                    }   
                }
               
                if(empty($error)){
                    if(count($passwordHistory)>=5){
                        array_shift($passwordHistory);    
                    } 
                    array_push($passwordHistory, $newPassword);
   
                    $results = $this->myPageService->changePasswordByUserId($data["userId"], $newPassword, $passwordHistory);
                    
                    if ($results >= 0) {
                        return new JsonModel(array(
                            'status' => true
                            , 'message' => $this->translate('MSG_MP_015_SuccessChangePassword')
                        ));    
                    } else {
                        return new JsonModel(array(
                            'status' => false
                            , 'message' => array($this->translate('MSG_MP_016_FailChangePassword'))
                        )); 
                    }    
                } else {
                    return new JsonModel(array(
                        'status' => false
                        , 'message' => $error
                    ));   
                }
            }
            
            return new JsonModel(array(
                'status' => false
                , 'message' => array($this->translate('MSG_MP_002_FailUpdateInfoUser'))
            ));  
        } catch (\Exception $e){
            return new JsonModel(array(
                'status' => false
                , 'message' => array($this->translate('MSG_CM_001_ServerError'))
            ));   
        }
    }
        
    public function confirmSettingMailAction(){
        try {
            $data = array();
            $data["userId"] = (int) $this->userInfo["userId"];
            $data["isSettingMail"] = !empty($this->userInfo["isSettingMail"]) ? 1 : 0;
            
            $data["searchContent"] = trim(strip_tags($this->params()->fromPost('searchContent', "")));
            $data["searchContent"] = json_decode($data["searchContent"], true);
            
            $data["searchField"] = trim(strip_tags($this->params()->fromPost('searchField', "")));
            $data["searchField"] = json_decode($data["searchField"], true);
            
            $data["searchTargetPeople"] = trim(strip_tags($this->params()->fromPost('searchTargetPeople', "")));
            $data["searchTargetPeople"] = json_decode($data["searchTargetPeople"], true);
            
            $data["searchTargetJob"] = trim(strip_tags($this->params()->fromPost('searchTargetJob', "")));
            $data["searchTargetJob"] = json_decode($data["searchTargetJob"], true);
            
            $data["searchArea"] = trim(strip_tags($this->params()->fromPost('searchArea', "")));
            $data["searchArea"] = json_decode($data["searchArea"], true);
            
            $data["searchAmount"] = trim(strip_tags($this->params()->fromPost('searchAmount', "")));
            $data["searchAmount"] = json_decode($data["searchAmount"], true);
            
            $data["searchNumberPeople"] = trim(strip_tags($this->params()->fromPost('searchNumberPeople', "")));
            $data["searchNumberPeople"] = json_decode($data["searchNumberPeople"], true);
           
            $data["searchFavourite"] = trim(strip_tags($this->params()->fromPost('searchFavourite', "")));
            $data["searchFavourite"] = json_decode($data["searchFavourite"], true);
            
            $searchPolicyType = trim(strip_tags($this->params()->fromPost('searchPolicyType', "")));
            $searchPolicyType = json_decode($searchPolicyType, true);
            $data["searchPolicyType"] = \Application\Utils\CommonUtils::convertRecruitmentTimeStatus($searchPolicyType);
            
            if (!empty($data["userId"])) {
                $error = "";
                
                if(empty($data["searchContent"]) && empty($data["searchField"]) && empty($data["searchPolicyType"])
                && empty($data["searchTargetPeople"]) && empty($data["searchTargetJob"]) && empty($data["searchArea"])
                && empty($data["searchAmount"]) && empty($data["searchNumberPeople"]) && empty($data["searchFavourite"])){
                    if(empty($data["isSettingMail"])){
                        $error .= "<p>" .  $this->translate('MSG_MP_024_SettingMailFirstTime') . "</p>";
                    } else {
                        return new JsonModel(array(
                            'status' => true
                            , 'message' => str_replace("[Value]", "0", $this->translate('MSG_MP_034_ConfirmSettingMail'))
                        ));    
                    }      
                }
                
                if(empty($error)){
                    $listFavouritePolicy = array();
                    $listPublishPolicyBySearch = array();
                    
                    if(!empty($data["searchFavourite"])){
                        $resultsFavourite = $this->myPageService->getListFavouritePolicies($data["userId"]);
                        foreach($resultsFavourite as $row){
                            $listFavouritePolicy[] = $row["policyId"];   
                        }   
                    }
                    
                    if(!empty($data["searchContent"]) || !empty($data["searchField"]) || !empty($data["searchPolicyType"])
                    || !empty($data["searchTargetPeople"]) || !empty($data["searchTargetJob"]) || !empty($data["searchArea"])
                    || !empty($data["searchAmount"]) || !empty($data["searchNumberPeople"])){
                        $results = $this->myPageService->getPublishPoliciesBySearch($data, 'new', $data["userId"]);
                        foreach($results as $row){
                            $listPublishPolicyBySearch[] = $row["id"];   
                        }    
                    }
                    
                    $listPolicy = array_unique(array_merge_recursive($listPublishPolicyBySearch, $listFavouritePolicy));
                    
                    // Check policy have notification flag checked
                    $listPolicySendMail = $this->myPageService->getPolicyHaveNotificationFlag($listPolicy);
                    
                    $list_policy_send_mail = array();
                    if(!empty($listPolicySendMail)){
                        foreach($listPolicySendMail as $row){
                            $list_policy_send_mail[] = $row["id"];
                        }   
                    }
                    return new JsonModel(array(
                        'status' => true
                        , 'message' => str_replace("[Value]", count($list_policy_send_mail), $this->translate('MSG_MP_034_ConfirmSettingMail'))
                    ));
                   
                } else {
                    return new JsonModel(array(
                        'status' => false
                        , 'message' => $error
                    ));   
                }
            }
            
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_MP_019_FailSaveSettingMail')
            ));  
        } catch (\Exception $e){
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_CM_001_ServerError')
            ));   
        }
    }
    
    public function saveSettingMailAction(){
        try {
            $data = array();
            $data["userId"] = (int) $this->userInfo["userId"];

            $data["isSettingMail"] = ($this->userInfo["isSettingMail"]) ? 1 : 0;
            $data["searchContent"] = trim(strip_tags($this->params()->fromPost('searchContent', "")));
            $data["searchContent"] = json_decode($data["searchContent"], true);
            
            $data["searchField"] = trim(strip_tags($this->params()->fromPost('searchField', "")));
            $data["searchField"] = json_decode($data["searchField"], true);
            
            $data["searchTargetPeople"] = trim(strip_tags($this->params()->fromPost('searchTargetPeople', "")));
            $data["searchTargetPeople"] = json_decode($data["searchTargetPeople"], true);
            
            $data["searchTargetJob"] = trim(strip_tags($this->params()->fromPost('searchTargetJob', "")));
            $data["searchTargetJob"] = json_decode($data["searchTargetJob"], true);
            
            $data["searchArea"] = trim(strip_tags($this->params()->fromPost('searchArea', "")));
            $data["searchArea"] = json_decode($data["searchArea"], true);
            
            //$data["searchAmount"] = trim(strip_tags($this->params()->fromPost('searchAmount', "")));
//            $data["searchAmount"] = json_decode($data["searchAmount"], true);
//            
//            $data["searchNumberPeople"] = trim(strip_tags($this->params()->fromPost('searchNumberPeople', "")));
//            $data["searchNumberPeople"] = json_decode($data["searchNumberPeople"], true);
            
            $searchAmountConfig = \Application\Utils\ApplicationConst::SEARCH_AMOUNT;
            $searchAmount = trim(strip_tags($this->params()->fromPost('searchAmount', "")));
            $searchAmount = json_decode($searchAmount, true);
            $data["searchAmount"] = array();
            foreach($searchAmount as $attrId){
                $data["searchAmount"] = array_merge($data["searchAmount"], $searchAmountConfig[$attrId]);
            }
            $data["searchAmount"] = array_unique($data["searchAmount"]);
            
            $searchNumberPeopleConfig = \Application\Utils\ApplicationConst::SEARCH_NUMBER_PEOPLE;
            $searchNumberPeople = trim(strip_tags($this->params()->fromPost('searchNumberPeople', "")));
            $searchNumberPeople = json_decode($searchNumberPeople, true);
            $data["searchNumberPeople"] = array();
            foreach($searchNumberPeople as $attrId){
                $data["searchNumberPeople"] = array_merge($data["searchNumberPeople"], $searchNumberPeopleConfig[$attrId]);
            }
            $data["searchNumberPeople"] = array_unique($data["searchNumberPeople"]);
            
            $data["searchFavourite"] = trim(strip_tags($this->params()->fromPost('searchFavourite', "")));
            $data["searchFavourite"] = json_decode($data["searchFavourite"], true);
            
            $data["searchPolicyType"] = trim(strip_tags($this->params()->fromPost('searchPolicyType', "")));
            $data["searchPolicyType"] = json_decode($data["searchPolicyType"], true);
            
            $data["attributes"] = array();
            $data["attributesDisplayNone"] = array();
            //get Attributes Type
            $allAttributes = $this->myPageService->getAllAttributes();
            $policyAttributes = array();
            foreach($allAttributes as $row){
                $policyAttributes[$row["id"]] = $row["attributeType"];
            }
            
            foreach($data["searchContent"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
            }
            
            foreach($data["searchField"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
            }
            
            foreach($data["searchTargetPeople"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
            }
            
            foreach($data["searchTargetJob"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
            }
            
            foreach($data["searchArea"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
            }
            
            foreach($data["searchAmount"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;
                if(!in_array($attributeId, $searchAmount)){
                    $data["attributesDisplayNone"][] = $attributeId;    
                }     
            }
            
            foreach($data["searchNumberPeople"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
                if(!in_array($attributeId, $searchNumberPeople)){
                    $data["attributesDisplayNone"][] = $attributeId;    
                }
            }
            
            foreach($data["searchFavourite"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
            }
            
            foreach($data["searchPolicyType"] as $attributeId){
                $attributeType = isset($policyAttributes[$attributeId]) ? $policyAttributes[$attributeId] : 0;
                $data["attributes"][$attributeType][] = $attributeId;     
            }

            if (!empty($data["userId"])) {                
                if(empty($data["isSettingMail"])){
                   $results = $this->myPageService->updateIsSettingMail($data["userId"]);
                   $userInfo = $this->userInfo;
                   $userInfo["isSettingMail"] = 1;
                   $this->loginService->saveUserInfoSession($userInfo); 
                }
                
                $results = $this->myPageService->saveSettingMail($data);
                if ($results >= 0) {
                    
                    if(empty($data["isSettingMail"]) && (!empty($data["searchContent"]) || !empty($data["searchField"]) || !empty($data["searchPolicyType"])
                    || !empty($data["searchTargetPeople"]) || !empty($data["searchTargetJob"]) || !empty($data["searchArea"])
                    || !empty($data["searchAmount"]) || !empty($data["searchNumberPeople"]) || !empty($data["searchFavourite"])) ){
                        
                        $listFavouritePolicy = array();
                        $listPublishPolicyBySearch = array();
                        
                        if(!empty($data["searchFavourite"])){
                            $resultsFavourite = $this->myPageService->getListFavouritePolicies($data["userId"]);
                            foreach($resultsFavourite as $row){
                                $listFavouritePolicy[] = $row["policyId"];   
                            }   
                        }
                        
                        if(!empty($data["searchContent"]) || !empty($data["searchField"]) || !empty($data["searchPolicyType"])
                        || !empty($data["searchTargetPeople"]) || !empty($data["searchTargetJob"]) || !empty($data["searchArea"])
                        || !empty($data["searchAmount"]) || !empty($data["searchNumberPeople"])){
                            $results = $this->myPageService->getPublishPoliciesBySearch($data, 'new', $data["userId"]);
                            foreach($results as $row){
                                $listPublishPolicyBySearch[] = $row["id"];   
                            }    
                        }
                        
                        $listPolicy = array_unique(array_merge_recursive($listPublishPolicyBySearch, $listFavouritePolicy));
                        
                        // Check policy have notification flag checked
                        $listPolicySendMail = $this->myPageService->getPolicyHaveNotificationFlag($listPolicy);
                        
                        $list_policy_send_mail = array();
                        if(!empty($listPolicySendMail)){
                            foreach($listPolicySendMail as $row){
                                $list_policy_send_mail[] = $row["id"];
                            }   
                        }
                        
                        // create content mail if the first time update setting mail
                        $results = $this->myPageService->createMailContent($list_policy_send_mail, $data["userId"]);    
                    }
                                                
                    return new JsonModel(array(
                        'status' => true
                        , 'message' => $this->translate('MSG_MP_018_SuccessSaveSettingMail')
                    ));    
                } else {
                    return new JsonModel(array(
                        'status' => false
                        , 'message' => $this->translate('MSG_MP_019_FailSaveSettingMail')
                    )); 
                }    
            }
            
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_MP_019_FailSaveSettingMail')
            ));  
        } catch (\Exception $e){
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_CM_001_ServerError')
            ));   
        }
    }
    
    public function checkEmailAction(){
        try {
            $data = array();
            $data["userId"] = (int) $this->params()->fromPost('userId', 0);
            
            if(!empty($data["userId"])) {
                $userInfo = $this->myPageService->getUserInfoById($data["userId"]);
                
                $error = "";
                if(empty($userInfo["email"])){
                    $error .= $this->translate('MSG_MP_022_CannotOpenSettingMail');    
                } else {
                    if($userInfo["email"]==$userInfo["nextEmail"]){
                        $error .= $this->translate('MSG_MP_032_CannotOpenSettingMailEmailNotActived');   
                    }
                }
                
                if(empty($error)) {
                    return new JsonModel(array(
                        'status' => true
                        , 'message' => ""
                    ));   
                } else {
                    return new JsonModel(array(
                        'status' => false
                        , 'message' => $error
                    ));
                }
            }
            
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_CM_001_ServerError')
            ));  
        } catch (\Exception $e){
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_CM_001_ServerError')
            ));   
        }
    }
}