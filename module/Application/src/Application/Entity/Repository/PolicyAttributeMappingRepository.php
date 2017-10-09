<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\ApplicationConst;
use Doctrine\ORM\EntityRepository;

class PolicyAttributeMappingRepository extends EntityRepository
{

    public function deleteAttributesByPolicy($policyId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyAttributeMapping', 'paMapping')
            ->where('paMapping.policyId = :policyId')
            ->setParameter(':policyId', intval($policyId));
        return $qb->getQuery()->execute();
    }

    public function getAttributeByMultiPolicy($policyIds)
    {
        if (!$policyIds || !is_array($policyIds)) {
            return false;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('paMapping')
            ->from('\Application\Entity\PolicyAttributeMapping', 'paMapping')
            ->where($qb->expr()->in('paMapping.policyId', ':policyIds'))
            ->setParameter(':policyIds', $policyIds);
        return $qb->getQuery()->getArrayResult();
    }

    public function insertMultiRows($arrNameField, $data)
    {
        $dateTime = new \DateTime('now');
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('Application\Entity\PolicyAttributeMapping')->getTableName();
        $sqlData = '';
        foreach ($data as $key => $value) {
            $sqlData .= '(' . intval($value['policyId']) . ', ' . $value['attributesPolicyId'] . ', ' . $value['attributeType'] . ', ' . $value['isSentMail'] . ', ' . $dateTime->getTimestamp() . ', ' . $dateTime->getTimestamp() . '),';
        }
        $sqlData = trim($sqlData, ',');
        $sql = "INSERT INTO " . $tableName . " (" . implode(',', $arrNameField) . ") VALUES " . $sqlData;

        return $em->getConnection()->executeQuery($sql);
    }

    public function queryPolicyIdByAttributes($listPolicyAttr, $numberType)
    {
        $em = $this->getEntityManager();
        $sql = $this->createQueryPolicyIdByAttributes($listPolicyAttr, $numberType);

        return $em->getConnection()->fetchAll($sql);
    }

    public function createQueryPolicyIdByAttributes($listPolicyAttr, $numberType, $isTemp)
    {
        $em = $this->getEntityManager();
        $subFixTable = $isTemp ? 'Temp' : '';
        $tableName = $em->getClassMetadata('Application\Entity\PolicyAttributeMapping' . $subFixTable)->getTableName();
        $sql = "SELECT p.policyId FROM (SELECT policyId, attributeType FROM " . $tableName . " WHERE attributesPolicyId IN (" . implode(',', $listPolicyAttr) . ")
 GROUP BY policyId, attributeType ORDER BY policyId, attributeType) as p GROUP BY p.policyId HAVING COUNT(p.policyId) = " . $numberType;

        return $sql;
    }

    public function createQueryPolicyIdByFreeWord($isTemp)
    {
        $em = $this->getEntityManager();
        $subFixTable = $isTemp ? 'Temp' : '';
        $tablePolicyAttributeMapping = $em->getClassMetadata('Application\Entity\PolicyAttributeMapping' . $subFixTable)->getTableName();
        $tablePolicyAttributes = $em->getClassMetadata('Application\Entity\PolicyAttributes')->getTableName();
        $listAttrSearch = implode(',', ApplicationConst::LIST_ARRTRIBUTES_SEARCH);
        $sql = "SELECT DISTINCT pam.policyId FROM $tablePolicyAttributeMapping as pam
                  INNER JOIN $tablePolicyAttributes as pa ON pam.attributesPolicyId = pa.id
                  WHERE pa.attributeType IN ($listAttrSearch)
                    AND pa.value LIKE :keyword";

        return $sql;
    }

    public function deleteDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\PolicyAttributeMapping', 'attribute')
            ->where($qb->expr()->in('attribute.policyId', ':policyId'))->setParameter(':policyId', $policyIds);

        return $qb->getQuery()->execute();
    }

    public function getDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return array();
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('poAttrMap')
            ->from('Application\Entity\PolicyAttributeMapping', 'poAttrMap')
            ->where($qb->expr()->in('poAttrMap.policyId', ':policyIds'))
            ->setParameter(':policyIds', $policyIds);
        return $qb->getQuery()->getArrayResult();
    }
}
