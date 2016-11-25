<?php
/**
 *
 * User: Lukasz Marszalek
 * Date: 2016-08-30
 * Time: 19:27
 */
return [
    'controllers' => [
        'invokables' => [
            'Admin\Controller\Admin' => 'Admin\Controller\AdminController',
        ],
    ],

    'router' => [
        'routes' => [
            'admin' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/admin[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Admin\Controller\Admin',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'admin' => __DIR__ . '/../view',
        ],
    ],
];