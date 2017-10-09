<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;

class PolicyTempRepository extends EntityRepository
{
    public function getDataHaveUpdateDateGreaterThanCurrentTime($policyId){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('policyTemp.id')
            ->from('\Application\Entity\PolicyTemp', 'policyTemp')
            ->where('policyTemp.id = :policyId')
            ->andWhere($qb->expr()->gt('policyTemp.updateDateSchedule', ':currentDate'))
            ->setParameter(':policyId', $policyId)
            ->setParameter(':currentDate', DateHelper::getCurrentTimeStamp());
        return $qb->getQuery()->getOneOrNullResult();
    }
    public function updateScheduleDateByPolicy($policyId, $updateDateSchedule)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->update('\Application\Entity\PolicyTemp', 'policyTemp')
            ->set('policyTemp.updateDateSchedule', ':updateDateSchedule')
            ->where('policyTemp.id = :policyId')
            ->setParameter(':updateDateSchedule', DateHelper::convertDateToNumber($updateDateSchedule))
            ->setParameter(':policyId', intval($policyId));
        return $qb->getQuery()->execute();
    }

    public function deletePolicyTempById($policyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyTemp', 'policyTemp')
            ->where('policyTemp.id = :policyId')
            ->setParameter(':policyId', intval($policyId));
        return $qb->getQuery()->execute();
    }

    public function deletePolicyAttributeMappingTempByPolicyId($policyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyAttributeMappingTemp', 'policyTemp')
            ->where('policyTemp.policyId = :policyId')
            ->setParameter(':policyId', intval($policyId));
        return $qb->getQuery()->execute();
    }

    public function deleteRecruitmentTimeTempByPolicyId($policyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\RecruitmentTimeTemp', 'policyTemp')
            ->where('policyTemp.policyId = :policyId')
            ->setParameter(':policyId', intval($policyId));
        return $qb->getQuery()->execute();
    }

    public function insertDataTempByPolicy($policyId, $updateDateSchedule = NULL)
    {
        if (!empty($updateDateSchedule)) {
            $sqlUpdateDate = DateHelper::convertDateToNumber($updateDateSchedule) . ' as updateDateSchedule';
        } else {
            $sqlUpdateDate = 'updateDateSchedule';
        }
        $em = $this->getEntityManager();
        $tablePolicyTemp = $em->getClassMetadata('Application\Entity\PolicyTemp')->getTableName();
        $tablePolicy = $em->getClassMetadata('Application\Entity\Policy')->getTableName();
        $sql = 'INSERT INTO ' . $tablePolicyTemp;
        $sql .= '(`id`, `bureauId`, `departmentId`,';
        $sql .= '`divisionId`, `shortName`, `name`, `recruitmentFlag`, `content`, `homepage`, `agencyImplement`,';
        $sql .= '`attentionFlag`, `recruitmentForm`, `purpose`, `contact`, `pdfFile`, `supportArea`, `detailOfSupportArea`,';
        $sql .= '`detailRecruitmentTime`, `publishStartdate`, `publishEnddate`, `emailNotificationFlag`, `createBy`,';
        $sql .= '`updateBy`, `createDate`, `updateDateSchedule`, `updateDateDisplay`, `updateDate`, `isDraft`)';
        $sql .= ' SELECT `id`, `bureauId`, `departmentId`,';
        $sql .= '`divisionId`, `shortName`, `name`, `recruitmentFlag`, `content`, `homepage`, `agencyImplement`,';
        $sql .= '`attentionFlag`, `recruitmentForm`, `purpose`, `contact`, `pdfFile`, `supportArea`, `detailOfSupportArea`,';
        $sql .= '`detailRecruitmentTime`, `publishStartdate`, `publishEnddate`, `emailNotificationFlag`, `createBy`,';
        $sql .= '`updateBy`, `createDate`, ' . $sqlUpdateDate . ', `updateDateDisplay`, `updateDate`, `isDraft` FROM ' . $tablePolicy . ' WHERE id = ' . intval($policyId) . '';
        return $em->getConnection()->executeQuery($sql);
    }

    public function insertDataAttributeTempByPolicy($policyId)
    {
        $em = $this->getEntityManager();
        $tableAttrPolicyTemp = $em->getClassMetadata('Application\Entity\PolicyAttributeMappingTemp')->getTableName();
        $tableAttrPolicy = $em->getClassMetadata('Application\Entity\PolicyAttributeMapping')->getTableName();

        $sql = 'INSERT INTO ' . $tableAttrPolicyTemp . '(`id`, `policyId`, `attributesPolicyId`, `attributeType`, `createDate`, `updateDate`)';
        $sql .= ' SELECT `id`, `policyId`, `attributesPolicyId`, `attributeType`, `createDate`, `updateDate` FROM ' . $tableAttrPolicy . ' WHERE policyId = ' . intval($policyId);
        return $em->getConnection()->executeQuery($sql);
    }

    public function insertDataRecruitmentTimeTempByPolicy($policyId)
    {
        $em = $this->getEntityManager();
        $tableAttrRecruitmentTimeTemp = $em->getClassMetadata('Application\Entity\RecruitmentTimeTemp')->getTableName();
        $tableAttrRecruitmentTime = $em->getClassMetadata('Application\Entity\RecruitmentTime')->getTableName();

        $sql = 'INSERT INTO ' . $tableAttrRecruitmentTimeTemp . '(`id`, `policyId`, `startDate`, `deadline`, `endDate`, `createDate`, `updateDate`)';
        $sql .= ' SELECT `id`, `policyId`, `startDate`, `deadline`, `endDate`, `createDate`, `updateDate` FROM ' . $tableAttrRecruitmentTime . ' WHERE policyId = ' . intval($policyId);
        return $em->getConnection()->executeQuery($sql);
    }

    public function deleteDataPolicyTempByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyTemp', 'policy')
            ->where($qb->expr()->in('policy.id', ':policyId'))->setParameter(':policyId', $policyIds);
        return $qb->getQuery()->execute();
    }

    public function deleteDataRecruitmentTimeByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\RecruitmentTimeTemp', 'recr')
            ->where($qb->expr()->in('recr.policyId', ':policyId'))->setParameter(':policyId', $policyIds);
        return $qb->getQuery()->execute();
    }

    public function deleteDataAttributesByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyAttributeMappingTemp', 'attribute')
            ->where($qb->expr()->in('attribute.policyId', ':policyId'))->setParameter(':policyId', $policyIds);
        return $qb->getQuery()->execute();
    }
}
