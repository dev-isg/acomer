<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Usuario;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Usuario\Model\Usuario;
use Usuario\Model\UsuarioTable;

use Usuario\Model\Cliente;

use Usuario\Model\Clientes;
use Usuario\Model\Comentarios;
use Usuario\Model\ComentariosTable;
use Usuario\Model\ClientesTable;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
		    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
//inicio
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Usuario\Model\UsuarioTable' =>  function($sm) {
                    $tableGateway = $sm->get('UsuarioTableGateway');
                    $table = new UsuarioTable($tableGateway);
                    return $table;
                },
                'UsuarioTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Usuario());
                    return new TableGateway('ta_usuario', $dbAdapter, null, $resultSetPrototype);
                },

                  'Usuario\Model\Cliente'=>function($sm){
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new Cliente($dbAdapter);
                    return $table;
                    
                 },
                'Usuario\Model\ComentariosTable' =>  function($sm) {
                    $tableGateway = $sm->get('ComentariosTableGateway');
                    $table = new Model\ComentariosTable($tableGateway);
                    return $table;
                },
                'ComentariosTableGateway' => function ($sm) {
                    $dbAdapte = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Comentarios());
                    return new TableGateway('ta_comentario', $dbAdapte, null, $resultSetPrototype);
                },
                 'Usuario\Model\ClientesTable' =>  function($sm) {
                    $tableGateway = $sm->get('ClientesTableGateway');
                    $table = new Model\ClientesTable($tableGateway);
                    return $table;
                },
                'ClientesTableGateway' => function ($sm) {
                    $dbAdapte = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Clientes());
                    return new TableGateway('ta_cliente', $dbAdapte, null, $resultSetPrototype);
                },

            ),
        );
    }

    //fin
    public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
//        
//                      $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function($e) {
//             $result = $e->getResult();
//             $result->setTerminal(TRUE);
//             
//
//            });
//          $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this,'onDispatchError'), 100); 
          
            $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config          = $e->getApplication()->getServiceManager()->get('config');

            $routeMatch = $e->getRouteMatch();
            $actionName = strtolower($routeMatch->getParam('action', 'not-found')); // get the action name
            
           
            $controller->layout()->formCliente = new \Usuario\Form\ClientesForm();
//            $face = new \Usuario\Controller\ClientesController();
//            $facebook = $face->facebook();
//            $controller->layout()->loginUrl = $facebook['loginUrl'];
//            $controller->layout()->user = $facebook['user']; 
            $controller->layout()->accion1 = 'registrarse';
            
            
            
            
            
            
            
            

            if (isset($config['module_layouts'][$moduleNamespace][$actionName])) {
                $controller->layout($config['module_layouts'][$moduleNamespace][$actionName]);
            }elseif(isset($config['module_layouts'][$moduleNamespace]['default'])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]['default']);
            }

        }, 100);
    }
    
            function onDispatchError(MvcEvent $e) {
            $vm = $e->getViewModel();
            $vm->setTemplate('layout/layout-error');
}
}
