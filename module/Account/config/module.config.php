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
            'Account\Controller\Account' => 'Account\Controller\AccountController',
            'Account\Controller\Data' => 'Account\Controller\DataController',

        ],
    ],

    'router' => [
        'routes' => [
            'account' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/account[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Account\Controller\Account',
                        'action' => 'index',
                    ],
                ],
            ],

            'data' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/data[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Account\Controller\Data',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'account' => __DIR__ . '/../view',
        ],
    ],
];