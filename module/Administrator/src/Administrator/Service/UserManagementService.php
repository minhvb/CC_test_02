<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administrator\Service;

use Administrator\Service\ServiceInterface\UserManagementServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class UserManagementService implements UserManagementServiceInterface, ServiceLocatorAwareInterface {
    
    use ServiceLocatorAwareTrait;
    
    public function getAllUsers($search, $currentPage, $resultPerPage) {
        return $this->getUserRepository()->getAllUsers($search, $currentPage, $resultPerPage);
    }
    
    public function getTotalUsers($search) {
        return $this->getUserRepository()->getTotalUsers($search);
    }
    
    public function deleteByUserId($userId) {
        return $this->getUserRepository()->deleteByUserId($userId);
    }
    
    public function resetPasswordByUserId($userId, $password) {
        return $this->getUserRepository()->resetPasswordByUserId($userId, $password);
    }
    
    public function getUserInfoById($userId) {
        return $this->getUserRepository()->getUserInfoById($userId);
    }
    
    public function roles() {
        return $this->getRoleRepository()->getAllRole();
    }
    
    public function getAttributesByType($type) {
        return $this->getPolicyAttributesRepository()->getAttributesByType($type);
    }
    
    public function getUserInList($list_user_id) {
        return $this->getUserRepository()->getUserInList($list_user_id);
    }
    
    public function getEmailInList($list_email) {
        return $this->getUserRepository()->getEmailInList($list_email);
    }
    
    public function getUserInListByUserIdNRole($list_unique_user_delete) {
        return $this->getUserRepository()->getUserInListByUserIdNRole($list_unique_user_delete);
    }
    
    public function insertMultipleUser($dataInsert){
        return $this->getUserRepository()->insertMultipleUser($dataInsert);    
    }
    
    public function deleteMultipleUser($list_user_id){
        return $this->getUserRepository()->deleteMultipleUser($list_user_id);    
    }
    
    public function getPolicyAttributesRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\PolicyAttributes');
    }
    
    public function getUserRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\User');
    }
    
    public function getRoleRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Role');
    }
    
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
}