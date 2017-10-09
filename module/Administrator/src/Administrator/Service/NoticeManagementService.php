<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administrator\Service;

use Administrator\Service\ServiceInterface\NoticeManagementServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Application\Utils\ApplicationConst;
use Application\Entity\Notice;
use Application\Entity\Survey;
use Application\Entity\Questions;
use Application\Entity\Answers;

class NoticeManagementService implements NoticeManagementServiceInterface, ServiceLocatorAwareInterface {

    const TYPE_CREATE_ACTION= 'CREATE';

    const TYPE_UPDATE_ACTION= 'UPDATE';

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

    public function getNoticeRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Notice');
    }

    public function getSurveyRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Survey');
    }

    public function getQuestionsRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Questions');
    }

    public function getAnswersRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Answers');
    }

    public function getResponseRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Response');
    }

    public function createNoticeSurvey($dataNotice){
        $surveyRepo = $this->getSurveyRepository();
        $questionRepo = $this->getQuestionsRepository();
        $answerRepo = $this->getAnswersRepository();

        try {
            $surveyData = array(
                'surveyName' => $dataNotice['noticeTitle'],
                'surveyDescription' => $dataNotice['noticeDescription']
            );

            $surveyId = $surveyRepo->addSurvey($surveyData);
            $dataNotice['surveyId'] = $surveyId;
            $noticeId = $this->saveNotice(self::TYPE_CREATE_ACTION,$dataNotice);

            $questionRepo->addMultipleQuestionBySurvey($dataNotice['questions'],$surveyId);
            
            $questions = $questionRepo->getListQuestionBySurvey($surveyId);
            $questions = array_column($questions, 'questionId');
            $questions = array_combine($questions, $dataNotice['questions']);
            $answerRepo->addMultipleAnswerByMultipleQuestion($questions);

            return $noticeId;
        } catch (\Exception $e) {
            throw new \Exception('An Error Occured !');
        }
    }

    public function updateNoticeSurvey($dataNotice){
        $surveyRepo = $this->getSurveyRepository();
        $questionRepo = $this->getQuestionsRepository();
        $answerRepo = $this->getAnswersRepository();

        try {
            $noticeId = $this->saveNotice(self::TYPE_UPDATE_ACTION,$dataNotice);

            // Delete all old questions and answers
            $questions = $questionRepo->getListQuestionBySurvey($dataNotice['surveyId']);
            $listQuestion = array_column($questions, 'questionId');
            $questionRepo->deleteQuestionBySurvey($dataNotice['surveyId']);
            $answerRepo->deleteAnswerByListQuestion($listQuestion);

            // Add new questions and answers
            $questionRepo->addMultipleQuestionBySurvey($dataNotice['questions'],$dataNotice['surveyId']);
            $questions = $questionRepo->getListQuestionBySurvey($dataNotice['surveyId']);
            $questions = array_column($questions, 'questionId');
            $questions = array_combine($questions, $dataNotice['questions']);
            $answerRepo->addMultipleAnswerByMultipleQuestion($questions);

            return $noticeId;
        } catch (\Exception $e) {
            throw new \Exception('An Error Occured !');
        }
    }

    public function createNoticeNormal($dataNotice){
        return $this->saveNotice(self::TYPE_CREATE_ACTION,$dataNotice);
    }

    public function updateNoticeNormal($dataNotice){
        return $this->saveNotice(self::TYPE_UPDATE_ACTION,$dataNotice);
    }

    public function saveNotice($typeAction,$dataNotice){
        $noticeRepo = $this->getNoticeRepository();
        try {
            if ($typeAction == self::TYPE_UPDATE_ACTION) {
                return $noticeRepo->updateNotice($dataNotice);
            } elseif ($typeAction == self::TYPE_CREATE_ACTION) {
                return $noticeRepo->addNotice($dataNotice);
            }
        } catch (\Exception $e) {
            throw new \Exception('An Error Occured !');
        }
    }

    public function deleteNotice($listNotice) {
        $noticeRepo = $this->getNoticeRepository();
        $surveyRepo = $this->getSurveyRepository();
        $questionRepo = $this->getQuestionsRepository();
        $answerRepo = $this->getAnswersRepository();

        try {
            $listSurvey = $noticeRepo->getSurveyIdByListNotice($listNotice);
            if (is_array($listSurvey)) {
                $listSurvey = array_column($listSurvey, 'surveyId');
            }
            
            $listQuestion = $questionRepo->getQuestionIdByListSurvey($listSurvey);
            if (is_array($listQuestion)) {
               $listQuestion = array_column($listQuestion, 'questionId');
            }

            $noticeRepo->delelteListNotice($listNotice);
            $surveyRepo->delelteListSurvey($listSurvey);
            $questionRepo->deleteQuestionByListSurvey($listSurvey);
            $answerRepo->deleteAnswerByListQuestion($listQuestion);

            return true;
        } catch (\Exception $e) {
            throw new \Exception('An Error Occured !');
        }
    }

    public function privateNotice($listNotice) {
        $noticeRepo = $this->getNoticeRepository();
        try {
            $noticeRepo->privateNotice($listNotice);
        } catch (\Exception $e) {
            throw new \Exception('An Error Occured !');
        }
    }

    public function publicNotice($listNotice) {
        $noticeRepo = $this->getNoticeRepository();
        try {
            $noticeRepo->publicNotice($listNotice);
        } catch (\Exception $e) {
            throw new \Exception('An Error Occured !');
        }
    }

    public function getListNotice($firstResult, $resultPerPage){
        $noticeRepo = $this->getNoticeRepository();

        try {
            $notices = $noticeRepo->getListNotice($firstResult, $resultPerPage);
            return $notices;
        } catch (NoResultException $ex) {
            throw new NoResultException('Not Found');
        }
    }

    public function getListNoticeBySearch($firstResult, $resultPerPage, $search){
        $noticeRepo = $this->getNoticeRepository();

        try {            
            $notices = $noticeRepo->getListNoticeBySearch($firstResult, $resultPerPage, $search);
            return $notices;
        } catch (NoResultException $ex) {
            throw new NoResultException('Not Found');
        }
    }

    public function getTotalNotices($search = array()) {
        $noticeRepo = $this->getNoticeRepository();

        try {
            $totalResult = $noticeRepo->getTotalNotices($search);
            return (int)$totalResult;
        } catch (NoResultException $ex) {
            throw new NoResultException('Not Found');
        }
    }

    public function getNoticeDetail($noticeId, $type, $firstResult = null, $resultPerPage = null) {
        $noticeRepo = $this->getNoticeRepository();
        
        try {
            if (!$type) {
                $noticeInfo = $noticeRepo->getNoticeDetail($noticeId);
                return $noticeInfo;
            } else {
                $questionsRepo = $this->getQuestionsRepository();
                $answersRepo = $this->getAnswersRepository();
                $noticeInfo = $noticeRepo->getNoticeDetail($noticeId);
                $questions = $questionsRepo->getListQuestionBySurvey($noticeInfo['surveyId'], $firstResult, $resultPerPage);
                foreach ($questions as $key => $question) {
                    $answers = $answersRepo->getListAnswers($question['questionId']);
                    $questions[$key]['answers'] = $answers;
                }
                $noticeInfo['questions'] = $questions;
                return $noticeInfo;
            }
        } catch (NoResultException $ex) {
            throw new NoResultException('Not Found');
        }
    }

    public function getResponseDetailByNotice($noticeId, $userId) {
        $responseRepo = $this->getResponseRepository();

        try {
            $responseInfo = $responseRepo->getResponseDetailByNotice($noticeId, $userId);
            return $responseInfo;
        } catch (NoResultException $ex) {
            throw new NoResultException('Not Found');
        }
    }
}