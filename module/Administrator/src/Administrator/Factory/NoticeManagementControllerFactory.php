<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administrator\Factory;

use Administrator\Controller\NoticeManagementController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class NoticeManagementControllerFactory implements FactoryInterface {

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $noticeService = $realServiceLocator->get('Administrator\Service\NoticeManagementServiceInterface');
        $entityManager = $realServiceLocator->get('Doctrine\ORM\EntityManager');

        return new NoticeManagementController($noticeService, $entityManager);
    }

}
