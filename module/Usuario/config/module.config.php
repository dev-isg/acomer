<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Usuario\Controller\Index' => 'Usuario\Controller\IndexController',
            'Usuario\Controller\Comentarios' => 'Usuario\Controller\ComentariosController',
            'Usuario\Controller\Cliente' => 'Usuario\Controller\ClienteController',
            'Usuario\Controller\Clientes' => 'Usuario\Controller\ClientesController',

            
        ),
    ),
    'router' => array(
        'routes' => array(
            'usuario' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/usuario',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Usuario\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array( //'default' => array( 
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    =>'/[:controller[/:action[/:va_email/:va_nombre_cliente]]]',
                           /* 'route'    =>'/[:controller[/:action[/:va_email]]]',
                            'route'    =>'/[:controller[/:action[/:va_nombre_cliente]]]',*/
                            ////'/usuario[/][:action]', //'/[:controller[/:action[/:texto]]]',
             //    'route'    =>'/[:controller[/:action[/:va_email/:va_nombre]]]',//'/usuario[/][:action]', //'/[:controller[/:action[/:texto]]]',                 
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',                          
                                'in_id'=>'[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Usuario\Controllers\Index',
                                'action'     => 'index',

                            ),
                        ),
                    ),
                ),
            ),
            
            'registrarse' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/registrarse',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Usuario\Controller',
                        'controller' => 'Clientes',
                        'action' => 'agregarcliente'
                    )
                )
            ), 
             'pruebadefacebook' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/pruebadefacebook',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Usuario\Controller',
                        'controller' => 'Clientes',
                        'action' => 'pruebadefacebook'
                    )
                )
            ), 
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
//            'layout/layout-administrador'           => __DIR__ . '/../view/layout/layout-administrador.phtml',
//           'layout/layout-error'           => __DIR__ . '/../view/layout/layout-error.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'Usuario' => __DIR__ . '/../view',
        ),
    ),
        'module_layouts' => array(
        'Usuario' => array(
            'default' => 'layout/layout-administrador',
            'index' => 'layout/layout-administrador',
            'comentarios' => 'layout/layout-administrador',
            'login' => 'layout/layout-administrador',
            'clientes' => 'layout/layout-portada2',
            
        ) 
    )
);
