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
use Administrator\Service\ServiceInterface\NoticeManagementServiceInterface;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Application\Utils\ApplicationConst;
use Zend\View\Model\JsonModel;
use Application\Utils\DateHelper;
use Zend\Json\Json;

use Application\Utils\BrowserHelper;
use Application\Utils\CharsetConverter;

class NoticeManagementController extends BaseController {

    /**
     * @var \Administrator\Service\NoticeManagementService
     */
    protected $noticeService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(NoticeManagementServiceInterface $noticeService, EntityManager $em) {
        $this->noticeService = $noticeService;
        $this->em = $em;
    }

    public function indexAction() {
        if ($this->userInfo['roleId']!=ApplicationConst::USER_ADMIN)
        {
            $this->redirect();
        }

        $search = $this->params()->fromQuery();

        $totalResults = $this->noticeService->getTotalNotices($search);

        $page = ($this->params()->fromQuery('page') !== null)?$this->params()->fromQuery('page'): 1;
        $resultPerPage = ApplicationConst::RESULT_PER_PAGE;
        $firstResult = $resultPerPage * ((int)$page - 1);
        $lastResult = $resultPerPage * (int)$page;
        $totalPages = ceil($totalResults / $resultPerPage);

        $notices = $this->noticeService->getListNoticeBySearch($firstResult, $resultPerPage, $search);
        foreach ($notices as $key => $notice) {
            $notices[$key]['firstPublicDate'] = date('Y/m/d',$notice['firstPublicDate']);
            if ($notices[$key]['lastPublicDate']) {
                $notices[$key]['lastPublicDate'] = date('Y/m/d',$notice['lastPublicDate']);
            } else {
                $notices[$key]['lastPublicDate'] = '。。。';
            }
            $notices[$key]['updateDate'] = date('Y/m/d H:i',$notice['updateDate']);
            $notices[$key]['href'] = ($notice['type']) ? 
                    $this->url()->fromRoute('notice-management/default',array('action' => 'editNoticeSurvey','id' => $notice['id'])) : 
                    $this->url()->fromRoute('notice-management/default',array('action' => 'editNoticeNormal','id' => $notice['id']));
        }
        // echo "<pre>";print_r($notices);die;

        $this->viewModel->setVariables(array(
                'totalResults' => $totalResults,
                'firstResult' => $firstResult,
                'lastResult'=> $lastResult,
                'resultPerPage'=> $resultPerPage,
                'totalPages'=> $totalPages,
                'page'=> $page,
                'currentUrl'=>'',
                'notices' => $notices
            ));
        return $this->viewModel;
    }

    public function addNoticeNormalAction(){
        return $this->viewModel;
    }

    public function editNoticeNormalAction(){
        $noticeId = $this->params()->fromRoute('id');
        $noticeInfo = $this->noticeService->getNoticeDetail($noticeId, 0);
        $noticeInfo['firstPublicDate'] = date('Y/m/d', $noticeInfo['firstPublicDate']);
        $noticeInfo['lastPublicDate'] = ($noticeInfo['lastPublicDate']) ? date('Y/m/d', $noticeInfo['lastPublicDate']) : '';
        $formURL = $this->url()->fromRoute('notice-management/default',array('action' => 'saveNoticeNormal','id' => $noticeInfo['noticeId']));
        $this->viewModel->setVariables(array(
            'noticeInfo' => $noticeInfo,
            'formURL'    => $formURL
        ));
        return $this->viewModel;
    }

