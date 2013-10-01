<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Admin\Model\Banner;
use Admin\Model\BannerTable;

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
  public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Admin\Model\BannerTable' =>  function($sm) {
                    $tableGateway = $sm->get('BannerTableGateway');
                    $table = new RestauranteTable($tableGateway);
                    return $table;
                },
                'BannerTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Banner());
                    return new TableGateway('ta_banner', $dbAdapter, null, $resultSetPrototype);
                },
//                 'Local\Model\LocalTable'=>function($sm){
//                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
//                    $table = new LocalTable($dbAdapter);
//                    return $table;
//                    
//                 },
                
         
            ),
        );
    }
   public function onBootstrap(MvcEvent $e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
//                      $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function($e) {
//             $result = $e->getResult();
//             $result->setTerminal(TRUE);
//             $result->setTemplate('layout/layout-error.phtml');

//            });
                 $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this,'onDispatchError'), 100); 
        
                        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config          = $e->getApplication()->getServiceManager()->get('config');

            $routeMatch = $e->getRouteMatch();
            $actionName = strtolower($routeMatch->getParam('action', 'not-found')); // get the action name

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
