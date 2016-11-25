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
            'Test\Controller\Test' => 'Test\Controller\TestController',
        ],
    ],

    'router' => [
        'routes' => [
            'test' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/test[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Test\Controller\Test',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'test' => __DIR__ . '/../view',
        ],
    ],
];