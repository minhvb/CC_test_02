<?php

namespace Application\Service;

use Application\Service\ServiceInterface\ApplicationServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Application\Utils\ApplicationConst;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Application\Utils\UserHelper;
use Zend\View\Model\ViewModel;

class ApplicationService implements ApplicationServiceInterface, ServiceLocatorAwareInterface
{

    use ServiceLocatorAwareTrait;

    public function getTranslator()
    {
        return $this->getServiceLocator()->get('MVCTranslator');
    }


    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /*
     * get user information of logged user
     * @param mixed
     * @return array
     */

    public function getUserInformation()
    {
        return PrivateSession::getData(ApplicationConst::USER_INFO);
    }

    /*
     * get current router 
     * @param \Zend\Mvc\MvcEvent $e
     * @return array = [
     *      moduleName => '',
     *      controllerName => '',
     *      actionName => '',
     * ]
     */
    public function getCurrentRoute(\Zend\Mvc\MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $controller = $routeMatch->getParam('controller');

        $arrayController = explode('\\', $controller);
        $actionName = str_replace('-', '', $routeMatch->getParam('action'));
        $moduleRoute = str_replace('-', '', strtolower(reset($arrayController)));
        $controllerRoute = str_replace('-', '', strtolower(end($arrayController)));
        $actionRoute = strtolower($actionName);
        $route = $moduleRoute . '/' . $controllerRoute . '/' . $actionRoute;
        return array(
            'moduleName' => reset($arrayController),
            'controllerName' => end($arrayController),
            'actionName' => $actionName,
            'route' => $route
        );
    }

    public function isAccessPage($currentRoutes, $userInfo)
    {
        return true;
        if (!isset($userInfo['roleId'])) {
            return false;
        }
        $em = $this->getEntityManager();

        $authenticate = $em->getRepository('Application\Entity\Authenticate')->getListActionByRoleId($userInfo['roleId']);

        foreach ($authenticate as $value) {
            $listRoutes[] = $value['link'];
        }

        if (!in_array($currentRoutes['route'], $listRoutes)) {
            return false;
        }
        return true;
    }

    public function getAllSecurityQuestion()
    {
        return $this->getEntityManager()->getRepository('Application\Entity\SecurityQuestion')->getAllQuestion();
    }

    public function getAllSecurityQuestionWithEmpty()
    {
        $questions = $this->getAllSecurityQuestion();
        $emptyOption = array(
            'id' => "",
            'content' => ApplicationConst::EMPTY_QUESTION_OPTION
        );
        array_unshift($questions, $emptyOption);
        return $questions;
    }

    public function isUserAdmin($roleId = null)
    {
        $roleId = $roleId === null ? $this->getUserInformation()['roleId'] : $roleId;
        return UserHelper::isAdministrator($roleId);
    }

    public function isUserInput($roleId = null)
    {
        $roleId = $roleId === null ? $this->getUserInformation()['roleId'] : $roleId;
        return UserHelper::isInputRole($roleId);
    }

    public function isUserView($roleId = null)
    {
        $roleId = $roleId === null ? $this->getUserInformation()['roleId'] : $roleId;
        return UserHelper::isViewer($roleId);
    }

    public function convertObjectEntityToArray(EntityManager $em, $entityName, $data)
    {
        $hydrator = new DoctrineObject($em, $entityName);
        return $hydrator->extract($data);
    }

    public function getHtmlOutPutOfTemplate($template, $params) {
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true)
            ->setTemplate($template)
            ->setVariables($params);
        $htmlOutput = $this->getServiceLocator()->get('viewrenderer')->render($viewModel);
        return $htmlOutput;
    }
}
