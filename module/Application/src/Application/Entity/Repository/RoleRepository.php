<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository {

    public function getAllRole() {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('r.id, r.title, r.description')
                ->from('\Application\Entity\Role', 'r');
        $qb->addOrderBy('r.position', "ASC");
        return $qb->getQuery()->getArrayResult();
    }
   
}
