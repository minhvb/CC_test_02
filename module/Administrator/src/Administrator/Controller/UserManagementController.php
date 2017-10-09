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
use Application\Utils\MailHelper;
use Administrator\Service\ServiceInterface\UserManagementServiceInterface;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;


class UserManagementController extends BaseController {

    /**
     * @var \Administrator\Service\UserManagementService
     */
    protected $userService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public $mappingField = array(
        0 => array("value" => "userId", "name"=>"ID", "require" => true, "title" => "User ID")
        , 1 => array("value" => "password", "name"=>"パスワード", "require" => false, "title" => "Password")
        , 2 => array("value" => "role", "name"=>"ID種別", "require" => true, "title" => "Role")
        , 3 => array("value" => "business", "name"=>"業種", "require" => false, "title" => "Business")
        , 4 => array("value" => "companySize", "name"=>"企業規模", "require" => false, "title" => "Company size")
        , 5 => array("value" => "region", "name"=>"地域", "require" => false, "title" => "Region")
        , 6 => array("value" => "email", "name"=>"メールアドレス", "require" => false, "title" => "Email")
        , 7 => array("value" => "delete", "name"=>"Delete", "require" => false, "title" => "Delete")
    );

    public function __construct(UserManagementServiceInterface $userService, EntityManager $em) {
        $this->userService = $userService;
        $this->em = $em;
    }

    public function indexAction() 
    {
        // Get all role of user
        $roles = $this->userService->roles();
        
        $currentPage = (int) $this->params()->fromQuery('page');
        $currentPage = $currentPage == 0 ? 1 : $currentPage;
        $resultPerPage = \Application\Utils\ApplicationConst::RESULT_PER_PAGE;
        $startRecord = ($currentPage - 1) * $resultPerPage;
        
        $search = array();
        $search["keyword"] = trim(strip_tags($this->params()->fromQuery('keyword', '')));
        $search['roleId'] = (int) $this->params()->fromQuery('roleId', 0);
        
        $users = $this->userService->getAllUsers($search, $currentPage, $resultPerPage);
        $totalResults = $this->userService->getTotalUsers($search);
        $totalPages = ceil($totalResults / $resultPerPage);
        
        $currentUrl = $this->getRequest()->getRequestUri();
        if($currentPage>1 && $totalResults==$startRecord){
            $currentUrl = \Application\Utils\CommonUtils::getPreviosPage($currentUrl, $currentPage);
            
            return $this->redirect()->toUrl($currentUrl);    
        }
        
        $userCanDelete = \Application\Utils\ApplicationConst::USER_CAN_DELETE;
        $userCanNotResetPass = \Application\Utils\ApplicationConst::USER_CANNOT_RESET_PASS;

        $currentUrl = $this->getRequest()->getRequestUri();
        
        $this->viewModel->setVariables(
            array(
                "roles"=>$roles
                , "search"=>$search
                , "users"=>$users
                , "currentPage"=>$currentPage
                , "resultPerPage"=>$resultPerPage
                , "totalResults"=>$totalResults
                , "totalPages"=>$totalPages
                , "startRecord"=>$startRecord
                , "userCanDelete"=>$userCanDelete
                , "userCanNotResetPass"=>$userCanNotResetPass
                , "currentUrl"=>$currentUrl
            )
        );
        return $this->viewModel;               
    }
    
