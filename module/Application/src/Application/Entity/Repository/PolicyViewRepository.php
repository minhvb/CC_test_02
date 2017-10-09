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

class PolicyViewRepository extends EntityRepository {

	public function getPolicyViewCurrentByUser( $userId ){
		$currentTime = DateHelper::getCurrentTimeStamp();
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->select('policyView.id as id, policyView.viewDate as viewDate')
			->from('\Application\Entity\PolicyView', 'policyView')
			->Where('policyView.userId = :userId')
			->setParameter(':userId', $userId)
			->addOrderBy('viewDate', 'DESC');
		$result = $qb->getQuery()->getArrayResult();
		return $result;
	}
	
	public function updatePolicyViewCurrent($policyId, $userId ){
		$currentTime = DateHelper::getCurrentTimeStamp();
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->update('\Application\Entity\PolicyView', 'policyView')
		->set('policyView.viewDate', ':viewDate' )
		->set('policyView.updateDate', ':updateDate' )
		->where('policyView.policyId = :policyId')
		->andWhere('policyView.userId = :userId')
		->setParameter(':policyId', $policyId)
		->setParameter(':userId', $userId)
		->setParameter(':viewDate', $currentTime)
		->setParameter(':updateDate', $currentTime);
		$result = $qb->getQuery()->getArrayResult();
		return $result;
	}

	public function deleteMultiplePolicyView($listIdView, $userId){
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->delete('\Application\Entity\PolicyView', 'policyView')
			->where($qb->expr()->notIn('policyView.id', ":list_id"))
			->andWhere('policyView.userId = :userId')
			->setParameter(":list_id", $listIdView)
			->setParameter(':userId', $userId);
		$result = $qb->getQuery()->execute();
		return $result;
	}

    public function deleteDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyView', 'policyView')
            ->where($qb->expr()->in('policyView.policyId', ':policyId'))
            ->setParameter(':policyId', $policyIds);

        return $qb->getQuery()->execute();
    }
}
