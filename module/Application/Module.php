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
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use Application\Model\Usuario;
use Application\Model\UsuarioTable;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\I18n\Translator\Translator;
use Zend\Validator\AbstractValidator;
class Module 
//implements
//    AutoloaderProviderInterface,
//    ConfigProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
//        $translator = $e->getApplication()->getServiceManager()->get('translator');
//         AbstractValidator::setDefaultTranslator($translator);
        $translator=$e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            './vendor/zendframework/zendframework/resources/languages/es/Zend_Validate.php'

        );
        AbstractValidator::setDefaultTranslator($translator);

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
//        $eventManager->attach('dispatch', array($this,'onDispatchError'), 100);  
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

//      $sm  = $e->getApplication()->getServiceManager();
//        $controller = $e->getRouteMatch()->getParam('controller');
//        if (0 !== strpos($controller, __NAMESPACE__, 0)) {
//            //if not this module
//            return;
//        }
//        //if this module 
//    $exceptionstrategy = $sm->get('ViewManager')->getExceptionStrategy();
//    $exceptionstrategy->setExceptionTemplate('layout/layout-error');
}

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
     public function getServiceConfig()
    {
        return array(
            'factories' => array(   
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
               'mail.transport' => function ($sm) {
                $config = $sm->get('config'); 
                $transport = new \Zend\Mail\Transport\Smtp();   
                $transport->setOptions(new \Zend\Mail\Transport\SmtpOptions($config['mail']['transport']['options']));

                return $transport;
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
