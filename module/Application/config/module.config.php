<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
//                    'solicita'=>array(
//                      '__NAMESPACE__' => 'Application\Controller',
//                        'controller'    => 'Index',
//                        'action'        => 'solicita',     
//                    ),
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
            'busqueda' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/buscar',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'ver'
                    )
                )
            ),

            'busqueda-distrito' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/buscar-por-distrito',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'detalleubicacion'
                    )
                )
            ),
            
            
            'jsonmapasa' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/jsonmapasa',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'jsonmapasa'
                    )
                )
            ),
            
            'jsonmovil' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/jsonmovil',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'uno'
                    )
                )
            ),
            
             'jsondesta' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/jsondesta',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'jsondesta'
                    )
                )
            ),
      
              'nosotros' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/nosotros',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'nosotros'
                    )
                )
            ),
                'terminos' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/terminos',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'terminos'
                    )
                )
            ),
                  'contactenos' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/contactenos',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'contactenos'
                    )
                )
            ),

//                    'solicita' => array(
//                'type' => 'Literal',
//                'options' => array(
//                    'route' => '/solicita',
//                    'defaults' => array(
//                        'controller' => 'Application\Controller\Index',
//                        'action' => 'solicita'
//                    )
//                )
//            ),
                'ingresardatos' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/solicita',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'ingresardatos'
                    )
                )
            ),
             'ingresarplatos' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/ingresarplatos',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'ingresarplatos'
                    )
                )
            ),


            'platos' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/plato/:nombre',
                    'defaults' => array(
                        'controller' => 'Platos\Controller\Index',
                        'action' => 'verplatos'
                    )
                    
                )
            ),
       

        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Platos\Controller\Index' => 'Platos\Controller\IndexController'
//            'Local\Controller\Index' => 'Local\Controller\IndexController',
//            'Application\Controller\Hola' => 'Application\Controller\HolaController',
//            'Application\Controller\Formulario' => 'Application\Controller\FormularioController'
            
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
//            'layout/layout-portada'           => __DIR__ . '/../view/layout/layout-portada2.phtml',
            'layout/layout-error'           => __DIR__ . '/../view/layout/layout-error.phtml',
//             'layout/layout-portada'  => __DIR__ . '/../view/error/404.phtml',
            // 'layout/layout-dos'  => __DIR__ . '/../view/layout/layout-dos.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            'application'=>__DIR__ . '/../view',
        ),
    ),
    'module_layouts' => array(
        'Application' => array(
            'index' => '/../view/layout/layout-portada2',
            'terminos' => 'layout/layout-portada2',
            'nosotros' => 'layout/layout-portada2',
            'solicita' => 'layout/layout-portada2',
            'contactenos' => 'layout/layout-portada2',
            'ver' => 'layout/layout-portada2',
            'detalleubicacion' => 'layout/layout-portada2'
        ),
        ),

    'view_helpers' => array(
        'invokables' => array(
            'host' => 'Application\View\Helper\Host',
            'canonicalUrl' => 'Application\View\Helper\CanonicalUrl',
             'canonical' => 'Application\View\Helper\Canonical',
            'canonical2' => 'Application\View\Helper\Canonical2',
        )
    )

);
