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
use Application\Entity\Survey;

class SurveyRepository extends EntityRepository {
	
  	public function getSurveyIdByPolicy( $policyId ){
    		$em = $this->getEntityManager();
    		$qb = $em->createQueryBuilder();
    		$qb->select('ps.surveyId')->from('\Application\Entity\PolicySurvey', 'ps')
    			->where('ps.policyId = :policyId')
    			->setParameter(':policyId', $policyId);
    		
    		return $qb->getQuery()->getArrayResult();
  	}
    
    public function addSurvey($data) {
        $em = $this->getEntityManager();
        $survey = new Survey();
        $survey->setName($data['surveyName']);
        $survey->setDescription($data['surveyDescription']);
        $survey->setCreateDate(time());
        $survey->setUpdateDate(time());

        $em->persist($survey);
        $em->flush();
          
        return $survey->getId();
    }

    public function updateSurvey($data) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->update('\Application\Entity\Survey', 's')
           ->set('s.name', ':name')
           ->set('s.description', ':description')
           ->set('s.updateDate', ':updateDate')
           ->where('s.id = :surveyId')
           ->setParameter(':name', $data['name'])
           ->setParameter(':description', $data['description'])
           ->setParameter(':updateDate', time())
           ->setParameter(':surveyId', (int)$data['surveyId']);

        $qb->getQuery()->execute();
        return $data['surveyId'];
    }

    public function delelteListSurvey($listSurvey) {
      if (!empty($listSurvey)) {
          $em = $this->getEntityManager();
          $qb = $em->createQueryBuilder();

          $qb->delete('\Application\Entity\Survey', 's')
              ->where($qb->expr()->in('s.id', $listSurvey ));

          return $qb->getQuery()->execute();
      }
    }
}
