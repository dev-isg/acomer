<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Platos\Controller\Index' => 'Platos\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'platos' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/platos',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Platos\Controller',
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
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id_pa/:in_id]]]',// /:va_nombre
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'in_id'=>'[0-9]+',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        'ver' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/plato',
                    'defaults' => array(
                        'controller' => 'Platos\Controller\Index',
                        'action' => 'verplatos'
                    )
                ),
                            'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:in_id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'in_id'         => '[0-9]*',
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
            'Platos' => __DIR__ . '/../view',
        ),
    ),
//     'module_layouts' => array(
//      'platos' => array(
//          'index' => 'layout/layout-portada2',
//          //'edit'    => 'layout/albumEdit',
//        )
//     ),
);
