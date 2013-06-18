<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Restaurante\Controller\Index' => 'Restaurante\Controller\IndexController',
            'Restaurante\Controller\Restaurant' => 'Usuarios\Controller\RestaurantController',
            
        ),
    ),
    'router' => array(
        'routes' => array(
            'restaurante' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/restaurante',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Restaurante\Controller',
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
                            'route'    =>'/[:controller[/:action[/:in_id]]]',//'/usuario[/][:action]', //'/[:controller[/:action[/:texto]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',                          
                                'in_id'=>'[0-9]+',
                            ),
                            'defaults' => array(

                                'controller' => 'Restaurante\Controllers\Index',
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
            'restaurante' => __DIR__ . '/../view',
        ),
    ),
);
