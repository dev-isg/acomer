<?php

namespace SanAuth2\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use SanAuth2\Form\UserForm;
use SanAuth2\Form\PasswordForm;
use SanAuth2\Form\UpdatepassForm;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Mail\Message;
use Usuario\Model\Usuario;
use Zend\View\Model\JsonModel;
//use Grupo\Controller\IndexController;

// SanAuth\Controller\UpdatepassForm;
// use SanAuth\Model\User;
class AuthController extends AbstractActionController {

    protected $form;
    protected $storage;
    protected $authservice;
    protected $clientesTable;

    
    public function __construct() {
        $this->_options = new \Zend\Config\Config(include APPLICATION_PATH . '/config/autoload/global.php');     
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('Auth2Service');
        }

        return $this->authservice;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()->get('SanAuth2\Model\MyAuthStorage');
        }

        return $this->storage;
    }

    public function getForm() {
        if (!$this->form) {
            // $user = new User();
            // $builder = new AnnotationBuilder();

            $this->form = new \SanAuth\Form\UserForm(); // $builder->createForm($user);
        }

        return $this->form;
    }


   
    
    public function sessionfacebook($email,$pass)
    {  
       
                $correo = $email;
                $contrasena = $pass;
                $this->getAuthService()
                        ->getAdapter()
                        ->setIdentity($correo)
                       ->setCredential($contrasena);
                    $result = $this->getAuthService()->authenticate();
                    foreach ($result->getMessages() as $message) {
                        $this->flashmessenger()->addMessage($message);
                    }
                    if ($result->isValid()) {                 
                        $storage = $this->getAuthService()->getStorage();
                        $storage->write($this->getServiceLocator()
                                        ->get('TableAuth2Service')
                                        ->getResultRowObject(array(
                                            'in_id',
                                            'va_nombre_cliente',
                                            'va_contrasena',
                                            'va_logout',
                                            'id_facebook'
                                        )));
                      
                    }
                    
     // return $this->redirect()->toUrl('/');
    }
    
 
    public function logoutAction() {
        session_destroy();
     //   $finsesion=  $this->params()->fromRoute('in_id_face');

        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
//            $this->flashmessenger()->addMessage("You've been logged out");
//        if($finsesion){
//            return $this->redirect()->toUrl($finsesion);
//         }
        }
        return $this->redirect()->toRoute('home');
        // return $this->redirect()->toRoute('login');
    }

   
   
   

    public function getClientesTable() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\ClientesTable');
        }
        return $this->clientesTable;
    }
 

}