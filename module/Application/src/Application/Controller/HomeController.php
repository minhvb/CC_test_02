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
use Application\Service\PrivateSession;
use Application\Service\ServiceInterface\HomeServiceInterface;
use Application\Utils\ApplicationConst;
use Application\Utils\CommonUtils;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Zend\Http\Headers;
use Zend\Http\Response\Stream;
use Zend\View\Model\JsonModel;

class HomeController extends BaseController
{

    /**
     * @var \Application\Service\HomeService
     */
    protected $homeService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(HomeServiceInterface $homeService, EntityManager $em) {
        $this->homeService = $homeService;
        $this->em = $em;
    }

    public function indexAction() {
        
        /* export log performance */
        $locationLog = getcwd() . "/public/static/performance/home" . $this->userInfo["username"] . date("Y-m-d") . ".log";
        $message = " \n ======================================== \n";
        if ($this->getRequest()->isPost()){
            $message .= "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Start search in home screen]\n";    
        } else {
            $message .= "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Start action home]\n";   
        }
        if (\Application\Utils\ApplicationConst::DEBUG) @error_log($message, 3, $locationLog);
        
        try {
            $currentUrl = $this->getRequest()->getRequestUri();
            $request = $this->getRequest();
            $searchParams = null;
            $query = $this->params()->fromQuery();
            $filter = $this->params()->fromQuery('filter', 'new');
            $page = isset($query['page']) && intval($query['page']) > 1 ? intval($query['page']) : 1;
            $resultPerPage = ApplicationConst::RESULT_PER_PAGE;
            $userId = $this->userInfo['userId'];
            $referQuery = $this->getRequest()->getHeader('Referer') ? $this->getRequest()->getHeader('Referer')->uri()->getQueryAsArray() : null;
            $referPath = $this->getRequest()->getHeader('Referer') ? $this->getRequest()->getHeader('Referer')->uri()->getPath() : null;

            $isKeepPage = $referPath == '/'
                && ((!isset($referQuery['filter']) && $filter == 'new') || (isset($referQuery['filter']) && $referQuery['filter'] == $filter))
                && in_array($filter, array('new', 'access', 'deadline'));
            // keep search param
            if ($request->isPost()) {
                $searchParams = $this->params()->fromPost();
                $searchParams['searchText'] = trim($searchParams['searchText']);
                $searchParams['searchBoxStatus'] = !empty($searchParams['searchBoxStatus']) ? $searchParams['searchBoxStatus'] : 'box0';
                PrivateSession::setData('homeSearchParams', json_encode($searchParams));

                $currentUrl = $this->replacePage($currentUrl, $page, 1);
                return $this->redirect()->toUrl($currentUrl);
            } elseif ($isKeepPage){
                $searchParams = !PrivateSession::isEmpty('homeSearchParams') ? json_decode(PrivateSession::getData('homeSearchParams'), true) : null;
            } else {
                PrivateSession::clear('homeSearchParams');
            }

            list($data, $totalPages, $totalResults) = $this->homeService->queryPublishPolicies($searchParams, $filter, $page, $resultPerPage, $userId);

            if ($totalPages >= 1 && $page > $totalPages) {
                $currentUrl = $this->replacePage($currentUrl, $page, $totalPages);

                return $this->redirect()->toUrl($currentUrl);
            }

            $allAttributes = $this->homeService->getAllAttributes();
            $searchAttributes = array();
            foreach ($allAttributes as $row) {
                $attributes["id"] = $row["id"];
                $attributes["value"] = $row["valueOfSearch"];
                $searchAttributes[$row["attributeType"]][] = $attributes;
            }

            $searchHistories = $this->homeService->getListHistories($this->userInfo['userId']);

            $errorMsg = $this->flashMessenger()->hasErrorMessages() ? $this->flashMessenger()->getErrorMessages()[0] : null;

            $this->viewModel->setVariables(array(
                'username' => $this->userInfo['username'],
                'data' => $data,
                'totalPages' => $totalPages,
                'totalResults' => $totalResults,
                'page' => $page,
                'resultPerPage' => $resultPerPage,
                'currentUrl' => $currentUrl,
                'filter' => $filter,
                'searchAttributes' => $searchAttributes,
                'searchParams' => $searchParams,
                'searchHistories' => $searchHistories,
                'errorMsg' => $errorMsg,
                'translator' => $this->getMessages(),
            ));
            
            if (!empty($searchParams)){
                $message = "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Stop search in home screen]\n";
            } else {
                $message = "[" . date("Y-m-d H:i:s") . " " . end(explode(".", microtime(true))) . "][Stop action home]\n";
            }
                
            if (\Application\Utils\ApplicationConst::DEBUG) @error_log($message, 3, $locationLog);
        }catch (\Exception $ex){
            throw $ex;
        }
        return $this->viewModel;
    }

