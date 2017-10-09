<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Service;

use Application\Service\ServiceInterface\MyPageServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Application\Utils\ApplicationConst;

class MyPageService implements MyPageServiceInterface, ServiceLocatorAwareInterface {
    use ServiceLocatorAwareTrait;
    
    public function updateUserInfo($data){
        return $this->getUserRepository()->updateUserInfo($data);
    }
    
    public function getUserInfo($username) {
        return $this->getUserRepository()->getUserInfo($username);
    }
    
    public function getUserInfoById($userId) {
        return $this->getUserRepository()->getUserInfoById($userId);
    }
    
    public function changePasswordByUserId($userId, $password, $passwordHistory) {
        return $this->getUserRepository()->changePasswordByUserId($userId, $password, $passwordHistory);
    }
    
    public function checkEmailExist($userId, $email){
        return $this->getUserRepository()->checkEmailExist($userId, $email);    
    }
    
    public function updateIsSettingMail($userId){
        return $this->getUserRepository()->updateIsSettingMail($userId);    
    } 
    
    public function createMailContent($listPolicy, $userId){
        return $this->getMailContentRepository()->createMailContent($listPolicy, $userId);    
    }
    
    public function getBusinessAttributes() {
        return $this->getPolicyAttributesRepository()->getBusinessAttributes();
    }
    
    public function getAllAttributes() {
        return $this->getPolicyAttributesRepository()->getAllAttributes($order = true);
    }
    
    public function getSettingMail($userId) {
        return $this->getSettingMailRepository()->getSettingMail($userId);
    }
    
    public function saveSettingMail($data) {
        return $this->getSettingMailRepository()->saveSettingMail($data);
    }
    
    public function getPublishPoliciesBySearch($search, $filter, $userId){
        return $this->getPolicyRepository()->getPublishPoliciesBySearch($search, $filter, $userId);   
    }
    
    public function getListFavouritePolicies($userId){
        return $this->getPolicyFavouriteRepository()->getListFavouritePolicies($userId);   
    }
    
    public function getListFavouritePoliciesHaveSendMail($userId){
        return $this->getPolicyFavouriteRepository()->getListFavouritePoliciesHaveSendMail($userId);   
    }
    
    public function getPolicyHaveNotificationFlag($listPolicy){
        return $this->getPolicyRepository()->getPolicyHaveNotificationFlag($listPolicy);   
    }
    
    public function getSettingMailRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\SettingMail');
    }
    
    public function getMailContentRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\MailContent');
    }
 
    public function getUserRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\User');
    }
    
    public function getPolicyAttributesRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\PolicyAttributes');
    }
    
    public function getPolicyRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\Policy');
    }
    
    public function getPolicyFavouriteRepository() {
        return $this->getEntityManager()->getRepository('Application\Entity\PolicyFavourite');
    }

    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }
}
