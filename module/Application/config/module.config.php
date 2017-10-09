<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

return array(
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity',  // Define path of entities
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'  // Define namespace of entities
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'role' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:role]',
                            'constraints' => array(
                                'role' => 'administrator|input',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller',
                            ),
                        ),
                    ),
                ),
            ),
            'access-denied' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/access-denied',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'accessDenied',
                    ),
                ),
            ),
            'login-fail' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/login-fail',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'loginFail',
                    ),
                ),
            ),
            'logout' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'logout',
                    ),
                ),
            ),
            'change-password' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/change-password',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'changePassword',
                    ),
                ),
            ),
            'register' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/register',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'register',
                    ),
                ),
            ),
            'register-success' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/register-success',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'registerSuccess',
                    ),
                ),
            ),
            'verify-email' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/verify-email',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'verifyEmail',
                    ),
                ),
            ),
            'active-email' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/active-email',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'activeEmail'
                    ),
                ),
            ),
            'change-email-success' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/change-email-success',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'changeEmailSuccess'
                    ),
                ),
            ),
            'security-question' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/security-question',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'securityQuestion',
                    ),
                ),
            ),
            'forgot-password' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/forgot-password',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Login',
                        'action' => 'forgotPassword',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'forgotPassword',
                                '__NAMESPACE__' => 'Application\Controller',
                            ),
                        ),
                    ),
                ),
            ),
            'home' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Home',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => 'home/[:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller',
                            ),
                        ),
                    ),
                ),
            ),
            'guide' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/guide',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Guide',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller',
                            ),
                        ),
                    ),
                ),
            ),
            'notice' => array(
                'type' => 'Literal',
                'options' => array(
                    'route'    => '/notification',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Notice',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action[/:id]]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller',
                            ),
                        ),
                    ),
                ),
            ),
            'my-page' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/my-page',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'MyPage',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller',
                            ),
                        ),
                    ),
                ),
            ),
            'download' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/download[/:policyId]/:year/:month/:day/:fileName',
                    'constraints' => array(
                        'year' => '[0-9]*',
                        'month' => '[0-9]*',
                        'day' => '[0-9]*',
                    ),
                    'defaults' => array(
                        'action' => 'download',
                        'controller' => 'Home',
                        '__NAMESPACE__' => 'Application\Controller',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/app',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Application\Controller'
                            )
                        ),
                    ),
                ),
            ),
            'policy' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/policy/[:action[/:id]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'policy',
                        'action' => 'detail',
                    ),
                ),
            ),
            'survey' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/survey/[:action[/:id]]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'survey',
                        'action' => 'vote',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'Application\Service\ApplicationServiceInterface' => 'Application\Service\ApplicationService',
            'Application\Service\NoticeServiceInterface' => 'Application\Service\NoticeService',
            'Application\Service\HomeServiceInterface' => 'Application\Service\HomeService',
            'Application\Service\LoginServiceInterface' => 'Application\Service\LoginService',
            'Application\Service\MyPageServiceInterface' => 'Application\Service\MyPageService',
        	'Application\Service\PolicyServiceInterface' => 'Application\Service\PolicyService',
        	'Application\Service\SurveyServiceInterface' => 'Application\Service\SurveyService',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            //'Application\Controller\Home' => 'Application\Controller\HomeController'
        ),
        'factories' => array(
            'Application\Controller\Home' => 'Application\Factory\HomeControllerFactory',
            'Application\Controller\Login' => 'Application\Factory\LoginControllerFactory',
            'Application\Controller\MyPage' => 'Application\Factory\MyPageControllerFactory',
        	'Application\Controller\Policy' => 'Application\Factory\PolicyControllerFactory',
        	'Application\Controller\Survey' => 'Application\Factory\SurveyControllerFactory',
            'Application\Controller\Notice' => 'Application\Factory\NoticeControllerFactory',
            'Application\Controller\Guide' => 'Application\Factory\GuideControllerFactory',
        ),
    ),

    'translator' => array(
        'locale' => 'ja_JP',
        'translation_file_patterns' => array(
            array(
                'base_dir' => __DIR__ . '/../language/phpArray',
                'type' => 'phpArray',
                'pattern' => '%s.php',
            ),
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index-debug',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/layout-login' => __DIR__ . '/../view/layout/layout-login.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
            'error/index-debug' => __DIR__ . '/../view/error/index-debug.phtml',
            'error/permission' => __DIR__ . '/../view/error/permission.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
    'module_layouts' => array(
        'Login' => array(
            'default' => 'layout/album',
            'edit' => 'layout/albumEdit',
        ),
    ),
);
