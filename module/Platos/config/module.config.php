<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Platos\Controller\Index' => 'Platos\Controller\IndexController',
                 'Application\Controller\Index' => 'Application\Controller\IndexController',
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
        'plato' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/plato[/:restaurante/:nombre]',
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
              'verplatos2' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/plato[/:nombre]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'verplatos2'
                    )
                )
            ), 
              'platoindex' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/plato/listar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'index'
                    )
                )
            ), 
              'agregarplatos' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/plato/agregar-plato',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'agregarplatos'
                    )
                )
            ),
              'eliminarsolar' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/plato/eliminar-solar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'eliminarsolar'
                    )
                )
            ), 
             'cronsolar' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/plato/cron-solar',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'cronsolar'
                    )
                )
            ), 
               'editarplatos' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/editar-plato[/:id_pa/:in_id]',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'editarplatos'
                    )
                )
            ),
            'cambiaestado' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/plato/destaque-plato',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'cambiaestado'
                    )
                )
            ),
            'eliminar' => array(
                'type' => 'Segment',
                'options' => array(                   
                   'route' => '/plato/eliminar-plato',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Platos\Controller',
                        'controller' => 'Index',
                        'action' => 'eliminar'
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
//            'layout/layout-portada'           => __DIR__ . '/../view/layout/layout-portada.phtml',
//            'layout/layout-error'           => __DIR__ . '/../view/layout/layout-error.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'Platos' => __DIR__ . '/../view',
        ),
    ),
    'module_layouts' => array(
        'Platos' => array(
            'default'=>'layout/layout-administrador',
            'index' => 'layout/layout-administrador',
            'verplatos' => 'layout/layout-portada2'
        ),
    )
);
