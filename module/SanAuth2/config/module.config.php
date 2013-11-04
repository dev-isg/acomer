<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'SanAuth2\Controller\Auth' => 'SanAuth2\Controller\AuthController',
            'SanAuth2\Controller\Success' => 'SanAuth2\Controller\SuccessController',
            'SanAuth2\Controller\Prueba' => 'SanAuth2\Controller\PruebaController'
        ),
    ),
    'router' => array(
        'routes' => array(
            
            'sessionfacebook' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/auth2',
                    'defaults' => array(
                        '__NAMESPACE__' => 'SanAuth2\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'sessionfacebook',
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
//            'cambio' => array(
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/cambio',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Auth',
//                        'action' => 'changeemail'
//                    )
//                )
////            ),  
//            'prueba' => array(
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/prueba',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Prueba',
//                        'action' => 'index'
//                    )
//                )
//            ), 
//            'prueba-login' => array(
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/prueba-login',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Prueba',
//                        'action' => 'login'
//                    )
//                )
//            ), 
//            'validar-correo' => array(   
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/validar-correo',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Auth',
//                        'action' => 'validarcorreo'
//                    )
//                )
//            ),
//             'validar-contrasena' => array(   
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/validar-contrasena',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Auth',
//                        'action' => 'validarcontrasena'
//                    )
//                )
//            ),
//               'validar' => array(   
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/validar',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Auth',
//                        'action' => 'validar'
//                    )
//                )
//            ),
//            'cambio-contrasena' => array(
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/cambio-contrasena',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Auth',
//                        'action' => 'recuperar'
//                    )
//                )
//            ),
//            'comprovarvalue' => array(
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/comprovar-value',
//                    'defaults' => array(
//                        '__NAMESPACE__' => 'SanAuth\Controller',
//                        'controller' => 'Auth',
//                        'action' => 'comprovarvalue'
//                    )
//                )
//            ),

            'success' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/success',
                    'defaults' => array(
                        '__NAMESPACE__' => 'SanAuth\Controller',
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
            'SanAuth' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