    public function changePasswordAction() {
        $params = $this->params()->fromPost();
        $result = $this->homeService->checkUserCurrentPassExist($this->userInfo['username'], $params['currentPaword']);
        if ($result == ApplicationConst::LOGIN_SUCCESS) {

        } else {
            $response = $this->getResponse();
            $response->setStatusCode(200);
            $response->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $response->setContent(json_encode('ms1'));

            return $response;
        }
    }

    public function downloadAction() {
        $year = $this->params()->fromRoute('year');
        $month = $this->params()->fromRoute('month');
        $day = $this->params()->fromRoute('day');
        $fileName = $this->params()->fromRoute('fileName');
        $policyId = $this->params()->fromRoute('policyId');
        try {
            $file = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->glConfig['folderUploadPdf'] . "/$year/$month$day/$fileName";
            if (!file_exists($file)) {
                $this->flashMessenger()->addErrorMessage($this->translate('MSG_HO_011_FileNotFound'));
                return $this->redirect()->toUrl($this->getReferUrl());
            }
            $response = new Stream();
            $response->setStream(fopen($file, 'r'));
            $response->setStatusCode(200);
            $response->setStreamName(basename($file));
            $headers = new Headers();
            $headers->addHeaders(array(
                'Content-Disposition' => 'attachment; filename="' . basename($file) . '"',
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => filesize($file),
                'Expires' => '@0', // @0, because zf2 parses date as string to \DateTime() object
                'Cache-Control' => 'must-revalidate',
                'Pragma' => 'public',
            ));
            $response->setHeaders($headers);
            if(isset($policyId)){
            	$this->homeService->updateDownloadPolicyStatistic($policyId, $this->userInfo["roleId"]);
            }
            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function addFavouriteAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $policyIds = $this->params()->fromPost('policyIds');
            $jsonModel = new JsonModel();
            try {
                list($listNew, $listExist) = $this->homeService->addFavouriteMultiPolicies($this->userInfo['userId'], $policyIds);

                if(empty($listNew)){
                    throw new ValidationException('MSG_HO_017_FavouriteExistError');
                }
                $jsonModel->setVariables(
                    array(
                        'success' => true,
                        'data' => array(
                            'listNew' => $listNew,
                            'listExist' => $listExist,
                        ),
                        'msg' => $this->translate('MSG_HO_003_AddFavouriteSuccess'),
                        'errors' => array(),
                    )
                );
            } catch (ValidationException $ex) {
                $jsonModel->setVariables(array(
                    'success' => false,
                    'msg' => $this->translate($ex->getMessage()),
                ));

            }

            return $jsonModel;
        }

        return $this->redirect()->toRoute('home');
    }

    public function removeFavouriteAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $policyIds = $this->params()->fromPost('policyIds');
            $jsonModel = new JsonModel();
            try {
                $listNew = $this->homeService->removeFavouritePolicies($this->userInfo['userId'], $policyIds);

                $jsonModel->setVariables(
                    array(
                        'success' => true,
                        'list' => $listNew,
                        'msg' => $this->translate('MSG_HO_004_RemoveFavouriteSuccess'),
                        'errors' => array(),
                    )
                );
            } catch (ValidationException $ex) {
                $jsonModel->setVariables(array(
                    'success' => false,
                    'msg' => $this->translate($ex->getMessage()),
                ));

            }

