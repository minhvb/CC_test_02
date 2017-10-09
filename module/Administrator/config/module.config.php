<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Administrator;

return array(
    'router' => array(
        'routes' => array(
            'policy-management' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/administrator/policy-management',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Administrator\Controller',
                        'controller' => 'PolicyManagement',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action][/:policyId]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Administrator\Controller'
                            )
                        ),
                    ),
                ),
            ),
        	'survey-management' => array(
        		'type' => 'Segment',
        		'options' => array(
        			'route' => '/administrator/survey-management',
        			'defaults' => array(
        				'__NAMESPACE__' => 'Administrator\Controller',
        				'controller' => 'SurveyManagement',
        				'action' => 'index',
        			),
        		),
        		'may_terminate' => true,
        		'child_routes' => array(
        			'default' => array(
        				'type' => 'Segment',
        				'options' => array(
        					'route' => '/[:action][/:surveyId]',
        					'constraints' => array(
        						'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
        						'id' => '[0-9]+',
        					),
        					'defaults' => array(
        						'action' => 'index',
        						'__NAMESPACE__' => 'Administrator\Controller'
        					)
        				),
        			),
        		),
        	),
            'notice-management' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/administrator/notice-management',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Administrator\Controller',
                        'controller' => 'NoticeManagement',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action][/:id][/:type]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'type' => '[a-zA-Z]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'action' => 'index',
                                '__NAMESPACE__' => 'Administrator\Controller'
                            )
                        ),
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /administrator/:controller/:action
            'administrator' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/administrator',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Administrator\Controller',
                        'controller' => 'PolicyManagement',
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
                                '__NAMESPACE__' => 'Administrator\Controller'
                            )
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'Administrator\Service\PolicyManagementServiceInterface' => 'Administrator\Service\PolicyManagementService',
            'Administrator\Service\UserManagementServiceInterface' => 'Administrator\Service\UserManagementService',
            'Administrator\Service\NoticeManagementServiceInterface' => 'Administrator\Service\NoticeManagementService',
            'Administrator\Service\SurveyManagementServiceInterface' => 'Administrator\Service\SurveyManagementService',
            'Administrator\Service\MenuServiceInterface' => 'Administrator\Service\MenuService',
            'Administrator\Service\ReportServiceInterface' => 'Administrator\Service\ReportService',
        ),
    ),
    'controllers' => array(
        'factories' => array(
            'Administrator\Controller\PolicyManagement' => 'Administrator\Factory\PolicyManagementControllerFactory',
            'Administrator\Controller\UserManagement' => 'Administrator\Factory\UserManagementControllerFactory',
            'Administrator\Controller\NoticeManagement' => 'Administrator\Factory\NoticeManagementControllerFactory',
            'Administrator\Controller\SurveyManagement' => 'Administrator\Factory\SurveyManagementControllerFactory',
            'Administrator\Controller\Menu' => 'Administrator\Factory\MenuControllerFactory',
            'Administrator\Controller\Report' => 'Administrator\Factory\ReportControllerFactory',
        ),
    ),

    'translator' => array(
        'locale' => 'ja_JP',
        'translation_file_patterns' => array(
            array(
                'base_dir' => __DIR__ . '/../language/phpArray',
                'type' => 'phpArray',
                'pattern' => '%s.php'
            )
        )
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'Administrator' => __DIR__ . '/../view'
        )
    ),
);
