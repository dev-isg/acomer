<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Local\Controller\Index' => 'Local\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'local' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    =>'/local',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Local\Controller',
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
                            'route'    => '/[:controller[/:action[/:in_id]]]',
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
                'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
//            'layout/layout-administrador'           =>  __DIR__ .'/../view/layout/layout-administrador.phtml',
//            'layout/layout-error'           => __DIR__ . '/../view/layout/layout-error.phtml',
//            'layout/layout'           =>  __DIR__ .'/../view/layout/layout.phtml',
//            'local/index/index' => __DIR__ . '/../view/local/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),

//        
        'template_path_stack' => array(
            'Local' => __DIR__ . '/../view',
        ),
        
    ),
    
    'module_layouts' => array(
        'Local' => array(
            'default'=>'layout/layout-administrador',
            'index' => 'layout/layout-administrador'
        ),   
    )
);
