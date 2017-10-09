<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class AuthenticateRepository extends EntityRepository {

    const STATUS_ENABLE = 1;

    public function getListActionByRoleId($roleId) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('action.name, action.link, action.description')
                ->from('\Application\Entity\Action', 'action')
                ->innerJoin('\Application\Entity\Authenticate', 'authenticate', \Doctrine\ORM\Query\Expr\Join::WITH, 'authenticate.actionId = action.id')
                ->where('authenticate.status = :status')
                ->andWhere('action.status = :status')
                ->andWhere('authenticate.roleId = :roleId')
                ->setParameter(':status', self::STATUS_ENABLE)
                ->setParameter(':roleId', intval($roleId));
        return $qb->getQuery()->getArrayResult();
    }

}
