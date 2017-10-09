<?php

namespace Application\Entity\Repository;

use Application\Utils\DateHelper;
use Application\Utils\ApplicationConst;
use Doctrine\ORM\EntityRepository;
use Zend\Db\Sql\Join;

class ResponseRepository extends EntityRepository {
	/**
     * @param $type: 0 -> notice, 1 -> policy
     *
     */
	public function insertMultipleResponse ($data , $type) {
		$em = $this->getEntityManager();
		$sqlValue = "";

		$tableResponse = $em->getClassMetadata('Application\Entity\Response')->getTableName();

		if (!$type) {
			foreach ($data['answers'] as $key => $answers) {
				$key = str_replace('question_', '', $key);
				foreach ($answers as $answer) {
					if(filter_var($answer, FILTER_VALIDATE_INT) ){
						$sqlValue .= ", (" . $data["userId"] . ", " . $data["surveyId"] . ", " . $key . ", " . $data["noticeId"] . ", " . $answer . " ,'',".time()." " . ")" ;
					} else {
						$sqlValue .= ", (" . $data["userId"] . ", " . $data["surveyId"] . ", " . $key . ", " . $data["noticeId"] . ", NULL, '" . strip_tags($answer) . "', ".time().")" ;
					}
				}
			}

			$sqlValue = ltrim($sqlValue, ", ");

			if (empty($sqlValue)) {
	            return 0;
	        }

	        $sql = "INSERT INTO `". $tableResponse ."` (`userId`, `surveyId`, `questionId`, `noticeId`, `answerId`, `answer`, `createDate`)VALUES " . $sqlValue;

	        $connection = $this->getEntityManager()->getConnection();
	        $result = $connection->executeUpdate($sql);

	        return $result;
		}
	}

	public function getResponseDetailByNotice($noticeId, $userId) {
		$em = $this->getEntityManager();

		$tableNotice = $em->getClassMetadata('Application\Entity\Notice')->getTableName();
		$tableResponse = $em->getClassMetadata('Application\Entity\Response')->getTableName();
		$tableQuestions = $em->getClassMetadata('Application\Entity\Questions')->getTableName();
		$tableAnswers = $em->getClassMetadata('Application\Entity\Answers')->getTableName();
		$tableUser = $em->getClassMetadata('Application\Entity\User')->getTableName();

		$sql = "SELECT u.userName, r.answer, r.questionId, r.answerId from response r LEFT JOIN user u ON r.userId = u.id group BY userId, questionId, answerId order BY userId, questionId";

		return $em->getConnection()->fetchAll($sql);
	}
	
	public function insertMultipleResponseByPolicy ($data ) {
		$em = $this->getEntityManager();
		$tableResponse = $em->getClassMetadata('Application\Entity\Response')->getTableName();
		$sqlValue = "";
		foreach ($data['question'] as $key => $value) {
			$answerId = empty($value["answer"]) ? 'NULL' : $value["answer"];
			$sqlValue .= ", ('" . $data["userId"] . "', '" . $data["surveyId"] . "', '" . $value["key"] . "', '" . $data["policyId"] . "', ".$answerId.", '" . $value["value"] . "')" ;
		}
		$sqlValue = ltrim($sqlValue, ", ");
		$sql = "INSERT INTO `".$tableResponse."` (`userId`, `surveyId`, `questionId`, `policyId`, `answerId`, `answer`)VALUES " . $sqlValue;
		if (empty($sqlValue)) {
			return 0;
		}
		$connection = $this->getEntityManager()->getConnection();
		$result = $connection->executeUpdate($sql); 
		return $result;
	}
	
	public function getTotalUserByPolicy($policyId){
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();		
		$qb->select($qb->expr()->countDistinct('Response.userId'))
			->from('\Application\Entity\Response', 'Response')
			->where('Response.surveyId = :surveyId')
			->andWhere('Response.policyId = :policyId')
			->groupBy('Response.userId')
			->setParameter(':surveyId', ApplicationConst::SURVEY_POLICY_ID )
			->setParameter(':policyId', $policyId);
		$totalResults = $qb->getQuery()->getArrayResult();
		return count($totalResults);
	}
}
?>