    public function deleteUserAction()
    {
        try {
            $userId = (int) $this->params()->fromPost('userId', 0);
            $username = trim(strip_tags($this->params()->fromPost('username', '')));
            $roleId = (int) $this->params()->fromPost('roleId', 0);
            
            if (!empty($userId)) {
                
                if(in_array($roleId, \Application\Utils\ApplicationConst::USER_CAN_DELETE)){
                    $results = $this->userService->deleteByUserId($userId);

                    if ($results > 0) {
                        return new JsonModel(array(
                            'status' => true
                            , 'message' => str_replace('[Value]', $username, $this->translate('MSG_UM_002_SuccessDeleteUser'))
                        ));    
                    } else {
                        return new JsonModel(array(
                            'status' => false
                            , 'message' => $this->translate('MSG_UM_003_FailDeleteUser')
                        )); 
                    }    
                } else {
                    return new JsonModel(array(
                        'status' => false
                        , 'message' => $this->translate('MSG_UM_011_CannotDeleteUser')
                    ));    
                }    
            }
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_UM_003_FailDeleteUser')
            ));  
        } catch (\Exception $e){
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_UM_001_ServerError')
            ));   
        }       
    }
    
    public function resetPasswordAction()
    {
        try {
            $userId = (int) $this->params()->fromPost('userId', 0);
            $username = trim(strip_tags($this->params()->fromPost('username', "")));            
            $roleId = (int) $this->params()->fromPost('roleId', 0);            
            $password = strip_tags($this->params()->fromPost('newPassword', ""));
            
            if (!empty($userId)) {                                      
                if(\Application\Utils\CommonUtils::validatePassword($password)){
                    if(!in_array($roleId, \Application\Utils\ApplicationConst::USER_CANNOT_RESET_PASS)){
                        $this->em->getConnection()->beginTransaction();
                        try{
                            $results = $this->userService->resetPasswordByUserId($userId, \Application\Utils\CommonUtils::encrypt($password));
                            if ($results >= 0) {
                                //send mail with new password
                                $userInfo = $this->userService->getUserInfoById($userId);
                                if(!empty($userInfo["email"]) && $userInfo["email"]!=$userInfo["nextEmail"]){
                                    $mailer = new MailHelper($this->getServiceLocator());
                                    $mailer->sendEmail($userInfo["email"], array("userId"=>$userInfo["username"], "password"=>$password), MailHelper::TEMPLATE_RESET_PASSWORD);   
                                }
                                $this->em->getConnection()->commit();
                                return new JsonModel(array(
                                    'status' => true
                                    , 'message' => str_replace("[Value]", $username, $this->translate('MSG_UM_014_SuccessResetPassword'))
                                ));    
                            } else {
                                return new JsonModel(array(
                                    'status' => false
                                    , 'message' => $this->translate('MSG_UM_013_FailResetPassword')
                                )); 
                            }     
                        } catch (\Exception $e) {
                            $this->em->getConnection()->rollback();
                            
                            throw $e;
                        }
                        
                                
                    } else {
                        return new JsonModel(array(
                            'status' => false
                            , 'message' => $this->translate('MSG_UM_012_CannotResetPassword')
                        ));    
                    }    
                } else {
                    return new JsonModel(array(
                        'status' => false
                        , 'message' => $this->translate('MSG_UM_005_WrongPassFormat')
                    ));   
                }
                
                        
            }
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_UM_013_FailResetPassword')
            ));  
        } catch (\Exception $e){
            return new JsonModel(array(
                'status' => false
                , 'message' => $this->translate('MSG_UM_001_ServerError')
            ));   
        }       
    }
   
    public function importUserAction()
    {
        try{
            $request = $this->getRequest();
            $error = "";

            $locationLog = getcwd() . "/public/static/import-user/import-user-" . $this->userInfo["username"] . date("Y-m-d") . ".log";
            if ($request->isPost()) {
                // Make certain to merge the files info!
                $post = array_merge_recursive(
                    $request->getFiles()->toArray()
                );
                
                $message = " \n ======================================== \n";
                $message .= "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Start validate upload file]\n";
                if (\Application\Utils\ApplicationConst::DEBUG) @error_log($message, 3, $locationLog);

                $file = isset($post["filename"]) ? $post["filename"] : array();

                $error .= empty($file["name"]) ? $this->translate('MSG_UM_015_FileNotExist') . "<br />" : "";
                if (!empty($error)) {
                    return $this->returnError($error);
                }
                
                $extension = end(explode(".", $file["name"]));
                $extension = strtolower($extension);
                $error .= !in_array($extension, array("csv")) ? $this->translate('MSG_UM_008_WrongCSVFile') . "<br />" : "";
                if (!empty($error)) {
                    return $this->returnError($error);
                }

                $error .= $file["size"] > \Application\Utils\ApplicationConst::FILE_UPLOAD_LIMIT ? $this->translate('MSG_UM_016_FileSizeExceedingLimits') . "<br />" : "";
                if (!empty($error)) {
                    return $this->returnError($error);
                }

                /* Read TSV File */
                $handle = @fopen($file["tmp_name"], "r");
                if ($handle == false) {
                    $error = $this->translate('MSG_UM_017_CanNotReadFile') . "<br />";
                    return $this->returnError($error);
                }

                $i = 0;
                $j = 1;
                $datas = array();
                
                //Get Attribute type 3
                $companySizeAttributes = $this->userService->getAttributesByType(3);
                
                //Get Attribute type 5
                $businessAttributes = $this->userService->getAttributesByType(5);
                
                //get all role
                $roles = $this->userService->roles();
                $allRoles = array();
                foreach($roles as $row){
                    $allRoles[$row["id"]] = $row["title"];   
                }
                
                $listBureauId = array_keys($this->glConfig["bureauId"]);
                $listDepartmentId = array_keys($this->glConfig["departmentId"]);
                $departmentId = $this->glConfig["departmentId"];
                
                $list_user_id = array();
                $list_unique_user_delete = array();
                $list_email = array();
                
                $checkUserExistInUpload = array();
                $checkEmailExistInUpload = array();
                while (($line = fgets($handle)) !== false) {
                    if ($i >= 0) {
                        if (!empty(trim($line))) {
                            $line = strip_tags($line);
                            $line = @mb_convert_encoding($line, 'UTF-8', mb_detect_encoding($line));
                            $row = explode("\t", $line);

                            $data = array();
                            foreach ($this->mappingField as $key => $mapping) {
                                $data[$mapping["value"]] = isset($row[$key]) ? trim(strip_tags($row[$key])) : "";
                                if ($mapping["require"]) {
                                    $error .= $data[$mapping["value"]] == "" ? "ライン " . $j . ": [" . $mapping["name"] . "] " . $this->translate('MSG_UM_018_RequireField') . "<br />" : "";
                                }
                            }
                            $data["roleId"] = 0;
                            
                            // Validate role
                            $isValidateDelete = false;
                            if(!empty($data["role"])){
                                if(!in_array($data["role"], $allRoles)){
                                    $error .= "ライン " . $j . ": [ID種別] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_021_RoleNotExistInSystem'), 'Value', $data["role"]) . "<br />";
                                } else {
                                    $isValidateDelete = true;
                                    $data["roleId"] = array_search($data["role"], $allRoles);   
                                }                                
                            }
                            
                            // Validate userId dupplicate in file upload
                            if (!empty($data["userId"])) {
                                if (isset($checkUserExistInUpload[strtoupper($data["userId"])])) {
                                    $error .= "ライン " . $j . ": [ID] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_026_DupplicateUserInFile'), 'Value', $data["userId"]) . "<br />";        
                                } else {
                                    $checkUserExistInUpload[strtoupper($data["userId"])] = $data["userId"];
                                }
                            }
                            
                            // Validate Email dupplicate in file upload
                            if (!empty($data["email"])) {
                                if (isset($checkEmailExistInUpload[strtoupper($data["email"])])) {
                                    $error .= "ライン " . $j . ": [メールアドレス] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_034_DupplicateEmailInFile'), 'Value', $data["email"]) . "<br />";        
                                } else {
                                    $checkEmailExistInUpload[strtoupper($data["email"])] = $data["email"];
                                }
                            }
                            
                            if(empty($data["delete"])){
                                // case insert
                                
                                // Validate userId format
                                if(!empty($data["roleId"]) && !empty($data["userId"])){
                                    if(in_array($data["roleId"], \Application\Utils\ApplicationConst::USER_ADMIN_ARRAY) || in_array($data["roleId"], \Application\Utils\ApplicationConst::USER_INPUT_ARRAY)){
                                        
                                        if(!\Application\Utils\CommonUtils::validateUserAdminRegex($data["userId"])){
                                            $error .= "ライン " . $j . ": [ID] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_031_UserAdminRegex'), 'Value', $data["userId"]) . "<br />";    
                                        } else {
                                            //User admin, input
                                            $data["bureauId"] = "029";
                                            $data["departmentId"] = substr($data["userId"], 3, 2);
                                            $data["divisionId"] = substr($data["userId"], 5, 2);
                                           
                                            if(!in_array($data["departmentId"], $listDepartmentId)){
                                                $error .= "ライン " . $j . ": [ID] " . $this->translate('MSG_UM_024_DepartmentNotExistInSystem') . "<br />";   
                                            } else {
                                                
                                                $divisionArray = array_keys($departmentId[$data["departmentId"]]["divisionId"]);
                                                if(!in_array($data["divisionId"], $divisionArray)){
                                                    $error .= "ライン " . $j . ": [ID] " . $this->translate('MSG_UM_025_DivisiontNotExistInSystem') . "<br />";       
                                                }
                                            }   
                                        }   
                                    } else {
                                   
                                        if($data["roleId"]==\Application\Utils\ApplicationConst::USER_VIEW_3){
                                            if(!\Application\Utils\CommonUtils::validateUserViewRegex($data["userId"])){
                                                $error .= "ライン " . $j . ": [ID] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_032_UserViewRegex'), 'Value', $data["userId"]) . "<br />";    
                                            }    
                                        } else {
                                            if(!\Application\Utils\CommonUtils::validateUserEmailRegex($data["userId"])){
                                                $error .= "ライン " . $j . ": [ID] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_033_UserEmailRegex'), 'Value', $data["userId"]) . "<br />";    
                                            }    
                                        }
                                        //User view
                                        $data["bureauId"] = "";
                                        $data["departmentId"] = "";
                                        $data["divisionId"] = "";    
                                    }
                                }
                                
                                // Validate business
                                $data["businessId"] = "";
                                if(!empty($data["business"])){
                                    $business = $data["business"];
                                    $business = array_map('trim', explode("、", $business));
                                    $buninessValidate = array_diff($business, $businessAttributes);
                                    if(!empty($buninessValidate)){
                                        foreach($buninessValidate as $value){
                                            $error .= "ライン " . $j . ": [業種] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_020_ValueNotExistInSystem'), 'Value', $value) . "<br />";        
                                        }
                                        
                                    } else {
                                        $userBuniness = array();
                                        foreach($business as $attr){
                                            $attrId = array_search($attr, $businessAttributes);
                                            if(!in_array($attrId, $userBuniness)){
                                                $userBuniness[] = array_search($attr, $businessAttributes);    
                                            }   
                                        }
                                        
                                        $data["businessId"] = json_encode($userBuniness);
                                    }  
                                }    
                                
                                // Validate companySize
                                $data["companySizeId"] = "";
                                if(!empty($data["companySize"])){
                                    $companySize = $data["companySize"];
                                    $companySize = array_map('trim', explode("、", $companySize));
                                    $companySizeValidate = array_diff($companySize, $companySizeAttributes);
                                    if(!empty($companySizeValidate)){
                                        foreach($companySizeValidate as $value){
                                            $error .= "ライン " . $j . ": [企業規模] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_020_ValueNotExistInSystem'), 'Value', $value) . "<br />";        
                                        }
                                        
                                    } else {
                                        $userCompanySize = array();
                                        foreach($companySize as $attr){
                                            $attrId = array_search($attr, $companySizeAttributes);
                                            if(!in_array($attrId, $userCompanySize)){
                                                $userCompanySize[] = array_search($attr, $companySizeAttributes);    
                                            }
                                        }
                                        
                                        $data["companySizeId"] = json_encode($userCompanySize);
                                    }    
                                }
                                
                                // Validate region
                                if(!empty($data["region"]) && !in_array($data["region"],  \Application\Utils\ApplicationConst::USER_REGION)){
                                    $error .= "ライン " . $j . ": [地域] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_020_ValueNotExistInSystem'), 'Value', $data["region"]) . "<br />";            
                                }
                               
                                // Validate email
                                if(!empty($data["email"])){
                                    if(!\Application\Utils\CommonUtils::validateEmail($data["email"])){
                                        $error .= "ライン " . $j . ": [メールアドレス] " . $this->translate('MSG_UM_022_EmailWrongFormat') . "<br />";
                                    } else {
                                        if(!empty($data["userId"]) && !empty($data["roleId"]) && in_array($data["roleId"], \Application\Utils\ApplicationConst::USER_USE_EMAIL_FOR_ID) && $data["userId"]!=$data["email"]){
                                            $error .= "ライン " . $j . ": [メールアドレス] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_038_EmailMustEqualID'), 'Value', $data["role"]) . "<br />";
                                        }
                                    }                
                                } else {
                                    if(!empty($data["roleId"]) && in_array($data["roleId"], \Application\Utils\ApplicationConst::USER_USE_EMAIL_FOR_ID)){
                                        $error .= "ライン " . $j . ": [メールアドレス] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_039_EmailNotEmpty'), 'Value', $data["role"]) . "<br />";
                                    }    
                                }
                                
                                // Validate password
                                if(!empty($data["password"]) && !\Application\Utils\CommonUtils::validatePassword($data["password"])){
                                    $error .= "ライン " . $j . ": [パスワード] " . $this->translate('MSG_UM_005_WrongPassFormat') . "<br />";            
                                }
                                
                                if (!in_array($data["userId"], $list_user_id)) {
                                    $list_user_id[] = $data["userId"];
                                }
                                
                                if (!empty($data["email"]) && !in_array(md5(strtoupper($data["email"])), $list_email)) {
                                    $list_email[] = md5(strtoupper($data["email"]));
                                }
                            } else {
                                if(strtoupper($data["delete"])=="DELETE") {
                                    // Validate delete
                                    if($isValidateDelete){
                                        if(!in_array($data["roleId"], \Application\Utils\ApplicationConst::USER_CAN_DELETE)){
                                            $error .= "ライン " . $j . ": [削除] " . $this->translate('MSG_UM_023_UserCannotDelete') . "<br />";            
                                        }    
                                    }    
                                } else {
                                    if($isValidateDelete){
                                        $error .= "ライン " . $j . ": [削除] " . $this->translate('MSG_UM_028_DeleteWrongFormat') . "<br />";                   
                                    }
                                }
                                
                                $unique_user_delete = md5($data["roleId"] . "-" . $data["userId"]);
                                if (!in_array($unique_user_delete, $list_unique_user_delete)) {
                                    $list_unique_user_delete[] = $unique_user_delete;
                                } 
                            }
                            
                            $datas[$i] = $data;
                            
                            $j++;
                        }
                    }
                    
                    $i++;
                }
                
                if (!empty($error)) {
                    return $this->returnError($error);
                }
                @fclose($handle); 
                
                if (empty($datas)){ 
                    $error = $this->translate('MSG_UM_010_WrongFileEmpty');
                    return $this->returnError($error);
                }
                $maxLineOfImport = \Application\Utils\CommonUtils::getConfigFromFileIni("MAX_IMPORT_LINE", $this->getServiceLocator());
                if ($j > $maxLineOfImport + 1) {
                    $error = str_replace("[Value]", $maxLineOfImport, $this->translate('MSG_UM_037_MaxLineOfImport'));
                    return $this->returnError($error);
                }
                
                // Validation user exist in system
                $checkUserId = array();
                if (!empty($list_user_id)) {
                    $listUser = $this->userService->getUserInList($list_user_id);
                    foreach($listUser as $row){
                        $checkUserId[strtoupper($row["username"])] = strtoupper($row["username"]);
                    }
                }
                
                $checkUserDeleteId = array();
                if (!empty($list_unique_user_delete)) {
                    $checkUserDeleteId = $this->userService->getUserInListByUserIdNRole($list_unique_user_delete);
                }
                
                $checkEmail = array();
                if (!empty($list_email)) {
                    $checkEmail = $this->userService->getEmailInList($list_email);
                    
                }
                
                $totalInsert = 0;
                $totalDelete = 0;
                foreach ($datas as $i => $data) {
                    if(strtoupper($data["delete"])=="DELETE"){
                        $totalDelete++;
                        if(!isset($checkUserDeleteId[md5($data["roleId"] . "-" . $data["userId"])])){
                            $message = \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_030_UserIdNotExistInSystem'), 'Value', $data["userId"]);
                            $message = \Application\Utils\CommonUtils::replaceMessage($message, 'Role', $data["role"]);
                            $error .= "ライン " . ($i + 1) . ": [ID] " . $message . "<br />";                                
                        }    
                    } else {
                        $regionId = array_search($data["region"], \Application\Utils\ApplicationConst::USER_REGION);
                        
                        if($regionId) {
                            $datas[$i]["region"] = $regionId;   
                        } else {
                            $datas[$i]["region"] = 0;
                        }
                        
                        $totalInsert++; 
                        if(isset($checkUserId[strtoupper($data["userId"])])){
                            $error .= "ライン " . ($i + 1) . ": [ID] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_027_UserIdExistInSystem'), 'Value', $data["userId"]) . "<br />";                                
                        }
                        
                        if(!empty($data["email"]) && isset($checkEmail[md5(strtoupper($data["email"]))])){
                            $error .= "ライン " . ($i + 1) . ": [メールアドレス] " . \Application\Utils\CommonUtils::replaceMessage($this->translate('MSG_UM_029_EmailExistInSystem'), 'Value', $data["email"]) . "<br />";                                
                        }  
                    }

                    $datas[$i]["createDate"] = time();
                    $datas[$i]["updateDate"] = time();
                }
                
                if (!empty($error)) {
                    $message = "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Validate data error]\n";
                    if(\Application\Utils\ApplicationConst::DEBUG) @error_log($message, 3, $locationLog);
                    return $this->returnError($error);
                }
                
                $message = "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Finished validate data]\n";
                if(\Application\Utils\ApplicationConst::DEBUG) @error_log($message, 3, $locationLog);

                return $this->getResponse()->setContent(Json::encode(array(
                    'status' => true
                    , 'totalInsert' => $totalInsert
                    , 'totalDelete' => $totalDelete
                    , 'data' => json_encode($datas, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)   
                )));
            }
        
        } catch (\Exception $e){
            return $this->getResponse()->setContent(Json::encode(array(
                'status' => false
                , 'message' => $this->translate('MSG_UM_001_ServerError')
            )));  
        }
    }
    
    public function importUserProceedAction(){
        try{
            $request = $this->getRequest();
            $datas = json_decode($request->getPost("data"), true);
            
            $locationLog = getcwd() . "/public/static/import-user/import-user-" . $this->userInfo["username"] . date("Y-m-d") . ".log";
            
            if (!empty($datas)) {
                
                $dataInsert = array();
                $list_delete_user_id = array();
                foreach ($datas as $i => $data) {
                    if(strtoupper($data["delete"])=="DELETE"){
                        $list_delete_user_id[] = $data["userId"];
                    } else {
                        $data["password"] = empty($data["password"]) ? $this->glConfig["passwordDefault"] : $data["password"];
                        $data["password"] = \Application\Utils\CommonUtils::encrypt($data["password"]);
                        $data["divisionId"] = $data["divisionId"]=='00' ? '' : $data["divisionId"];

                        $dataInsert[] = $data;  
                    }                
                }

                $this->em->getConnection()->beginTransaction();
                try {
                    $message = "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Start insert, delete to Database]\n";
                    if (\Application\Utils\ApplicationConst::DEBUG) @error_log($message, 3, $locationLog);

                    $resultInsert = $this->userService->insertMultipleUser($dataInsert);
                    $resultDelete = $this->userService->deleteMultipleUser($list_delete_user_id);

                    $message = "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Finished insert, delete data to Database]\n";
                    if (\Application\Utils\ApplicationConst::DEBUG) @error_log($message, 3, $locationLog);

                    $this->em->getConnection()->commit();
                } catch (\Exception $e) {
                    $this->em->getConnection()->rollback();
                    
                    return $this->getResponse()->setContent(Json::encode(array(
                        'status' => false
                        , 'message' => $e->getMessage()
                    )));
                }
            }
            
            return $this->getResponse()->setContent(Json::encode(array(
                'status' => true,
                'message'=>''
            )));
        } catch (\Exception $e){
            return $this->getResponse()->setContent(Json::encode(array(
                'status' => false
                , 'message' => $this->translate('MSG_UM_001_ServerError')
            )));  
        }
    }
    
    public function returnError($error){
        if(empty($error)){
           return $this->getResponse()->setContent(Json::encode(array(
                'status' => false,
                'message'=>''
            )));
        } else {
            return $this->getResponse()->setContent(Json::encode(array(
                'status' => false,
                'message'=> $error
            )));
        }
    }
}
