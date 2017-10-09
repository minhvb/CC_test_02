<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\ApplicationConst;
use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;

class QuestionsRepository extends EntityRepository
{
    public function getListQuestionBySurvey($surveyId, $firstResult = null, $resultPerPage = null) {
    	$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('q.content,q.id as questionId,q.typeQuestion')
            ->from('\Application\Entity\Questions', 'q')
            ->where('q.surveyId = :surveyId')
            ->setParameter(':surveyId',$surveyId);
        
        if ($firstResult !== null && $resultPerPage !== null) {
        	$qb->setFirstResult($firstResult)->setMaxResults($resultPerPage);
        }
        return $qb->getQuery()->getArrayResult();
    }
    
    public function getTotalQuestions($surveyId = null){
    	$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        if ($surveyId) {
        	 $qb->select('count(q.id)')
            ->from('\Application\Entity\Questions', 'q')
            ->where('q.surveyId = :surveyId')
            ->setParameter(':surveyId',$surveyId);

        	$totalResults = $qb->getQuery()->getSingleScalarResult();
        } else {
        	$qb->select('count(q.id)')
            ->from('\Application\Entity\Questions', 'q');

            $totalResults = $qb->getQuery()->getSingleScalarResult();
        }
       
        return (int)$totalResults;
    }
    
    public function getQuestionsBySurvey( $listSurveyId = array(), $page, $resultPerPage ){
    	$firstResult = ($page - 1) * $resultPerPage;
    	$em = $this->getEntityManager();
    	$qb = $em->createQueryBuilder();
    	if(is_array($listSurveyId)){
    		$qb->select('questions')->from('\Application\Entity\Questions', 'questions')
    		->where($qb->expr()->in('questions.surveyId', $listSurveyId ));
    		$qb->setFirstResult($firstResult)->setMaxResults($resultPerPage);
    	}
    	return $qb->getQuery()->getArrayResult();
    }
    
    public function getTotalResultBySurvey( $listSurveyId = array() ){
    	$em = $this->getEntityManager();
    	$qb = $em->createQueryBuilder();
    	if(is_array($listSurveyId)){
    		$qb->select('count(questions.id)')->from('\Application\Entity\Questions', 'questions')
    		->where($qb->expr()->in('questions.surveyId', $listSurveyId ));
    	}
    	try {
    		return $qb->getQuery()->getSingleScalarResult();
    	} catch (\Exception $ex) {
    		throw new \Exception;
    	}
    }

    public function addMultipleQuestionBySurvey($data, $surveyId){
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('Application\Entity\Questions')->getTableName();
        $qb = $em->createQueryBuilder();

        $sqlValue = "";

        foreach ($data as $key => $question) {
            $sqlValue .= ", ('" . $surveyId . "', '" . strip_tags($question["content"]) . "', '" . $question["questionType"] . "', ".time().", " . time() . ")" ;
        }

        $sqlValue = ltrim($sqlValue, ", ");
        $sql = "INSERT INTO `" .$tableName. "`(`surveyId`, `content`, `typeQuestion`, `createDate`, `updateDate`) VALUES " . $sqlValue;

        if (empty($sqlValue)) {
            return 0;
        }

        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeUpdate($sql);

        return $result;
    }
    // sonnt43 change typeQuestion in element questions - tomorow minhvb change questionType
    public function addMultipleQuestionBySurveyPolicy($data, $surveyId){
    	$em = $this->getEntityManager();
    	$qb = $em->createQueryBuilder();
    	
    	$sqlValue = "";
    	
    	foreach ($data as $key => $question) {
    		if(isset($question['changeType'])){
    			$question["typeQuestion"] = $question["changeType"];
    		}
    		$sqlValue .= ", ('', '" . $surveyId . "', '" . $question["content"] . "', '" . $question["typeQuestion"] . "', ".time().", " . time() . ")" ;
    	}
    	
    	$sqlValue = ltrim($sqlValue, ", ");
    	$sql = "INSERT INTO `".$tableName."`(`id`, `surveyId`, `content`, `typeQuestion`, `createDate`, `updateDate`) VALUES " . $sqlValue;
    	
    	if (empty($sqlValue)) {
    		return 0;
    	}
    	
    	$connection = $this->getEntityManager()->getConnection();
    	$result = $connection->executeUpdate($sql);
    	
    	return $result;
    }
    

    public function deleteQuestionBySurvey($surveyId) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->delete('\Application\Entity\Questions', 'q')
            ->where('q.surveyId = :surveyId')
            ->setParameter('surveyId', $surveyId);

        return $qb->getQuery()->execute();
    }

    public function deleteQuestionByListSurvey($listSurvey) {
        if (!empty($listSurvey)) {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->delete('\Application\Entity\Questions', 'q')
                ->where($qb->expr()->in('q.surveyId', $listSurvey ));

            return $qb->getQuery()->execute();
        }
    }

    public function getQuestionIdByListSurvey($listSurvey) {
        if (!empty($listSurvey)) {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->select('q.id as questionId')
                ->from('\Application\Entity\Questions', 'q')
                ->where($qb->expr()->in('q.surveyId', $listSurvey ));
            return $qb->getQuery()->getArrayResult();
        }
    }
}
