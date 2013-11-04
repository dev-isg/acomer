<?php

namespace LoginFace\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use LoginFace\Form\UserForm;
use LoginFace\Form\PasswordForm;
use LoginFace\Form\UpdatepassForm;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Mail\Message;
use Usuario\Model\Usuario;
use Zend\View\Model\JsonModel;
//use Grupo\Controller\IndexController;

// SanAuth\Controller\UpdatepassForm;
// use SanAuth\Model\User;
class FacebookController extends AbstractActionController {

    protected $form;
    protected $storage;
    protected $authservice;


    
    public function __construct() {
        $this->_options = new \Zend\Config\Config(include APPLICATION_PATH . '/config/autoload/global.php');     
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('FacebookService');
        }

        return $this->authservice;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()->get('LoginFace\Model\MyAuthStorage');
        }

        return $this->storage;
    }

    public function getForm() {
        if (!$this->form) {
            $this->form = new \LoginFace\Form\UserForm(); // $builder->createForm($user);
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
                                        ->get('TableFacebookService')
                                        ->getResultRowObject(array(
                                            'in_id',
                                            'va_nombre_cliente',
                                            'va_contrasena',
                                            'va_logout',
                                            'id_facebook'
                                        )));
                       
                    }
                  
           //   return $storage; 
         }
  


}