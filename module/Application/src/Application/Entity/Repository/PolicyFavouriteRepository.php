<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;
use Zend\Db\Sql\Join;

class PolicyFavouriteRepository extends EntityRepository
{

    public function deleteDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyFavourite', 'pFavourite')
            ->where($qb->expr()->in('pFavourite.policyId', ':policyId'))->setParameter(':policyId', $policyIds);

        return $qb->getQuery()->execute();
    }

//    public function getListFavouritePolicies($userId) {
//        $em = $this->getEntityManager();
//        $qb = $em->createQueryBuilder();
//        $qb->select('pv.id, pv.policyId')
//            ->from('\Application\Entity\PolicyFavourite', 'pv')
//            ->where($qb->expr()->eq('pv.userId', ':userId'))
//            ->setParameter(':userId', $userId);
//        return $qb->getQuery()->getArrayResult();
//    }

    public function getListFavouritePolicies($userId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pv.id, pv.policyId')
            ->from('\Application\Entity\PolicyFavourite', 'pv')
            ->leftJoin('\Application\Entity\Policy', 'p', Join::JOIN_LEFT, 'p.id = pv.policyId')
            ->leftJoin('\Application\Entity\PolicyTemp', 'pt', Join::JOIN_LEFT, 'pt.id = pv.policyId')
            ->where($qb->expr()->eq('pv.userId', ':userId'))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->lt('p.updateDateSchedule', ':currentTime'),
                        $qb->expr()->isNull('p.updateDateSchedule')
                    ),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('p.publishStartdate'),
                            $qb->expr()->isNotNull('p.publishEnddate'),
                            $qb->expr()->lte('p.publishStartdate', ':currentTime'),
                            $qb->expr()->gte('p.publishEnddate', ':currentTime')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('p.publishStartdate'),
                            $qb->expr()->isNull('p.publishEnddate'),
                            $qb->expr()->lte('p.publishStartdate', ':currentTime')
                        )
                    )
                ),
                $qb->expr()->andX(
                    $qb->expr()->gte('pt.updateDateSchedule', ':currentTime'),
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('pt.publishStartdate'),
                            $qb->expr()->isNotNull('pt.publishEnddate'),
                            $qb->expr()->lte('pt.publishStartdate', ':currentTime'),
                            $qb->expr()->gte('pt.publishEnddate', ':currentTime')
                        ),
                        $qb->expr()->andX(
                            $qb->expr()->isNotNull('pt.publishStartdate'),
                            $qb->expr()->isNull('pt.publishEnddate'),
                            $qb->expr()->lte('pt.publishStartdate', ':currentTime')
                        )
                    )
                )
            ))
            ->setParameter(':userId', $userId)
            ->setParameter(':currentTime', DateHelper::getCurrentTimeStamp());

        return $qb->getQuery()->getArrayResult();
    }

    public function insertMultiFavourite($userId, $policyIds)
    {
        $dateTime = DateHelper::getCurrentTimeStamp();
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('Application\Entity\PolicyFavourite')->getTableName();
        $sqlData = '';
        $arrNameField = array('userId', 'policyId', 'isSentMail', 'updateDate', 'createDate');
        foreach ($policyIds as $id) {
            $sqlData .= '(' . intval($userId) . ', ' . $id . ', 0, ' . $dateTime . ', ' . $dateTime . '),';
        }
        $sqlData = trim($sqlData, ',');
        $sql = "INSERT INTO " . $tableName . " (" . implode(',', $arrNameField) . ") VALUES " . $sqlData;

        return $em->getConnection()->executeQuery($sql);
    }

    public function deleteMultiFavourite($userId, $policyIds)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyFavourite', 'pv')
            ->where($qb->expr()->in('pv.policyId', ':policyId'))
            ->andWhere($qb->expr()->eq('pv.userId', ':userId'))
            ->setParameter(':policyId', $policyIds)
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->execute();
    }

    public function getDataByListPolicyAndListUser($policyIds, $userIds)
    {
        if (!is_array($policyIds) || !is_array($userIds) || !$userIds || !$policyIds) {
            return false;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pFav')
            ->from('\Application\Entity\PolicyFavourite', 'pFav')
            ->where($qb->expr()->in('pFav.policyId', ':policyIds'))
            ->andWhere($qb->expr()->in('pFav.userId', ':userIds'))
            ->setParameter(':policyIds', $policyIds)
            ->setParameter(':userIds', $userIds);
        return $qb->getQuery()->getArrayResult();
    }
}
