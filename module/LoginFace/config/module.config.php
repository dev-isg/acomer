<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'LoginFace\Controller\Facebook' => 'LoginFace\Controller\FacebookController',
            'LoginFace\Controller\Success' => 'LoginFace\Controller\SuccessController',
            'LoginFace\Controller\Prueba' => 'LoginFace\Controller\PruebaController'
        ),
    ),
    'router' => array(
        'routes' => array(
            
            'login' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/Facebook',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller'    => 'Facebook',
                        'action'        => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[/:action[/:in_id_face]]',//[:controller
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'cambio' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/cambio',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Facebook',
                        'action' => 'changeemail'
                    )
                )
            ),  
            'prueba' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/prueba',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Prueba',
                        'action' => 'index'
                    )
                )
            ), 
            'prueba-login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/prueba-login',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Prueba',
                        'action' => 'login'
                    )
                )
            ), 
            'validar-correo' => array(   
                'type' => 'Literal',
                'options' => array(
                    'route' => '/validar-correo',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Facebook',
                        'action' => 'validarcorreo'
                    )
                )
            ),
             'validar-contrasena' => array(   
                'type' => 'Literal',
                'options' => array(
                    'route' => '/validar-contrasena',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Facebook',
                        'action' => 'validarcontrasena'
                    )
                )
            ),
               'validar' => array(   
                'type' => 'Literal',
                'options' => array(
                    'route' => '/validar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Facebook',
                        'action' => 'validar'
                    )
                )
            ),
            'cambio-contrasena' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/cambio-contrasena',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Facebook',
                        'action' => 'recuperar'
                    )
                )
            ),
            'comprovarvalue' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/comprovar-value',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller' => 'Facebook',
                        'action' => 'comprovarvalue'
                    )
                )
            ),

            'success' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/success',
                    'defaults' => array(
                        '__NAMESPACE__' => 'LoginFace\Controller',
                        'controller'    => 'Success',
                        'action'        => 'index',
                    ),
                ),

                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:action[/:in_id_face]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'LoginFace' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
