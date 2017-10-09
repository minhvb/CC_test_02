<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Administrator\Controller;

use Application\Controller\BaseController;
use Application\Service\ApplicationService;
use Application\Service\ServiceInterface\ApplicationServiceInterface;
use Administrator\Service\ServiceInterface\MenuServiceInterface;
use Application\Utils\ApplicationConst;
use Doctrine\ORM\EntityManager;
use Zend\View\Model\ViewModel;


class MenuController extends BaseController {

    /**
     * @var \Administrator\Service\MenuService
     */
    protected $menuService;


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;


    public function __construct(MenuServiceInterface $menuService, EntityManager $em) {
        $this->menuService = $menuService;
        $this->em = $em;
    }

    public function indexAction() { 
        return new ViewModel(array(
            'roleId' => $this->userInfo['roleId']
        ));
    }
    public function mainManagementAction(){
        return new ViewModel();
    }

}
