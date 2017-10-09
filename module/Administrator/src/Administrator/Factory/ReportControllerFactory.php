<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administrator\Factory;

use Administrator\Controller\ReportController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ReportControllerFactory implements FactoryInterface
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
        $reportService = $realServiceLocator->get('Administrator\Service\ReportServiceInterface');
        $entityManager = $realServiceLocator->get('Doctrine\ORM\EntityManager');

        return new ReportController($reportService, $entityManager);
    }

}
