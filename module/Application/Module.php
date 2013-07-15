<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;


use Application\Model\Usuario;
use Application\Model\UsuarioTable;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
//        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
//        $controller = $e->getTarget();
//        $controller->layout('layout/layout-portada2');
//    });

//              $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function($e) {
//             $result = $e->getResult();
////             $result->setTerminal(TRUE);
////             $result->setLayout('layout/layout-portada.phtml');
//             $result->setTemplate('layout/layout-error.phtml');
//          
//            });
            
           $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller      = $e->getTarget();
//            var_dump($controller);Exit;
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config          = $e->getApplication()->getServiceManager()->get('config');

            $routeMatch = $e->getRouteMatch();
//            $controllerName = strtolower($routeMatch->getParam('controller', 'not-found'));
            $actionName = strtolower($routeMatch->getParam('action', 'not-found')); // get the action name
//            var_dump($config['module_layouts'][$moduleNamespace][$actionName]);exit;
            if (isset($config['module_layouts'][$moduleNamespace][$actionName])) {
                $controller->layout($config['module_layouts'][$moduleNamespace][$actionName]);
            }/*elseif(isset($config['module_layouts'][$moduleNamespace]['default'])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]['default']);
            }*/

        }, 100);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
     public function getServiceConfig()
    {
        return array(
            'factories' => array(
//                        'Usuario\Model\Cliente'=>function($sm){
//                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                    $table = new Cliente($dbAdapter);
//                    return $table;
//                    
//                 },
                         
                'Application\Model\UsuarioTable' =>  function($sm) {
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
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
                
            ),
        );
    }
}
