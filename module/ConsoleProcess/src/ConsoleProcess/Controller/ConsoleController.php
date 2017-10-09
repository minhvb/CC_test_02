<?php

namespace ConsoleProcess\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class ConsoleController extends AbstractActionController
{

    protected $userInfo;
    protected $glConfig;

    /**
     * @var \Application\Service\ApplicationService
     */
    protected $appService;

    public function onDispatch(MvcEvent $e)
    {
        if (!$this->appService) {
            $this->appService = $this->getServiceLocator()->get('Application\Service\ApplicationServiceInterface');
        }
        if (!$this->glConfig) {
            $this->glConfig = $this->getServiceLocator()->get('Config');
        }

        $this->userInfo = $this->appService->getUserInformation();
        return parent::onDispatch($e);
    }
}