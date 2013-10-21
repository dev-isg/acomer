<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Restaurante\Controller\Index' => 'Restaurante\Controller\IndexController',
            'Restaurante\Controller\Local' => 'Restaurante\Controller\LocalController',
            
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
  
                    'default' => array( //'default' => array( 
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    =>'/[:controller[/:action[/:in_id]]]',
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
             'ubigeototaldistrito' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/ubigeototaldistrito',
                    'defaults' => array(
                        'controller' => 'Restaurante\Controller\Index',
                        'action' => 'ubigeototaldistrito'
                    )
                    
                )
            ),
            
            'editarmenu' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/editar-menu[/:id]',
                    'defaults' => array(
                        'controller' => 'Restaurante\Controller\Index',
                        'action' => 'editarmenu'
                    )
                    
                )
            ),
            'listadoregistroplatos' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/listar-registro-platos[/:id]',
                    'defaults' => array(
                        'controller' => 'Restaurante\Controller\Index',
                        'action' => 'listadoregistroplatos'
                    )
                    
                )
            ),
              'editarbanner' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/editar-banner[/:id]',
                    'defaults' => array(
                        'controller' => 'Restaurante\Controller\Index',
                        'action' => 'editarbanner'
                    )
                    
                )
            ),
             'editartag' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/editar-tag[/:id]',
                    'defaults' => array(
                        'controller' => 'Restaurante\Controller\Index',
                        'action' => 'editartag'
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
//            'layout/layout-administrador'           => __DIR__ . '/../view/layout/layout-administrador3.phtml',
//            'layout/layout-error'           => __DIR__ . '/../view/layout/layout-error.phtml',
             'restaurante/index/index' => __DIR__ . '/../view/restaurante/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'Restaurante' => __DIR__ . '/../view',
        ),
    ),
    
//        'module_layouts' => array(
//        'Restaurante' => array(
//            'default'=>'layout/layout-administrador',
//            'index' => 'layout/layout-administrador'
//                )     
//    )
);
