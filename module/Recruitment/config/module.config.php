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
            'Recruitment\Controller\Recruitment' => 'Recruitment\Controller\RecruitmentController',
        ],
    ],

    'router' => [
        'routes' => [
            'recruitment' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/recruitment[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Recruitment\Controller\Recruitment',
                        'action' => 'apply',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'recruitment' => __DIR__ . '/../view',
        ],
    ],
];