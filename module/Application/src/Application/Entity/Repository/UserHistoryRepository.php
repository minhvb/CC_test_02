<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Entity\Repository;


use Doctrine\ORM\EntityRepository;

class UserHistoryRepository extends EntityRepository
{
    public function getUserHistories($userId) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.id, u.userId, u.roleId, u.loginDate')
            ->from('\Application\Entity\UserHistory', 'u')
            ->where('u.userId = :userId')
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->getArrayResult();
    }
    
    public function getLoginData($startMonth, $endMonth){
        $condition = "";
        
        if(!empty($startMonth)){
            $startTime = $startMonth . "-01 00:00:00";
            $condition .= " AND uh.loginDate>='" . strtotime($startTime) . "'";
        }
        
        if(!empty($endMonth)){
            $endTime = strtotime('+1 month', strtotime($endMonth . "-01"));            
            $condition .= " AND uh.loginDate<'" . $endTime . "'";
        }

        $sql = "SELECT uh.username, rl.title as roleTitle, uh.loginDate FROM user_history uh LEFT JOIN role rl ON uh.roleId=rl.id WHERE 1 " . $condition . " ORDER BY uh.loginDate DESC";
        $connection = $this->getEntityManager()->getConnection();
        $rows = $connection->fetchAll($sql);
        
        return $rows;    
    }
    
    public function getTotalLoginByRole($startMonth, $endMonth){
        $condition = "";
        
        if(!empty($startMonth)){
            $startTime = $startMonth . "-01 00:00:00";
            $condition .= " AND uh.loginDate>='" . strtotime($startTime) . "'";
        }
        
        if(!empty($endMonth)){
            $endTime = strtotime('+1 month', strtotime($endMonth . "-01"));          
            $condition .= " AND uh.loginDate<'" . $endTime . "'";
        }  
        $sql = "SELECT count(uh.id) as totalLogin, rl.title as roleTitle, FROM_UNIXTIME(uh.loginDate, '%Y-%m-%d') as loginDay FROM user_history as uh LEFT JOIN role rl ON uh.roleId=rl.id WHERE 1 " . $condition . "  GROUP BY uh.roleId, FROM_UNIXTIME(uh.loginDate, '%Y-%m-%d') ORDER BY FROM_UNIXTIME(uh.loginDate, '%Y-%m-%d') DESC";
        $connection = $this->getEntityManager()->getConnection();
        $rows = $connection->fetchAll($sql);
        
        return $rows;
    }
}
