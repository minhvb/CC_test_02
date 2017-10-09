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

class SettingMailRepository extends EntityRepository
{

    public function getSettingMail($userId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s.userId, s.attributesPolicyId')
            ->from('\Application\Entity\SettingMail', 's')
            ->where('s.userId = :userId')
            ->andWhere('s.isDisplayed = :isDisplayed')
            ->setParameter(':isDisplayed', 1)
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->getArrayResult();
    }

    public function saveSettingMail($data)
    {
        //delete all setting mail
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete('\Application\Entity\SettingMail', 'u')
            ->where($qb->expr()->eq('u.userId', ":userId"))
            ->setParameter(":userId", $data["userId"]);

        $result = $qb->getQuery()->getResult();

        $sqlValue = "";
        foreach ($data["attributes"] as $attributeType => $rows) {
            foreach ($rows as $value) {
                if(in_array($value, $data["attributesDisplayNone"])){
                    $sqlValue .= ", (" . ($data["userId"]) . ", " . $value . ", " . $attributeType . ", 0, " . time() . ", " . time() . ")";
                } else {
                    $sqlValue .= ", (" . ($data["userId"]) . ", " . $value . ", " . $attributeType . ", 1, " . time() . ", " . time() . ")";
                }
                    
            }
        }
        $sqlValue = ltrim($sqlValue, ", ");

        if (empty($sqlValue)) {
            return 0;
        }

        $sql = "INSERT INTO setting_mail (`userId`, `attributesPolicyId`, `attributeType`, `isDisplayed`, `createDate`, `updateDate`) VALUES " . $sqlValue;
        $connection = $this->getEntityManager()->getConnection();
        $result = $connection->executeUpdate($sql);
        $sessionId = $connection->lastInsertId();

        return $result;
    }

    public function getDataByListAttributes($attributes)
    {
        if (!$attributes || !is_array($attributes)) {
            return false;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('setting')->from('\Application\Entity\SettingMail', 'setting');

        $qbAttributes = $em->createQueryBuilder();
        $orXAttributes = $qbAttributes->expr()->orX();
        foreach ($attributes as $key => $listValues) {
            $orXAttributes->add($qb->expr()->andX(
                $qb->expr()->eq('setting.attributeType', intval($key)),
                $qb->expr()->in('setting.attributesPolicyId', $listValues)
            ));
        }
        $qb->andWhere($orXAttributes);
        return $qb->getQuery()->getArrayResult();
    }

    public function getDataByAttributeFavourite()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('setting')->from('\Application\Entity\SettingMail', 'setting')
            ->where('setting.attributeType = :attributeType')
            ->andWhere('setting.attributesPolicyId = :attributesPolicyId')
            ->setParameter(':attributeType', ApplicationConst::POLICY_ATTRIBUTE_FAVOURITE_TYPE)
            ->setParameter(':attributesPolicyId', ApplicationConst::POLICY_ATTRIBUTE_FAVOURITE_VALUE);
        return $qb->getQuery()->getArrayResult();
    }
}
