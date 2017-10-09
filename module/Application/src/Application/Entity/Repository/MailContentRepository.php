<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;

class MailContentRepository extends EntityRepository
{
    public function createMailContent($listPolicy, $userId)
    {
        // create mail content        
        $sqlValue = "";
        foreach ($listPolicy as $policyId) {
            $sqlValue .= ", (" . $userId . ", " . $policyId . ", " . time() . ", " . time() . ")";
        }

        $sqlValue = ltrim($sqlValue, ", ");

        if (empty($sqlValue)) {
            return 0;
        }

        $sql = "INSERT INTO mail_content (`userId`, `policyId`, `createDate`, `updateDate`) VALUES " . $sqlValue;
        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeUpdate($sql);

        return $result;

    }

    public function insertMultiData($listData)
    {
        if (!$listData) {
            return false;
        }
        $em = $this->getEntityManager();
        $tableName = $em->getClassMetadata('Application\Entity\MailContent')->getTableName();
        $sqlValue = "";
        foreach ($listData as $value) {
            $sqlValue .= "(" . intval($value['userId']) . ", " . intval($value['policyId']) . ", " . $value['createDate'] . ", " . $value['updateDate'] . "), ";
        }
        $sqlValue = trim($sqlValue, ', ');
        $sql = "INSERT INTO " . $tableName . " (`userId`, `policyId`, `createDate`, `updateDate`) VALUES " . $sqlValue;

        return $em->getConnection()->executeQuery($sql);
    }

    public function deleteDataByArrayPolicy($policyIds)
    {
        if (!is_array($policyIds)) return false;
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->delete('\Application\Entity\MailContent', 'mailContent')
            ->where($qb->expr()->in('mailContent.policyId', ':policyId'))->setParameter(':policyId', $policyIds);
        return $qb->getQuery()->execute();
    }
}
