<?php
namespace SanAuth2;

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
                
                'SanAuth2\Model\MyAuthStorage' => function ($sm)
                {
                    return new \SanAuth2\Model\MyAuthStorage('zf_tutorial');
                },
                
                'Auth2Service' => function ($sm)
                {
                    $dbTableAuthAdapter = $sm->get('TableAuth2Service');
                    
                    $authService = new AuthenticationService();
                    $authService->setStorage(new \Zend\Authentication\Storage\Session('Auth2')); // $authService->setStorage($sm->get('SanAuth\Model\MyAuthStorage')); //
                    $authService->setAdapter($dbTableAuthAdapter);
                    return $authService;
                },
                'TableAuth2Service' => function ($sm)
                {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'ta_cliente', 'va_email', 'va_contrasena_facebook', 'SHA1(?)'); //
                    return $dbTableAuthAdapter;
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
            $authAdapter = $locator->get('Auth2Service');
            $controller = $e->getTarget();
            $routeMatch = $e->getRouteMatch();
            $actionName = $routeMatch->getParam('action', 'not-found');
//            $actionName ='auth2//authenticate';
//            $controller->layout()->form = new \SanAuth2\Form\UserForm();
//            $controller->layout()->formCambio = new \SanAuth2\Form\PasswordForm();
//            $controller->layout()->formActualizar = new \SanAuth2\Form\UpdatepassForm();
//            $controller->layout()->accion3 = '/cambio';
//          //  $controller->layout()->accion3 = '/cambio';
            $controller->layout()->accion = $actionName;
            
            if ($actionName == 'sessionfacebook') {
                if ($authAdapter->hasIdentity() === true) {
                    $storage = new \Zend\Authentication\Storage\Session('Auth2');
                    $session = $storage->read();
                    $controller->layout()->sessionface = $session;
                    return $controller->redirect()
                            ->toRoute('home');
                } else {
                    return;
                }
            } else {
                $storage = new \Zend\Authentication\Storage\Session('Auth2');
                $session = $storage->read();
                $controller->layout()->sessionface = $session;
                return;
            }
   //////////////////////////////////////////////////////////INICIO//////////////////////////////////////////////////////////////////
//            if ($authAdapter->hasIdentity() === true) {
////                echo 'aka entro session';exit;
//                $storage = new \Zend\Authentication\Storage\Session('Auth');
//                $session = $storage->read();
//                $controller->layout()->session = $session;
//
//                    if ($actionName == 'login') {
//                            return $controller->redirect()->toRoute('grupos');
//                    }else{
//                        return;
//                    }
//                
//            } else {
////                echo 'aka sin logear';exit;
////                 if ($actionName == 'agregargrupo') {
////                                 return $controller->redirect()
////                                 ->toRoute('login');
////                         }else{
////                             return;
////                         }
//          
//            }
 //////////////////////////////////////////////////////////////FINNNN///////////////////////////////////////////////////

        }, 100);
    }
//     if ($actionName == 'login') {
//         if ($authAdapter->hasIdentity() === true) {
//             return $controller->redirect()
//             ->toRoute('grupo');
//         } else {
//             return;
//         }
//     } else
//     if ($actionName == 'agregargrupo') {
//         if ($authAdapter->hasIdentity() === false) {
//             return $controller->redirect()
//             ->toRoute('login');
//         } else {}
//     }
//     }
//     public function GetData($e)
//     {
//     $columnsToReturn = array(
//     'in_id',
//     'va_nombre',
//     'va_contrasena'
//     );
//     $locator = $e->getApplication()->getServiceManager();
//     $authAdapter = $locator->get('TableAuthService');
//     $auth = $locator->get('AuthService');
//     $storage = $auth->getStorage();
//     $storage->write($return = $authAdapter->getResultRowObject($columnsToReturn));
//     var_dump($storage->read());
//     Exit();
//     }
    // public function bootstrapSession($e)
    // {
    // $session = $e->getApplication()
    // ->getServiceManager()
    // ->get('Zend\Session\SessionManager');
    // $session->start();
    //
    // $container = new Container('usuario');
    // // if (!isset($container->init)) {
    // //// $session->regenerateId(true);
    // // $container->init = 1;
    // // }
    // var_dump($container->init);exit;
    // }
    //
//     public function getDbDatos(MvcEvent $e){
//     $locator = $e->getApplication()->getServiceManager();
//     $authAdapter = $locator->get('AuthService');
//     $correo=$authAdapter->getIdentity();
    
//     $dbAdapter = $e->getApplication()->getServiceManager()->get('Zend\Db\Adapter\Adapter');
//     $results = $dbAdapter->query('SELECT * FROM ta_usuario WHERE va_nombre="'.$correo.'"')->execute();
//     var_dump($results->current());exit;
//     return $results->current();
//     }
}
