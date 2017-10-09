<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administrator\Service;

use Administrator\Service\ServiceInterface\ReportServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ReportService implements ReportServiceInterface, ServiceLocatorAwareInterface {
    
    use ServiceLocatorAwareTrait;
    
    public function getLoginData($startMonth="", $endMonth=""){
        return $this->getUserHistoryRepository()->getLoginData($startMonth, $endMonth);    
    }
    
    public function getTotalLoginByRole($startMonth="", $endMonth=""){
        return $this->getUserHistoryRepository()->getTotalLoginByRole($startMonth, $endMonth);    
    }
    
    public function getPolicyData($startMonth="", $endMonth=""){
        return $this->getPolicyViewStatisticRepository()->getPolicyData($startMonth, $endMonth);    
    }
    
    public function getUserHistoryRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\UserHistory');
    }
    
    public function getPolicyViewStatisticRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\PolicyViewStatistic');
    }
    
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
}