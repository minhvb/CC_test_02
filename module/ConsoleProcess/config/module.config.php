<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ConsoleProcess\Controller\Statistic' => 'ConsoleProcess\Controller\StatisticController',
        )
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'statistic' => array(
                    'options' => array(
                        'route' => 'statistic [<action>]',
                        'defaults' => array(
                            'controller' => 'ConsoleProcess\Controller\Statistic',
                            'action' => 'index'
                        )
                    )
                ),
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'ConsoleProcess' => __DIR__ . '/../view'
        )
    ),
    'service_manager' => array(
    ),
    'ConsoleProcess' => array(
        'moduleDir' => realpath(__DIR__ . '/../'),
        'moduleViewPath' => realpath(__DIR__ . '/../view/console-process'),
        'videoPath' => realpath(getcwd() . '/data'),
    )
);
