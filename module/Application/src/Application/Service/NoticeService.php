<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service;

use Application\Service\ServiceInterface\NoticeServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Application\Utils\ApplicationConst;


class NoticeService implements NoticeServiceInterface, ServiceLocatorAwareInterface {
    use ServiceLocatorAwareTrait;

    /**
     * @return Notice List (assoc arrays)
     */
    public function getListNotice() {
        $noticeRepo = $this->getNoticeRepository();

        try {
            $notices = $noticeRepo->getListNotice();
            return $notices;
        } catch (NoResultException $ex) {
            throw new ValidationException('Not Found');
        }
    }

    public function getTotalNotices($search) {
        $noticeRepo = $this->getNoticeRepository();

        try {
            $totalResult = $noticeRepo->getTotalNotices($search);
            return (int)$totalResult;
        } catch (NoResultException $ex) {
            throw new NoResultException('Not Found');
        }
    }

    /**
     * @return Notice Info (array)
     * @param  notice ID (int)
     */

    public function getNoticeDetail($noticeId, $type, $firstResult = null, $resultPerPage = null) {
        $noticeRepo = $this->getNoticeRepository();
        $questionsRepo = $this->getQuestionsRepo();
        $answersRepo = $this->getAnswersRepo();
        
        if (!$type) {
            $noticeInfo = $noticeRepo->getNoticeDetail($noticeId);
            return $noticeInfo;
        } else {
            $noticeInfo = $noticeRepo->getNoticeDetail($noticeId);
            $questions = $questionsRepo->getListQuestionBySurvey($noticeInfo['surveyId'], $firstResult, $resultPerPage);
            foreach ($questions as $key => $question) {
                $answers = $answersRepo->getListAnswers($question['questionId']);
                $questions[$key]['answers'] = $answers;
            }
            $noticeInfo['questions'] = $questions;
            return $noticeInfo;
        }
    }

    public function getTotalNoticeQuestions($surveyId){
        $questionsRepo = $this->getQuestionsRepo();
        return $questionsRepo->getTotalQuestions($surveyId);
    }

    public function saveSurveyResponse ($data, $type) {
        $responseRepo = $this->getResponseRepo();
        return $responseRepo->insertMultipleResponse($data, $type);
    }

    public function getQuestionsRepo() {
        return $this->getEntityManager()->getRepository('Application\Entity\Questions');
    }

    public function getResponseRepo() {
        return $this->getEntityManager()->getRepository('Application\Entity\Response');
    }

    public function getAnswersRepo() {
        return $this->getEntityManager()->getRepository('Application\Entity\Answers');
    }

    public function getNoticeRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Notice');
    }

    public function getEntityManager() {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
}
