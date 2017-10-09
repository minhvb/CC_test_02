<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;

use Application\Utils\DateHelper;
use Doctrine\ORM\EntityRepository;

class SecurityQuestionRepository extends EntityRepository {

    public function getAllQuestion() {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('q.id, q.content')
                ->from('\Application\Entity\SecurityQuestion', 'q');
        $qb->addOrderBy('q.id', "ASC");
        return $qb->getQuery()->getArrayResult();
    }
}
