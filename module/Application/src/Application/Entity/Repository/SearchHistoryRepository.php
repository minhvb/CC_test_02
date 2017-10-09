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

class SearchHistoryRepository extends EntityRepository
{
    public function getListHistories($userId) {
    	$em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s.id, s.name, s.content')
            ->from('\Application\Entity\SearchHistory', 's', 's.id')
            ->where('s.userId = :userId')
            ->setParameter(':userId',$userId);

        return $qb->getQuery()->getArrayResult();
    }

    public function getHistoryById($userId, $id) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s.id, s.name, s.content')
            ->from('\Application\Entity\SearchHistory', 's')
            ->where('s.userId = :userId')
            ->andWhere('s.id = :id')
            ->setParameter(':userId',$userId)
            ->setParameter(':id',$id);

        return $qb->getQuery()->getSingleResult();
    }

    public function deleteHistory($userId, $id) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\SearchHistory', 's')
            ->where('s.userId = :userId')
            ->andWhere('s.id = :id')
            ->setParameter(':userId',$userId)
            ->setParameter(':id',$id);

        return $qb->getQuery()->execute();
    }
}
