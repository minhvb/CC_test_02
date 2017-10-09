<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Exception\ValidationException;
use Application\Service\LoginService;
use Application\Service\ServiceInterface\LoginServiceInterface;
use Application\Utils\ApplicationConst;
use Application\Utils\CommonUtils;
use Application\Utils\UserHelper;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class LoginController extends BaseController
{

    const LAYOUT = 'layout/layout-login';
    /**
     * @var \Application\Service\LoginService
     */
    protected $loginService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(LoginServiceInterface $loginService, EntityManager $em) {
        $this->loginService = $loginService;
        $this->em = $em;
    }

    public function indexAction() {
        $this->layout(self::LAYOUT);
        $viewModel = $this->viewModel;
        $viewModel->setVariable('translator', $this->getMessages());
        $request = $this->getRequest();
        $role = $this->params()->fromRoute('role', LoginService::ROLE_VIEWER);
        $previousUrl = $this->params()->fromQuery('url');
        $viewModel->setVariable('role', $role);
        if ($request->isPost()) {
            $params = $this->params()->fromPost();
            $username = trim($params['username']);
            $password = $params['password'];
            try {
                if ($username === null || strlen($username) == 0 || $password === null || strlen($password) == 0) {
                    throw new ValidationException('validation fail!');
                }
                $authenResult = $this->loginService->authenticateUser($username, $password, $role);
                if ($authenResult === ApplicationConst::LOGIN_SUCCESS) {
                    return $this->routeUserLoggedIn($previousUrl);
                }
            } catch (ValidationException $ex) {
                $msg = $ex->getMessage();
                $errorCode = $ex->getCode();

                if ($errorCode === ApplicationConst::LOGIN_LOCKED_30MINUTES_ADMIN) {
                    return $this->redirect()->toRoute('login-fail', array(), array('query' => array('ref' => 1)));
                }

                if ($errorCode === ApplicationConst::LOGIN_LOCKED_30MINUTES_OTHER) {
                    return $this->redirect()->toRoute('login-fail');
                }

                if ($errorCode === ApplicationConst::LOGIN_PASSWORD_EXPIRE) {
                    $this->flashMessenger()->addInfoMessage($msg);
                    $query = $role != LoginService::ROLE_VIEWER ? array('ref' => $role) : array();

                    return $this->redirect()->toRoute('change-password', array(), array('query' => $query));
                }

                if ($errorCode === ApplicationConst::LOGIN_FIRST_TIME) {
                    $this->flashMessenger()->addInfoMessage($username);
                    $this->flashMessenger()->addInfoMessage($password);
                    $query = $role != LoginService::ROLE_VIEWER ? array('ref' => $role) : array();

                    return $this->redirect()->toRoute('security-question', array(), array('query' => $query));
                }

                // show error msg on login screen
                $viewModel->setVariables(array(
                    'form' => array(
                        'username' => $username,
                        'password' => $password,
                    ),
                    'errors' => array(
                        $this->translate($msg),
                    ),
                ));

            }
        }

        if ($request->isGet() && $this->appService->getUserInformation() != null) {
            return $this->routeUserLoggedIn($previousUrl);
        }

        if ($request->isGet() && $this->flashMessenger()->hasErrorMessages()) {
            // show error msg on login screen
            $viewModel->setVariables(array(
                'errors' => $this->flashMessenger()->getErrorMessages(),
            ));
        }

        return $viewModel;
    }

    public function routeUserLoggedIn($previousUrl = null) {
        if($previousUrl){
            return $this->redirect()->toUrl($previousUrl);
        }
        if ($this->appService->isUserAdmin()) {
            return $this->redirect()->toRoute('administrator/default', array("controller" => "menu"));
        }
        if ($this->appService->isUserInput()) {
            return $this->redirect()->toRoute('administrator/default', array("controller" => "menu"));
        }
        if ($this->appService->isUserView()) {
            return $this->redirect()->toRoute('home');

        }

        return $this->redirect()->toRoute('logout');
    }

    public function loginFailAction() {
        $this->layout(self::LAYOUT);
        $ref = $this->params()->fromQuery('ref');
        $errorMsg = $ref == 1 ? $this->translate('MSG_LG_002_WrongMore5') : $this->translate('MSG_LG_003_WrongMore10');

        $this->viewModel->setVariable('message', $errorMsg);

        return $this->viewModel;
    }

    public function logoutAction() {
        $ref = $this->params()->fromQuery('ref');
        if (empty($ref)) {
            $ref = UserHelper::isAdministrator($this->userInfo['roleId'])
                ? LoginService::ROLE_ADMIN
                : (UserHelper::isInputRole($this->userInfo['roleId']) ? LoginService::ROLE_INPUT : '');
        }
        $this->loginService->clearUserInfoSession();

        return $this->redirect()->toRoute('login/role', array('role' => $ref));
    }

    public function forgotPasswordAction() {
        $this->layout(self::LAYOUT);
        $request = $this->getRequest();
        $viewModel = $this->viewModel;
        $viewModel->setVariable('translator', $this->getMessages());
        $questions = $this->appService->getAllSecurityQuestionWithEmpty();
        $ref = $this->params()->fromQuery('ref');
        $viewModel->setVariables(array(
            'questions' => $questions,
            'ref' => $ref,
        ));
        $refQuery = !empty($ref) ? array('ref' => $ref) : array();
        if ($request->isPost()) {
            $username = $this->params()->fromPost('username');
            $mailFlag = $this->params()->fromPost('mailFlag');
            $questionId = $this->params()->fromPost('questionId');
            $answer = $this->params()->fromPost('answer');

            if (empty($mailFlag)) {
                try {
                    if (empty($username)) {
                        throw new ValidationException('validation fail!');
                    }
                    $this->loginService->sendMailChangePass($username);

                    return $this->redirect()->toRoute(
                        'forgot-password/default',
                        array('action' => 'send-email'),
                        array('query' => $refQuery)
                    );
                } catch (ValidationException $ex) {
                    $viewModel->setVariables(array(
                        'errors' => array(
                            $this->translate($ex->getMessage()),
                        ),
                        'username' => $username,
                        'ref' => $ref,
                    ));
                }
            }

            if ($mailFlag == 1) {
                try {
                    $this->loginService->validateUserQuestion($username, $questionId, $answer);
                    $this->flashMessenger()->addInfoMessage($username);

                    return $this->redirect()->toRoute(
                        'forgot-password/default',
                        array('action' => 'new-password'),
                        array('query' => $refQuery)
                    );
                } catch (ValidationException $ex) {
                    $viewModel->setVariables(array(
                        'errors' => array(
                            $this->translate($ex->getMessage()),
                        ),
                        'username' => $username,
                        'ref' => $ref,
                    ));
                    if($ex->getMessage() != 'MSG_LG_046_HaveEmail'){
                        $viewModel->setVariables(array(
                            'mailFlag' => 1,
                            'questionId' => $questionId,
                            'answer' => $answer,
                        ));
                    }
                }
            }
        }

        return $viewModel;
    }

    public function sendEmailAction() {
        $this->layout(self::LAYOUT);
        $this->viewModel->setVariable('ref', $this->params()->fromQuery('ref'));
        return $this->viewModel;
    }

    public function newPasswordAction() {
        $this->layout(self::LAYOUT);
        $request = $this->getRequest();
        /** @var ViewModel $viewModel */
        $viewModel = $this->viewModel;
        $viewModel->setVariable('translator', $this->getMessages());
        $ref = $this->params()->fromQuery('ref');
        $viewModel->setVariable('ref', $ref);

        // route from forgot-password
        if ($request->isGet() && $this->flashMessenger()->hasInfoMessages()) {
            $username = $this->flashMessenger()->getInfoMessages()[0];
            if ($this->loginService->isAdmin($username)) {
                return $this->redirect()->toRoute('login/role', array('role' => LoginService::ROLE_ADMIN));
            }
            $viewModel->setVariable('username', $username);

            return $viewModel;
        }

        // route from email change pass
        if ($request->isGet()) {
            $username = $this->params()->fromQuery('id');
            $token = $this->params()->fromQuery('token');
            try {
                if (!isset($username) || !isset($token)) {
                    throw new ValidationException();
                }
                if ($this->loginService->isAdmin($username)) {
                    return $this->redirect()->toRoute('login/role', array('role' => LoginService::ROLE_ADMIN));
                }
                $userInfo = $this->loginService->validateToken($username, $token);
                $viewModel->setVariable('username', $username);
                $viewModel->setVariable('fromEmail', true);
                $viewModel->setVariable('token', $token);
                $referPath = UserHelper::isInputRole($userInfo['roleId'])
                    ? $this->url()->fromRoute('login')
                    : $this->url()->fromRoute('login/role', array('role' => LoginService::ROLE_INPUT));
                $viewModel->setVariable('referPath', $referPath);
            } catch (ValidationException $ex) {
                if ($ex->getMessage() == 'MSG_LG_024_TokenExpired') {
                    // todo: route to expire token screen
                    $this->flashMessenger()->addErrorMessage($this->translate($ex->getMessage()));

                    return $this->redirect()->toRoute('login');
                }

                return $this->redirect()->toRoute('login');
            }

            return $viewModel;
        }

        return $this->redirect()->toRoute('login');
    }

    /**
     * @return \Zend\Http\Response|JsonModel
     */
    public function updatePasswordAction() {
        /** @var Request $request */
        $request = $this->getRequest();
        $this->viewModel->setVariable('translator', $this->getMessages());
        if ($request->isXmlHttpRequest()) {
            try {
                $username = trim($this->params()->fromPost('username', ''));
                $newPassword = $this->params()->fromPost('newPassword');
                $token = $this->params()->fromPost('token');

                if (empty($username) || !CommonUtils::validatePassword($newPassword)) {
                    throw new ValidationException('Validation fail!');
                }
                if (!empty($token)) {
                    $this->loginService->validateToken($username, $token);
                }
                $this->loginService->changePassword($username, $newPassword, $rest = true);
                $this->loginService->setTokenExpire($username);
                $jsonModel = new JsonModel();
                $jsonModel->setVariables(array(
                        'success' => true,
                        'errors' => array(),
                    )
                );

                return $jsonModel;
            } catch (ValidationException $ex) {
                $jsonModel = new JsonModel();
                $jsonModel->setVariables(array(
                        'success' => false,
                        'errors' => array(
                            $this->translate($ex->getMessage()),
                        ),
                    )
                );

                return $jsonModel;
            }
        }
        $this->getResponse()->setStatusCode(Response::STATUS_CODE_500);

        return new JsonModel();
    }

    public function registerAction() {
        $this->layout(self::LAYOUT);
        /** @var Request $request */
        $request = $this->getRequest();
        $viewModel = $this->viewModel;
        $viewModel->setVariable('translator', $this->getMessages());
        if ($request->isPost()) {
            try {
                $params = $this->params()->fromPost();
                $viewModel->setVariables(array(
                    'data' => $params,
                ));
                $this->loginService->validateUserInfo($params);
                $this->loginService->registerUser($params);
                $this->flashMessenger()->addInfoMessage(trim($params['email']));

                return $this->redirect()->toRoute('register-success');
            } catch (ValidationException $ex) {
                $questions = $this->appService->getAllSecurityQuestionWithEmpty();
                $viewModel->setVariables(array(
                    'questions' => $questions,
                    'errors' => array($this->translate($ex->getMessage())),
                ));

                return $viewModel;
            }
        }

        if ($request->isGet()) {
            $questions = $this->appService->getAllSecurityQuestionWithEmpty();
            $viewModel->setVariables(array(
                'questions' => $questions,
            ));

            return $viewModel;
        }

        return $this->viewModel;
    }

    public function registerSuccessAction() {
        $this->layout(self::LAYOUT);
        if (!$this->flashMessenger()->hasInfoMessages()) {
            return $this->redirect()->toRoute('login');
        }
        $username = $this->flashMessenger()->getInfoMessages()[0];
        $this->viewModel->setVariable('username', $username);

        return $this->viewModel;
    }

    public function verifyEmailAction() {
        $this->layout(self::LAYOUT);
        $request = $this->getRequest();
        $viewModel = $this->viewModel;
        $viewModel->setVariable('translator', $this->getMessages());
        if ($request->isGet()) {
            $username = $this->params()->fromQuery('id');
            $token = $this->params()->fromQuery('token');

            try {
                $this->loginService->activeUser($username, $token);
                // todo: route verify email success screen
                $this->flashMessenger()->addErrorMessage($this->translate('MSG_LG_025_VerifyEmailSuccess'));

                return $this->redirect()->toRoute('login');
            } catch (ValidationException $ex) {
                if ($ex->getMessage() == 'MSG_LG_024_TokenExpired') {
                    // todo: route to expire token screen
                    $this->flashMessenger()->addErrorMessage($this->translate($ex->getMessage()));

                    return $this->redirect()->toRoute('login');
                }
            }

            return $viewModel;
        }

        return $this->viewModel;
    }
    
    public function activeEmailAction() {
        $this->layout(self::LAYOUT);
        $request = $this->getRequest();

        $username = trim(strip_tags($this->params()->fromQuery('id', '')));
        $token = trim(strip_tags($this->params()->fromQuery('token', '')));
        
        if(empty($username) || empty($token)){
            return $this->redirect()->toRoute('login');
        }

        // Check email have actived
        $userInfo = $this->loginService->getUserByUsernameToken($username, $token);
        
        if(empty($userInfo)){
            return $this->redirect()->toRoute('login');    
        }

        if($request->isPost()) {
            $password = trim(strip_tags($this->params()->fromPost('password', '')));;

            $errors = array();
            if(empty($password)){
                $errors[] = "<p>" . $this->translate('MSG_MP_027_PasswordNotEmpty') . "</p>";    
            } else {
                if(\Application\Utils\CommonUtils::encrypt($password)!=$userInfo['password']){
                    $errors[] = "<p>" . $this->translate('MSG_MP_028_PasswordWasWrong') . "</p>";    
                }    
            }
            
            if(empty($userInfo["email"])) {
                $errors[] = "<p>" . $this->translate('MSG_MP_029_EmailEmpty') . "</p>";    
            } else {
                if(empty($userInfo["nextEmail"])){
                    $errors[] = "<p>" . $this->translate('MSG_MP_030_EmailActived') . "</p>";        
                }    
            }

            if(empty($errors)){
                //Active email
                $results = $this->loginService->activeEmail($username, $userInfo["nextEmail"], $userInfo["roleId"]);
                if($results>=0){
                    if(in_array($userInfo["roleId"], \Application\Utils\ApplicationConst::USER_USE_EMAIL_FOR_ID)){
                        $this->loginService->clearUserInfoSession();
                        return $this->redirect()->toRoute('change-email-success');
                    } else if($userInfo["roleId"]==\Application\Utils\ApplicationConst::USER_VIEW_3){
                        return $this->redirect()->toRoute('login/role', array('role' => ''));
                    } else if(in_array($userInfo["roleId"], \Application\Utils\ApplicationConst::USER_ADMIN_ARRAY)){
                        return $this->redirect()->toRoute('login/role', array('role' => LoginService::ROLE_ADMIN));
                    } else if(in_array($userInfo["roleId"], \Application\Utils\ApplicationConst::USER_INPUT_ARRAY)){
                        return $this->redirect()->toRoute('login/role', array('role' => LoginService::ROLE_INPUT));    
                    }     
                } else {
                    $errors[] = "<p>" . $this->translate('MSG_CM_001_ServerError') . "</p>";
                }
            } 
            
            $this->viewModel->setVariable('errors', $errors);  
        }
           
        return $this->viewModel;
    }
    
    public function changeEmailSuccessAction(){
        $this->layout(self::LAYOUT);
        return $this->viewModel;   
    }

    /**
     * Change password when expired
     */
    public function changePasswordAction() {
        $this->layout(self::LAYOUT);
        /** @var \HttpRequest $request */
        $request = $this->getRequest();
        /** @var ViewModel $viewModel */
        $viewModel = $this->viewModel;
        $viewModel->setVariable('translator', $this->getMessages());
        $ref = $this->params()->fromQuery('ref');
        $viewModel->setVariable('ref', $ref);
        if ($request->isGet() && $this->flashMessenger()->hasInfoMessages()) {
            $username = $this->flashMessenger()->getInfoMessages()[0];
            $viewModel->setVariable('username', $username);

            return $viewModel;
        }

        if ($request->isXmlHttpRequest()) {
            $username = $this->params()->fromPost('username');
            $oldPassword = $this->params()->fromPost('oldPassword');
            $newPassword = $this->params()->fromPost('newPassword');
            try {
                $this->loginService->checkOldPassword($username, $oldPassword);
                $this->loginService->changePassword($username, $newPassword);

                $jsonModel = new JsonModel();
                $jsonModel->setVariables(
                    array(
                        'success' => true,
                        'errors' => array(),
                    )
                );

                return $jsonModel;
            } catch (ValidationException $ex) {
                $jsonModel = new JsonModel();
                if ($ex->getMessage() == 'MSG_LG_001_WrongIdPass') {
                    $jsonModel->setVariables(array(
                        'success' => false,
                        'errors' => array(),
                        'redirect' => $this->url()->fromRoute('login'),
                    ));
                } else {
                    $jsonModel->setVariables(array(
                        'success' => false,
                        'errors' => array(
                            $this->translate($ex->getMessage()),
                        ),
                    ));
                }

                return $jsonModel;
            }
        }

        return $this->redirect()->toRoute('login');
    }

    public function securityQuestionAction() {
        $this->layout(self::LAYOUT);
        /** @var \HttpRequest $request */
        $request = $this->getRequest();
        /** @var ViewModel $viewModel */
        $viewModel = $this->viewModel;
        $viewModel->setVariable('translator', $this->getMessages());
        $questions = $this->appService->getAllSecurityQuestionWithEmpty();
        $viewModel->setVariable('questions', $questions);
        $ref = $this->params()->fromQuery('ref');
        $viewModel->setVariable('ref', $ref);

        if ($request->isGet() && $this->flashMessenger()->hasInfoMessages()) {
            $username = $this->flashMessenger()->getInfoMessages()[0];
            $password = $this->flashMessenger()->getInfoMessages()[1];
            $viewModel->setVariable('username', $username);
            $viewModel->setVariable('password', $password);

            return $viewModel;
        }

        if ($request->isPost()) {
            $username = trim($this->params()->fromPost('username'));
            $password = trim($this->params()->fromPost('password'));
            $questionId = $this->params()->fromPost('questionId');
            $answer = trim($this->params()->fromPost('answer'));
            try {
                $this->loginService->updateSecurityQuestion($username, $questionId, $answer);
                $authenResult = $this->loginService->authenticateUser($username, $password);
                if ($authenResult === ApplicationConst::LOGIN_SUCCESS) {
                    return $this->routeUserLoggedIn();
                }
            } catch (ValidationException $ex) {
                $viewModel->setVariables(
                    array(
                        'errors' => array(
                            $this->translate($ex->getMessage()),
                        ),
                        'username' => $username,
                        'password' => $password,
                        'questionId' => $questionId,
                        'answer' => $answer,
                        'ref' => $ref,
                    )
                );

                return $viewModel;
            }
        }

        return $this->redirect()->toRoute('login', array('role' => $ref));
    }

    public function checkEmailAction() {
        $this->layout(self::LAYOUT);
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $username = $this->params()->fromPost('username');
            try {
                $hasMail = $this->loginService->checkUserHasEmail($username);
                $jsonModel = new JsonModel();
                if (!$hasMail) {
                    $jsonModel->setVariables(array(
                        'success' => true,
                        'errors' => array(),
                    ));
                } else {
                    throw new ValidationException('MSG_LG_046_HaveEmail');
                }


                return $jsonModel;
            } catch (ValidationException $ex) {
                $jsonModel = new JsonModel();
                if ($ex->getMessage() == 'MSG_LG_001_WrongIdPass') {
                    $jsonModel->setVariables(array(
                        'success' => false,
                        'errors' => array(),
                        'redirect' => $this->url()->fromRoute('login'),
                    ));
                } else {
                    $jsonModel->setVariables(array(
                        'success' => false,
                        'errors' => array(
                            $this->translate($ex->getMessage()),
                        ),
                    ));
                }

                return $jsonModel;
            }
        }
    }

    public function getMessages()
    {
        return array(
            'MSG_LG_047_WrongPassFormat' => $this->translate('MSG_LG_047_WrongPassFormat'),
            'MSG_LG_048_ConfirmNewPassWrong' => $this->translate('MSG_LG_048_ConfirmNewPassWrong'),
            'MSG_LG_049_ConfirmEmailWrong' => $this->translate('MSG_LG_049_ConfirmEmailWrong'),
        );
    }
}
