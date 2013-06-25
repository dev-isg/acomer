<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Usuario\Controller\Index' => 'Usuario\Controller\IndexController',
            'Usuario\Controller\Comentarios' => 'Usuario\Controller\ComentariosController',
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
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Usuario' => __DIR__ . '/../view',
        ),
    ),
);
