<?php

namespace Application\Controller;

use Application\Utils\ApplicationConst;
use Application\Utils\DateHelper;
use Doctrine\ORM\NoResultException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;


class BaseController extends AbstractActionController {

    /* @var $viewModel \Zend\View\Model\ViewModel */
    protected $viewModel;

    protected $userInfo;
    protected $glConfig;
    protected $isPC = true;

    /**
     * @var \Zend\Mvc\I18n\Translator
     */
    protected $translator;

    /**
     * @var \Application\Service\ApplicationService
     */
    protected $appService;

    public function onDispatch(MvcEvent $e) {
        if (!$this->appService) {
            $this->appService = $this->getServiceLocator()->get('Application\Service\ApplicationServiceInterface');
        }
        if (!$this->glConfig) {
            $this->glConfig = $this->getServiceLocator()->get('Config');
        }
        if (!$this->translator) {
            $this->translator = $this->appService->getTranslator();
        }
        $mobileDetect = new \Application\Utils\MobileDetect();
        if($mobileDetect->isMobile() || $mobileDetect->isTablet()){
            $this->isPC = false;
        }

        $currentRoutes = $this->appService->getCurrentRoute($e);
        $this->userInfo = $this->appService->getUserInformation();

        $pageDontCheckLogin = array(
            'application/login/index',
            'application/login/loginfail',
            'application/login/forgotpassword',
            'application/login/newpassword',
            'application/login/sendemail',
            'application/login/updatepassword',
            'application/login/register',
            'application/login/registersuccess',
            'application/login/changepassword',
            'application/login/verifyemail',
            'application/login/securityquestion',
            'application/login/checkemail',
            'application/login/activeemail',
            'application/login/changeemailsuccess',
        );
        $publicPage = array(
            'application/login/activeemail',
            'application/login/changeemailsuccess',
            'application/login/logout',
            'application/login/accessdenied',
            'application/index/error',
        );
        if (!$this->userInfo) {
            if (!in_array($currentRoutes['route'], $pageDontCheckLogin)) {
                //check request ajax
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return $e->getResponse()->setStatusCode(ApplicationConst::ERROR_AJAX_EXPIRED_SESSION);
                } else {
                    $previousUrl = trim($this->getRequest()->getRequestUri(), '/');

                    return $this->redirect()->toRoute('login', array(), array(
                        'query' => array(
                            'url' => !empty($previousUrl) && $previousUrl != 'logout' ? $previousUrl : null,
                        ),
                    ));
                }
            }
        } else {
            if (!in_array($currentRoutes['route'], $publicPage) && !in_array($currentRoutes['route'], $pageDontCheckLogin)) {
                if (!$this->appService->isAccessPage($currentRoutes, $this->userInfo)) {
                    //check request ajax
                    if ($this->getRequest()->isXmlHttpRequest()) {
                        return $e->getResponse()->setStatusCode(ApplicationConst::ERROR_AJAX_ACCESS_DENIED);
                    } else {
                        return $this->redirect()->toRoute('application/default', array('controller' => 'login', 'action' => 'access-denied'));
                    }
                }
            }
        }
        
        $this->viewModel = new ViewModel();
        $this->viewModel->setVariable('userInfo', $this->userInfo);

        if ($this->userInfo) {
            $myPageService = $this->getServiceLocator()->get('Application\Service\MyPageServiceInterface');
            try{
                //get user info
                $userInfo = $myPageService->getUserInfo($this->userInfo["username"]);
            } catch (NoResultException $ex){
                \Application\Service\PrivateSession::clear(ApplicationConst::USER_INFO);
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return $e->getResponse()->setStatusCode(ApplicationConst::ERROR_AJAX_EXPIRED_SESSION);
                } else {
                    return $this->redirect()->toRoute('logout');
                }
            }

            //get setting mail
            $settingMailUserAttributes = $myPageService->getSettingMail($this->userInfo["userId"]);
            $settingMailUser = array();
            foreach($settingMailUserAttributes as $row){
                $settingMailUser[$row["attributesPolicyId"]] = $row["attributesPolicyId"];
            }

            //get attributes
            $allAttributes = $myPageService->getAllAttributes();
            $settingMailAttributes = array();
            foreach($allAttributes as $row){
                $attributes["id"] = $row["id"];
                $attributes["value"] = $row["valueOfSearch"];
                $settingMailAttributes[$row["attributeType"]][] = $attributes;
            }

            $attributePolicyType = $this->glConfig["attributePolicyType"];

            // get business attributes
            $businessAttributes = $myPageService->getBusinessAttributes();
            $userCanShowSQ = \Application\Utils\ApplicationConst::USER_CAN_SHOW_SQ;
            $securityQuestions = $this->appService->getAllSecurityQuestion();
            $messageConfirmUpdateInfo = $this->translate('MSG_MP_004_ConfirmUpdateInfo');
            $messageCannotOpenSettingMail = $this->translate('MSG_MP_022_CannotOpenSettingMail');

            // get list notice 
            $noticeList = $this->getServiceLocator()->get('Application\Service\NoticeServiceInterface')->getListNotice();
            $noticeList = $this->handlingNoticeData($noticeList);

            $this->layout()->setVariables(array(
                'userInfo' => $userInfo,
                'businessAttributes' => $businessAttributes,
                'userCanShowSQ' => $userCanShowSQ,
                'securityQuestions' => $securityQuestions,
                'messageConfirmUpdateInfo' => $messageConfirmUpdateInfo,
                'settingMailAttributes' => $settingMailAttributes,
                'attributePolicyType' => $attributePolicyType,
                'settingMailUser' => $settingMailUser,
                'messageCannotOpenSettingMail' => $messageCannotOpenSettingMail,
                'noticeList' => $noticeList,
                'isPC' => $this->isPC,
            ));
        }    
        
        return parent::onDispatch($e);
    }

    public function translate($msg) {
        return $this->translator->translate($msg);
    }

    protected function getReferPath(){
        return !$this->getRequest()->getHeader('Referer') ? false : $this->getRequest()->getHeader('Referer')->uri()->getPath();
    }

    protected function getBaseUrl(){
        $uri = $this->getRequest()->getUri();
        return $uri->getScheme() . '://' . $uri->getHost();
    }

    protected function getReferUrl(){
        if ($this->getRequest()->getHeader('Referer')) {
            $path = $this->getRequest()->getHeader('Referer')->uri()->getPath();
            $query = $this->getRequest()->getHeader('Referer')->uri()->getQuery();

            return $path . (empty($query)? '' : "?$query");
        }

        return false;
    }

    public function handlingNoticeData ($data) {
        foreach ($data as $key => $notice) {
            $data[$key]['gengoFirstPublicDate'] = DateHelper::convertTimestampToGengo($notice['firstPublicDate']);
            $data[$key]['href'] = ($notice['type'])?$this->url()->fromRoute('notice/default',array('action' => 'vote-notice-survey','id' => $notice['id'])) : $this->url()->fromRoute('notice/default',array('action' => 'notice-normal','id' => $notice['id']));
            if (strlen($notice['title']) > 25) {
               $data[$key]['title'] = mb_substr($notice['title'],0, 25, 'utf-8').'。。。';
            }
            if ( $notice['firstPublicDate'] > time() || $notice['lastPublicDate'] < time() ) {
                if ($notice['lastPublicDate']) {
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }

}
