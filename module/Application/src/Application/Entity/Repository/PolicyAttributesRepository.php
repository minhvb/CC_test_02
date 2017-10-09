<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\ApplicationConst;
use Doctrine\ORM\EntityRepository;

class PolicyAttributesRepository extends EntityRepository
{

    const STATUS_ENABLE = 1;

    public function getAllAttributes($order = false)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pAttr')
            ->from('\Application\Entity\PolicyAttributes', 'pAttr');

        if($order){
            $qb->orderBy('pAttr.attributeType, pAttr.position');
        }
        $data = $qb->getQuery()->getArrayResult();
        
        return $data;
    }
    
    public function getAttributesByMultiIds($ids)
    {
        if (!$ids || !is_array($ids)) {
            return false;
        }
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pAttr')
            ->from('\Application\Entity\PolicyAttributes', 'pAttr')
            ->where($qb->expr()->in('pAttr.id', ':ids'))
            ->setParameter(':ids', $ids);
        return $qb->getQuery()->getArrayResult();
    }

    public function getBusinessAttributes()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pAttr')
            ->from('\Application\Entity\PolicyAttributes', 'pAttr')
            ->where('pAttr.attributeType = :attributeType')
            ->setParameter(':attributeType', 5);
        return $qb->getQuery()->getArrayResult();
    }
    
    public function getAttributesByType($type)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('pAttr')
            ->from('\Application\Entity\PolicyAttributes', 'pAttr')
            ->where('pAttr.attributeType = :attributeType')
            ->setParameter(':attributeType', $type);
        $rows = $qb->getQuery()->getArrayResult();
        $results = array();
        foreach($rows as $row){
            $results[$row["id"]] = $row["value"];    
        }
        
        return $results;
    }

}