    public function saveNoticeNormalAction(){
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->params()->fromPost('noticeId')) {
                $noticeId = $this->noticeService->updateNoticeNormal($this->params()->fromPost());
                return new JsonModel(array('noticeId' => $noticeId));
            } else {
                $noticeId = $this->noticeService->createNoticeNormal($this->params()->fromPost());
                return new JsonModel(array('noticeId' => $noticeId));
            }
        }
    }

    public function addNoticeSurveyAction(){
        return $this->viewModel;
    }

    public function saveNoticeSurveyAction(){
        if ($this->getRequest()->isXmlHttpRequest()) {
            if ($this->params()->fromPost('noticeId')) {
                $noticeId = $this->noticeService->updateNoticeSurvey($this->params()->fromPost());
                return new JsonModel(array('noticeId' => $noticeId));
            } else {
                $noticeId = $this->noticeService->createNoticeSurvey($this->params()->fromPost());
                return new JsonModel(array('noticeId' => $noticeId));
            }
        }
    }

    public function editNoticeSurveyAction(){
        $noticeId = $this->params()->fromRoute('id');
        $noticeInfo = $this->noticeService->getNoticeDetail($noticeId, 1);
        $noticeInfo['firstPublicDate'] = date('Y/m/d', $noticeInfo['firstPublicDate']);
        $noticeInfo['lastPublicDate'] = ($noticeInfo['lastPublicDate']) ? date('Y/m/d', $noticeInfo['lastPublicDate']) : '';

        $this->viewModel->setVariable('noticeInfo', $noticeInfo);

        return $this->viewModel;
    }

    public function reviewNoticeAction(){
        if ($this->params()->fromRoute('id') == null || $this->params()->fromRoute('type') == null){
            return $this->viewModel->setTemplate('error/404');
        }

        if ($this->params()->fromRoute('type') == 'normal') {
            $noticeInfo = $this->noticeService->getNoticeDetail($this->params()->fromRoute('id'), 0);
            
            if ($noticeInfo['type'])
                return $this->viewModel->setTemplate('error/404');

            $noticeInfo['firstPublicDate'] = DateHelper::convertTimestampToGengo($noticeInfo['firstPublicDate']);
            $noticeInfo['lastPublicDate'] = DateHelper::convertTimestampToGengo($noticeInfo['lastPublicDate']);
            $this->viewModel->setVariable('noticeInfo', $noticeInfo);
            return $this->viewModel;
        } elseif ($this->params()->fromRoute('type') == 'survey'){
            $noticeInfo = $this->noticeService->getNoticeDetail($this->params()->fromRoute('id'), 1);
            if (!$noticeInfo['type'])
                return $this->viewModel->setTemplate('error/404');

            $noticeInfo['firstPublicDate'] = DateHelper::convertTimestampToGengo($noticeInfo['firstPublicDate']);
            $noticeInfo['lastPublicDate'] = DateHelper::convertTimestampToGengo($noticeInfo['lastPublicDate']);
            // echo "<pre>";print_r($noticeInfo);die;
            $this->viewModel->setVariable('noticeInfo', $noticeInfo);
            return $this->viewModel;
        }
    }

    public function deleteNoticeAction(){
        $listNotice = Json::decode($this->params()->fromPost('notices'));
        $this->noticeService->deleteNotice($listNotice);

        return new JsonModel();
    }

    public function publicNoticeAction(){
        $listNotice = Json::decode($this->params()->fromPost('notices'));
        $this->noticeService->publicNotice($listNotice);

        return new JsonModel();
    }

    public function privateNoticeAction(){
        $listNotice = Json::decode($this->params()->fromPost('notices'));
        $this->noticeService->privateNotice($listNotice);

        return new JsonModel();
    }

    public function exportAction(){
        $responseInfo = $this->noticeService->getResponseDetailByNotice($this->params()->fromRoute('id'), $this->userInfo['userId']);
        /* export total login by role. group by day */
        $results = array();
        
        foreach($responseInfo as $key => $value) {
            $results[$value['userName']][] = $value;
        }

        // foreach ($results[1] as $key => $value) {
        //     if (isset($results[1][$key + 1]) && is_array($results[1][$key + 1]) && $results[1][$key + 1]['questionId'] === $results[1][$key]['questionId']) {
        //         $results[1][$key]['answerId'] = $results[1][$key]['answerId'] . ', ' . $results[1][$key + 1]['answerId'];
        //         unset($results[1][$key + 1]);
        //         $results[1] = array_values($results[1]);
        //     }
        // }
        foreach ($results as $rk => $result) {
            foreach ($result as $key => $value) {
                if (isset($result[$key + 1]) && is_array($result[$key + 1]) && $result[$key + 1]['questionId'] === $result[$key]['questionId']) {
                    $result[$key]['answerId'] = $result[$key]['answerId'] . ', ' . $result[$key + 1]['answerId'];
                    unset($result[$key + 1]);
                    $result = array_values($result);
                    $results[$rk] = $result;
                }
            }
        }
        echo "<pre>";print_r($results);die;
        $filename = 'ログイン数集計' . date("Ymd") . ".csv";
        
        /* create tmp file */
        $fp = tempnam("tmp", "csv");

        $data = array('User', 'Notice Name', 'Question', 'Answer By Id', 'Answer (Free text)');
        for ($i = 1; $i <= 50 ; $i++) { 
            array_push($data, 'question'.$i);
        }
        var_dump($data);die;
        /* create header */
        $this->putDataToFile($fp, $data);

        /* get data export */
        foreach ($responseInfo as $row) {
            $data = array($row['userName'], preg_replace( "/\r|\n/", "", $row['noticeTitle'] ),preg_replace( "/\r|\n/", "", $row['question'] ), $row['answerById'], $row['answer']);
            $this->putDataToFile($fp, $data);                        
        }
         
        $this->responseToBrowser($fp, $filename);
        exit();
    }

    private function putDataToFile($fp, $data){
        $string = pack("S", 0xfeff) . \Application\Utils\CommonUtils::chboData(iconv("UTF-8", "UTF-16LE", implode("\t", $data) . PHP_EOL));
        file_put_contents($fp, pack("S", 0xfeff) . $string, FILE_APPEND);    
    }

    private function responseToBrowser($fp, $filename){
        /* clear buffer */
        $objFileName = new CharsetConverter();
        $browser = BrowserHelper::getBrowserName($_SERVER['HTTP_USER_AGENT']);
        $filename = $browser != BrowserHelper::INTERNET_EXPLORER ? $objFileName->toUtf8($filename) : $objFileName->utf8ToShiftJis($filename);
        
        ob_clean();

        header('Content-Description: File Transfer');
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);       
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header('Content-Length: ' . filesize($fp));
        @readfile($fp);   
    }
}
