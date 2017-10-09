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

class AnswersRepository extends EntityRepository
{
    public function getListAnswers($questionId) {
    	$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a.content,a.id as answerId')
            ->from('\Application\Entity\Answers', 'a')
            ->where('a.questionId = :questionId')
            ->setParameter(':questionId',$questionId);

        return $qb->getQuery()->getArrayResult();
    }

    public function addMultipleAnswerByQuestion($data, $questionId) {
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('Application\Entity\Answers')->getTableName();
    	$sqlValue = "";

        foreach ($data as $key => $answer) {
            $sqlValue .= ", ('" . $questionId . "', '" . strip_tags($answer["content"]) . "', ".time().", " . time() . ")" ;
        }

        $sqlValue = ltrim($sqlValue, ", ");
        $sql = "INSERT INTO `".$tableName."`(`questionId`, `content`, `createDate`, `updateDate`) VALUES " . $sqlValue;

        if (empty($sqlValue)) {
            return 0;
        }

        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeUpdate($sql); 
        return $result;
    }

    public function addMultipleAnswerByMultipleQuestion($data) {
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('Application\Entity\Answers')->getTableName();
        $sqlValue = "";

        foreach ($data as $key => $question) {
            if (isset($question['answers']) && !empty($question['answers'])) {
                foreach ($question['answers'] as $answer) {
                    $sqlValue .= ", ('" . $key . "', '" . strip_tags($answer) . "', ".time().", " . time() . ")" ;
                }
            }
            
        }

        $sqlValue = ltrim($sqlValue, ", ");
        $sql = "INSERT INTO `".$tableName."`(`questionId`, `content`, `createDate`, `updateDate`) VALUES " . $sqlValue;

        if (empty($sqlValue)) {
            return 0;
        }

        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeUpdate($sql); 
        return $result;
    }

    public function deleteAnswerByListQuestion($listQuestionId) {
        if (!empty($listQuestionId)) {
            $em = $this->getEntityManager();
            $qb = $em->createQueryBuilder();

            $qb->delete('\Application\Entity\Answers', 'a')
                ->where($qb->expr()->in('a.questionId', $listQuestionId ));

            return $qb->getQuery()->execute();
        }
    }
}