            return $jsonModel;
        }

        return $this->redirect()->toRoute('home');
    }

    public function getListSearchHistoryAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $jsonModel = new JsonModel();
            try {
                $listNew = $this->homeService->getListSearchHistory($this->userInfo['userId']);

                $jsonModel->setVariables(
                    array(
                        'success' => true,
                        'list' => $listNew,
                        'msg' => $this->translate('MSG_HO_003_AddFavouriteSuccess'),
                        'errors' => array(),
                    )
                );
            } catch (ValidationException $ex) {
                $jsonModel->setVariables(array(
                    'success' => false,
                    'msg' => $this->translate($ex->getMessage()),
                ));

            }

            return $jsonModel;
        }

        return $this->redirect()->toRoute('home');
    }

    public function addSearchHistoryAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $jsonModel = new JsonModel();
            try {
                $searchParams = $this->params()->fromPost();
                $name = $this->params()->fromQuery('name');
                $policyId = $this->params()->fromQuery('policyId');
                $name = trim($name);
                if (empty($name) || empty($searchParams)) {
                    throw new ValidationException('MSG_HO_005_HistoryNameEmpty');
                }
                if (!CommonUtils::validateFullSize($name) || CommonUtils::lengthFullSize($name) > 50) {
                    throw new ValidationException('MSG_HO_016_MaxNameHistory');
                }
                if(intval($policyId) === null){
                    throw new ValidationException('validation fail!');
                }
                $searchParams['searchText'] = trim($searchParams['searchText']);
                $searchParams['searchBoxStatus'] = !empty($searchParams['searchBoxStatus']) ? $searchParams['searchBoxStatus'] : 'box0';

                $this->homeService->addSearchHistory($this->userInfo['userId'], $name, json_encode($searchParams), $policyId);
                PrivateSession::setData('homeSearchParams', json_encode($searchParams));
                $jsonModel->setVariables(
                    array(
                        'success' => true,
                        'msg' => $this->translate('MSG_HO_006_AddNameSuccess'),
                        'errors' => array(),
                    )
                );
            } catch (ValidationException $ex) {
                $jsonModel->setVariables(array(
                    'success' => false,
                    'errors' =>array( $this->translate($ex->getMessage())),
                ));

            }

            return $jsonModel;
        }

        return $this->redirect()->toRoute('home');
    }

    public function loadSearchHistoryAction(){
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $jsonModel = new JsonModel();
            try {
                $historyId = $this->params()->fromPost('historyId');
                if (empty($historyId)) {
                    throw new ValidationException('MSG_HO_009_SelectHistory');
                }

                $history = $this->homeService->getHistory($this->userInfo['userId'], $historyId);
                PrivateSession::setData('homeSearchParams', $history['content']);

                $jsonModel->setVariables(
                    array(
                        'success' => true,
                        'msg' => $this->translate('MSG_HO_006_AddNameSuccess'),
                        'errors' => array(),
                    )
                );
            }catch (NoResultException $ex) {
                $jsonModel->setVariables(array(
                    'success' => false,
                    'errors' =>array( $this->translate('MSG_AP_009_ServerError')),
                ));

            }
            catch (ValidationException $ex) {
                $jsonModel->setVariables(array(
                    'success' => false,
                    'errors' =>array( $this->translate($ex->getMessage())),
                ));

            }

            return $jsonModel;
        }

        return $this->redirect()->toRoute('home');
    }

    public function exportComparePolicyAction(){
        $policyIds = $this->params()->fromPost('exportIdsCompare', '');
        $policyIds = explode(',' , $policyIds);
        try{
            $response  = $this->homeService->getDataComparePolicy($this->getResponse(), $policyIds, $this->getBaseUrl());
            return $response;
        }catch (ValidationException $ex){
            $this->flashMessenger()->addErrorMessage($this->translate($ex->getMessage()));
            return $this->redirect()->toUrl($this->getReferUrl());
        }
    }

    private function getMessages(){
        return array(
            'MSG_HO_002_EmptyPolicyIds' => $this->translate('MSG_HO_002_EmptyPolicyIds'),
            'MSG_HO_010_MaxMinCompare' => $this->translate('MSG_HO_010_MaxMinCompare'),
            'MSG_HO_012_SearchHistoryEmpty' => $this->translate('MSG_HO_012_SearchHistoryEmpty'),
            'MSG_HO_013_SearchConditionEmpty' => $this->translate('MSG_HO_013_SearchConditionEmpty')
        );
    }

    private function replacePage($currentUrl, $currentPage, $newPage){
        $currentUrl = str_replace("?page=$currentPage", "?page=$newPage", $currentUrl);
        $currentUrl = str_replace("&page=$currentPage", "&page=$newPage", $currentUrl);
        return $currentUrl;
    }
}
