<?php
namespace LoginFace;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Session\SessionManager;
use Zend\Session\Container;

class Module implements AutoloaderProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
                )
            )
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
                'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
                
                'LoginFace\Model\MyAuthStorage' => function ($sm)
                {
                    return new \LoginFace\Model\MyAuthStorage('zf_tutorial');
                },
                
                'FacebookService' => function ($sm)
                {
                    $dbTableAuthsAdapter = $sm->get('TableFacebookService');
                    
                    $authsService = new AuthenticationService();
                    $authsService->setStorage(new \Zend\Authentication\Storage\Session('Facebook')); // $authService->setStorage($sm->get('SanAuth\Model\MyAuthStorage')); //
                    $authsService->setAdapter($dbTableAuthsAdapter);
                    return $authsService;
                },
                'TableFacebookService' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthsAdapter = new DbTableAuthAdapter($dbAdapter, 'ta_cliente', 'va_email', 'va_contrasena_facebook', 'SHA1(?)'); //
                    return $dbTableAuthsAdapter;
                }
            )
        )
        ;
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        // $this->getDbDatos($e);
        
        $app = $e->getApplication();
        $app->getEventManager()
            ->getSharedManager()
            ->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function ($e)
        {
                $locator = $e->getApplication()
                ->getServiceManager();
            $authAdapter = $locator->get('FacebookService');
            $controller = $e->getTarget();
            $routeMatch = $e->getRouteMatch();
                 if ($authAdapter->hasIdentity() === true) {
                    $storage = new \Zend\Authentication\Storage\Session('Facebook');
                    $session = $storage->read();
                    $controller->layout()->sessionface = $session;
                    return $controller->redirect()
                            ->toRoute('/');
                } else {
                    return;
                }
//                $storage = new \Zend\Authentication\Storage\Session('Facebook');
//                $session = $storage->read();
//                $controller->layout()->sessionface = $session;

 //////////////////////////////////////////////////////////////FINNNN///////////////////////////////////////////////////

        }, 100);
    }

}
