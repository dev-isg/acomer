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

class FacebookController extends AbstractActionController {

    protected $form;
    protected $storage;
    protected $Facebookservice;
    protected $clientesTable;

    
    public function __construct() {
        $this->_options = new \Zend\Config\Config(include APPLICATION_PATH . '/config/autoload/global.php');     
    }

    public function getFacebookService() {
        if (!$this->facebookservice) {
            $this->facebookservice = $this->getServiceLocator()->get('FacebookService');
        }

        return $this->facebookservice;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()->get('LoginFace\Model\MyFacebookStorage');
        }

        return $this->storage;
    }

    public function getForm() {
        if (!$this->form) {
            $this->form = new \LoginFace\Form\UserForm(); 
        }

        return $this->form;
    }


    public function sessionfacebook($email,$pass)
    {  
       
                $correo = $email;
                $contrasena = $pass;
                $this->getFacebookService()
                        ->getAdapter()
                        ->setIdentity($correo)
                       ->setCredential($contrasena);
                    $result = $this->getFacebookService()->authenticate();
                    foreach ($result->getMessages() as $message) {
                        $this->flashmessenger()->addMessage($message);
                    }
                    if ($result->isValid()) {                 
                        $storage = $this->getFacebokService()->getStorage();
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
      return $this->redirect()->toUrl('/');
    }
  
    public function getClientesTable() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\ClientesTable');
        }
        return $this->clientesTable;
    }
 

}