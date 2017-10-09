<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Factory;

use Application\Controller\MyPageController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class MyPageControllerFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $myPageService = $realServiceLocator->get('Application\Service\MyPageServiceInterface');
        $entityManager = $realServiceLocator->get('Doctrine\ORM\EntityManager');
        
        $loginService = $realServiceLocator->get('Application\Service\LoginServiceInterface');
        

        return new MyPageController($myPageService, $loginService, $entityManager);
    }

}
