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
            'Advertisement\Controller\Advertisement' => 'Advertisement\Controller\AdvertisementController',
            'Advertisement\Controller\Skill' => 'Advertisement\Controller\SkillController',
        ],
    ],

    'router' => [
        'routes' => [
            'advertisement' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/advertisement[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Advertisement\Controller\Advertisement',
                        'action' => 'index',
                    ],
                ],
            ],

            'skill' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/skill[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Advertisement\Controller\Skill',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'advertisement' => __DIR__ . '/../view',
        ],
    ],
];