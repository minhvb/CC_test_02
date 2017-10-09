<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class RecruitmentTimeRepository extends EntityRepository
{
    public function getRecruitmentTimeByPolicy($policyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('recTime')
            ->from('\Application\Entity\RecruitmentTime', 'recTime')
            ->where('recTime.policyId = :policyId')
            ->setParameter(':policyId', intval($policyId));
        return $qb->getQuery()->getArrayResult();
    }

    public function getRecruitmentTimeByMultiPolicy($policyIds)
    {
        if (!$policyIds || !is_array($policyIds)) {
            return false;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('recTime')
            ->from('\Application\Entity\RecruitmentTime', 'recTime')
            ->where($qb->expr()->in('recTime.policyId', ':policyIds'))
            ->setParameter(':policyIds', $policyIds);
        return $qb->getQuery()->getArrayResult();
    }

    public function deleteRecruitmentTimeByPolicy($policyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\RecruitmentTime', 'recTime')
            ->where('recTime.policyId = :policyId')
            ->setParameter(':policyId', intval($policyId));
        return $qb->getQuery()->execute();
    }

    public function insertMultiRows($arrNameField, $data)
    {
        $dateTime = new \DateTime('now');
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('Application\Entity\RecruitmentTime')->getTableName();
        $sqlData = '';
        foreach ($data as $key => $value) {
            $sqlData .= '(' . intval($value['policyId']) . ', ' . ($value['startDate'] > 0 ? $value['startDate'] : 'NULL') . ', ' . ($value['deadline'] > 0 ? $value['deadline'] : 'NULL') . ', ' . ($value['endDate'] > 0 ? $value['endDate'] : 'NULL') . ', ' . $dateTime->getTimestamp() . ', ' . $dateTime->getTimestamp() . '),';
        }
        if (empty($sqlData)) {
            return false;
        }
        $sqlData = trim($sqlData, ',');
        $sql = "INSERT INTO " . $tableName . " (" . implode(',', $arrNameField) . ") VALUES " . $sqlData;
        return $em->getConnection()->executeQuery($sql);
    }

    public function deleteDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\RecruitmentTime', 'recr')
            ->where($qb->expr()->in('recr.policyId', ':policyId'))->setParameter(':policyId', $policyIds);
        return $qb->getQuery()->execute();
    }

}
