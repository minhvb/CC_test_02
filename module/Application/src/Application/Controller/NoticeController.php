<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Service\ServiceInterface\NoticeServiceInterface;
use Application\Utils\ApplicationConst;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\NoResultException;

class NoticeController extends BaseController
{   
    /**
     * @var \Application\Service\myPageService
     */
    protected $noticeService;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(NoticeServiceInterface $noticeService, EntityManager $em) {
        $this->noticeService = $noticeService;
        $this->em = $em;
    }

    public function noticeNormalAction() {
        if (!$this->params()->fromRoute('id')) {
            return $this->viewModel->setTemplate('error/404');
        } else {
            try {
                $noticeInfo = $this->noticeService->getNoticeDetail($this->params()->fromRoute('id'), 0);
            } catch (NoResultException $e) {
                return $this->viewModel->setTemplate('error/404');
            }

            if (!$noticeInfo['type']) {
                $this->viewModel->setVariable('noticeInfo', $noticeInfo);
                return $this->viewModel;
            } else {
                return $this->viewModel->setTemplate('error/404');
            }
        }
    }

    public function saveAction() {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $data = $this->params()->fromPost();
            $data['userId'] = $this->userInfo['userId'];
            try {
                $this->noticeService->saveSurveyResponse($data, 0);
                die;
            } catch (Exception $e) {
                throw new ValidationException('Submit Survey Failed');
            }
        }
        return new JsonModel();
    }

    public function voteNoticeSurveyAction() {
        if (!$this->params()->fromRoute('id')) {
           return $this->viewModel->setTemplate('error/404');
        } else {
            $resultPerPage = ApplicationConst::RESULT_PER_PAGE;
            $page = $this->params()->fromQuery('page');
            $page = ($page) ? $page : 1;
            $firstResult = ($page - 1) * $resultPerPage;

            $noticeInfo = $this->noticeService->getNoticeDetail(
                $this->params()->fromRoute('id'),
                1,
                $firstResult,
                ApplicationConst::RESULT_PER_PAGE
            );

            $totalResults = $this->noticeService->getTotalNoticeQuestions($noticeInfo['surveyId']);
            $totalPages = ceil($totalResults / $resultPerPage);
            if ($noticeInfo['type']) {
                $this->viewModel->setVariables(array(
                    'noticeInfo'    => $noticeInfo,
                    'page'          => $page,
                    'totalResults'  => $totalResults,
                    'totalPages'    => $totalPages,
                    'resultPerPage' => $resultPerPage,
                    'noticeId'      => $this->params()->fromRoute('id'),
                ));
                return $this->viewModel;
            } else {
                return $this->viewModel->setTemplate('error/notice-404');
            }
        }
    }
}