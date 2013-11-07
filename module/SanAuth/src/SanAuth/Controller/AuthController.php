<?php

namespace SanAuth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\View\Model\ViewModel;
use SanAuth\Form\UserForm;
use LoginFace\Controller\FacebookController;
use SanAuth\Form\PasswordForm;
use SanAuth\Form\UpdatepassForm;
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
     protected $storage2;
    protected $authservice;
    protected $clientesTable;

    
    public function __construct() {
        $this->_options = new \Zend\Config\Config(include APPLICATION_PATH . '/config/autoload/global.php');     
    }

    public function getAuthService($valor=null) {
        if (!$this->authservice) {
            if($valor!==null)
               { 
                    $this->authservice = $this->getServiceLocator()->get('AuthService2');
                    
                   }
               else
            { $this->authservice = $this->getServiceLocator()->get('AuthService');}
           
        }

        return $this->authservice;
    }
    

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()->get('SanAuth\Model\MyAuthStorage');
        }
        return $this->storage;
    }

    
   public function sessionfacebook($email,$pass,$url=null)
       {  

                $correo = $email;
                $contrasena = $pass;
                $this->getAuthService(1)
                        ->getAdapter()
                        ->setIdentity($correo)
                       ->setCredential($contrasena);
                    $result = $this->getAuthService(1)->authenticate();
                    foreach ($result->getMessages() as $message) {
                        $this->flashmessenger()->addMessage($message);
                    }
                    if ($result->isValid()) {                 
                        $storage = $this->getAuthService(1)->getStorage();
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
          if($url==null) 
              
          {return $this->redirect()->toUrl('/');}
          else{return  $this->redirect()->toUrl($url);} 
        
    }
    
    public function authenticateAction() {
        $form = $this->getForm();
        $redirect = 'login';
        $request = $this->getRequest();

        if ($request->isPost()) {

            $form->setData($request->getPost());
            if ($form->isValid()) {
                $correo = $request->getPost('va_email');
                $contrasena = $request->getPost('va_contrasena');
                $this->getAuthService()
                        ->getAdapter()
                        ->setIdentity($correo)
                        ->setCredential($contrasena);

              $usuario = $this->getClientesTable()->usuario1($correo);
                if ($usuario[0]['en_estado'] == 'activo') {
                    $result = $this->getAuthService()->authenticate();
                    foreach ($result->getMessages() as $message) {
                        if($message){
                           $this->flashmessenger()->addMessage('Usuario ó contraseña incorrecto');
//                            return new JsonModel(array(
//                            'menssage' =>$message,
//                            'success'=>false
//                            ));  
//                            exit;
                        }                       
                    }
                    if ($result->isValid()) { 
                        $urlorigen = $this->getRequest()->getHeader('Referer')->uri()->getPath();
                        $arrurl = explode('/', $urlorigen);
                        $id = end($arrurl);
                       
                        $storage = $this->getAuthService()->getStorage();
                        $storage->write($this->getServiceLocator()
                                        ->get('TableAuthService')
                                        ->getResultRowObject(array(
                                            'in_id',
                                            'va_nombre_cliente',
                                            'va_contrasena',
                                            'va_email',
                                            'va_logout',
                                            'id_facebook'
                                        )));
                         return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
                        if ($id) {
                            return $this->redirect()->toRoute($redirect, array('in_id' => $id));
                        } else {
                            return $this->redirect()->toRoute($redirect);
                        }
                    } else {
                           return new JsonModel(array(
                            'menssage' =>$message,
                            'success'=>false
                            ));  
                            exit;
                        
                        return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/auth');
                    }
                }
            } else {
//              foreach ($form->getInputFilter()->getInvalidInput() as $error) {
//                       foreach($error->getMessages() as $mensaje){
//                             $this->flashmessenger()->addMessage($mensaje);
//                       }
//              }
//              }
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/auth');
            }
        }
    }
    

    
    public function getForm() {
        if (!$this->form) {
            // $user = new User();
            // $builder = new AnnotationBuilder();

            $this->form = new \SanAuth\Form\UserForm(); // $builder->createForm($user);
        }

        return $this->form;
    }


    public function loginAction() {
          $view =new ViewModel();
        $this->layout('layout/layout-portada2');
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $renderer->inlineScript()
                ->prependFile($this->_options->host->base . '/js/main.js');
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session = $storage->read();
        if (!isset($session)) {
            $face = new \Usuario\Controller\ClientesController();
            $facebook = $face->facebook();
            $this->layout()->login = $facebook['loginUrl'];
            $this->layout()->user = $facebook['user'];
        }
        $token = $this->params()->fromQuery('token');
        if ($token) {
            $usuario = $this->getClientesTable()->clientes($token);
            if (count($usuario) > 0) {
                $this->getClientesTable()->cambiarestado($usuario[0]['in_id']);
                $mensaje = 'Bienvenido '.ucwords($usuario[0]['va_nombre_cliente']).'. Tu cuenta ya esta lista para usarse. ';
                      return new JsonModel(array(
                          'menssage' =>$mensaje,
                           'success'=>true
                            ));  
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
            } else {
           $mensaje = 'Esta cuenta ya ha sido activada. Inicie Sesión. '; 
                return new JsonModel(array(
                          'menssage' =>$mensaje,
                           'success'=>false
                            ));
                
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/');
            }
        }
        
       $form = $this->getForm();   
       $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $mensajes = $flashMessenger->getMessages();
        }


         $view->setVariables(array(
            'form' => $form,
            'mensaje' => $mensaje,
            'messages' => $mensajes//$this->flashmessenger()->getMessages()
        ));
        return $view;
    }



    

    public function validarAction(){
      $request = $this->getRequest();
        if ($request->isPost()) {
                $correo = $this->params()->fromPost('va_email');//$request->getPost('va_email');
                $contrasena =$this->params()->fromPost('va_contrasena');// $request->getPost('va_contrasena');
                $this->getAuthService()
                        ->getAdapter()
                        ->setIdentity($correo)
                        ->setCredential($contrasena);
           $result = $this->getAuthService()->authenticate();
           if($result->isValid()){
                        $urlorigen = $this->getRequest()->getHeader('Referer')->uri()->getPath();
                        $arrurl = explode('/', $urlorigen);
                        $id = end($arrurl);
                        $accion = $request->getPost('accion');
                        $origen = $request->getPost('origen', 'evento');
                        if ($accion == 'verplato') {
                            $redirect = 'plato/restaurante';
                        } elseif ($accion == 'detalleubicacion') {
                            $redirect = 'busqueda-distrito';
                        } elseif ($accion == 'index' && $origen == 'ingresarPrin') {
                            $redirect = 'home';
                        }

                        $storage = $this->getAuthService()->getStorage();
                        $storage->write($this->getServiceLocator()
                                        ->get('TableAuthService')
                                        ->getResultRowObject(array(
                                           'in_id',
                                            'va_nombre_cliente',
                                            'va_contrasena',
                                            'va_email',
                                            'va_logout',
                                            'id_facebook'
                                        )));
                        
                    if ($id) {
                      $success=true;
                     return new JsonModel(array('success'=>$success,'in_id'=>$id));
                   } else {
                    $success=true;
                     return new JsonModel(array('success'=>$success));
                   }
           }else{
               $mensajes ='Usuario ó contraseña incorrecto';
               $success=false;
               return new JsonModel(array( 'menssage' =>$mensajes,'success'=>$success));

           }
        }
                
    }

    public function validarcorreoAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
                $correo = $this->params()->fromPost('va_email');     
                $usuario = $this->getClientesTable()->usuario1($correo);
                if($usuario){
                     if ($usuario[0]['va_estado'] == 'activo') {
                            return new JsonModel(array(
                            'success'=>true
                            ));
                    }else{
                       $mensaje='El correo no se encuentra registrado';
                       $result = new JsonModel(array(
                               'menssage' =>$mensaje,
                               'success'=>false
                           ));
                        return $result;
                   }                   
                }else{
                    $mensaje='El correo no se encuentra registrado';
                      return new JsonModel(array(
                          'menssage' =>$mensaje,
                           'success'=>false
                            ));
                    
                }

        }
    
    }
    
     public function validarcontrasenaAction(){
        $request = $this->getRequest();
        if ($request->isPost()) {
                $correo = $this->params()->fromPost('va_email');
                $contrasena = sha1($this->params()->fromPost('va_contrasena'));  
                $usuario = $this->getUsuarioTable()->usuario1($correo);
                if ($usuario[0]['en_estado'] == 'activo') {
                    $password=$this->getUsuarioTable()->getUsuario($usuario[0]['in_id'])->va_contrasena;
                    if ($password) {
                        if($password===$contrasena){
                            return new JsonModel(array(
                            'success'=>true
                            ));
                        }else{
                           $mensaje='El correo no concide con la contrasena';
                           $result = new JsonModel(array(
                            'menssage' =>$mensaje,
                            'success'=>false
                            ));
                            return $result;
                        }
                    }else{
                            return new JsonModel(array(
                            'success'=>false
                            ));
                    }
                }else{
                    $mensaje='El correo no se encuentra registrado';
                    $result = new JsonModel(array(
                            'menssage' =>$mensaje,
                            'success'=>false
                        ));
                     return $result;
                }
        }
    
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

    public function changeemailAction() {
        $view =new ViewModel();
        $this->layout('layout/layout-portada2');
        $request = $this->getRequest();
        $form = new PasswordForm();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $mail = $this->params()->fromPost('va_email');
                try {
                    $results = $this->getClientesTable()->generarPassword($mail);
                    $usuario = $this->getClientesTable()->getUsuarioxEmail($mail);
//                    $mensajes='Este correo fue enviado con exito...';
                    $this->flashmessenger()->addMessage('Se le ha enviado un correo a la cuenta indicada, por favor seguir las instrucciones.');
                
                    
                    
                } catch (\Exception $e) {
//                    $mensajes='Este correo no esta registrado...';
                    $this->flashmessenger()->addMessage('Este correo no esta registrado.');
                    
                }

                if ($results) {
                    $config = $this->getServiceLocator()->get('Config');
                    $bodyHtml = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml">
                                               <head>
                                               <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
                                               </head>
                                               <body>
                                                    <div style="color: #7D7D7D"><br />
                                                    Hola '.ucwords($usuario->va_nombre_cliente).',<br /><br />  
                                                    Para recuperar tu contraseña debes hacer <a href="' . $config['host']['base'] . '/?value=' . utf8_decode($results) . '">Clic Aquí</a><br /><br /> 
                                                    o copiar la siguiente url en su navegador:<br /><br />' . $config['host']['base'] . '/?value=' . utf8_decode($results) .'          
                                                     </div>
                                                     <br /><br /><br />
                                                     <img src="'.$config['host']['img'].'/img/logo.png" title="listadelsabor.com"/>
                                               </body>
                                               </html>';

                    $message = new Message();
                    $message->addTo($mail)
                            ->addFrom('listadelsabor@innovationssystems.com', 'listadelsabor.com')
                            ->setSubject('Recuperación de contraseña');
                    $bodyPart = new \Zend\Mime\Message();
                    $bodyMessage = new \Zend\Mime\Part($bodyHtml);
                    $bodyMessage->type = 'text/html';
                    $bodyPart->setParts(array(
                        $bodyMessage
                    ));
                    $message->setBody($bodyPart);
                    $message->setEncoding('UTF-8');

                    $transport = $this->getServiceLocator()->get('mail.transport'); // new SendmailTransport();//$this->getServiceLocator('mail.transport')
                    $transport->send($message);
                }
                
            return $this->redirect()->toUrl('/cambio');
            }
            else{ 
                foreach ($form->getInputFilter()->getInvalidInput() as $error)
                {
                 
                    $mensajes = $error->getMessages();
                     return new JsonModel(array(
                          'menssage' =>$mensajes,
                           'success'=>false
                            ));
                exit;
                    }
                    }
        }
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $mensajes = $flashMessenger->getMessages();
                return new JsonModel(array(
                          'menssage' =>$mensajes,
                           'success'=>false
                            ));
                exit;
        }
        return array(
            'form' => $form,
            'mensaje' => $mensajes
        );
         return $view;
    }

    
    
    public function comprovarvalueAction()
            
    {
        $password = $this->params()->fromQuery('value');
        $results = $this->getClientesTable()->consultarPassword($password);
      //  var_dump($results->in_id);exit;
        if($results)
        {
             $mensajes='Ingrese su nueva Contraseña.';
                         return new JsonModel(array(
                          'menssage' =>$mensajes,
                           'success'=>true
                            ));exit;   
        }else                  
            {
           $mensajes='El token recibido no es válido o es obsoleto. Por favor verifique el enlace recibido en su correo.';
                    return new JsonModel(array(
                          'menssage' =>$mensajes,
                           'success'=>false
                            ));exit;                       
                                
            }  
    }
    public function recuperarAction() {
        $view =new ViewModel();
        $this->layout('layout/layout-portada2');
        $password = $this->params()->fromPost('value');
        $form = new UpdatepassForm();
        $request = $this->getRequest();  
        $form->setData($request->getPost());
        if ($request->isPost()) {
            if ($form->isValid()) {
                try {
                    $results = $this->getClientesTable()->consultarPassword($password);
                } catch (\Exception $e) {
                    $mensajes='El token recibido no es válido o es obsoleto. Por favor verifique el enlace recibido en su correo.';
                    
                    return new JsonModel(array(
                          'menssage' =>$mensajes,
                           'success'=>false
                            ));
                }
                if ($results) {

                    $nuevopass = $this->params()->fromPost('va_contrasena');
                   // echo 'eee';exit;
                    if ($this->getClientesTable()->cambiarPassword($nuevopass, $results->in_id)) {
                       $mensajes =  'La contraseña se actualizo correctamente...';
             return new JsonModel(array(
                          'menssage' =>$mensajes,
                           'success'=>true
                            ));
                exit;
                        return $this->redirect()->toUrl('/auth');
                    } else {
                       $mensajes =   'La contraseña se no se pudo actualizar correctamente...';
                        
                         return new JsonModel(array(
                          'menssage' =>$mensajes,
                           'success'=>false
                            ));
                exit;
                    }
                    return $this->redirect()->toUrl('/cambio-contrasena?value=' . $password);
                }
               }
            }
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            $mensajes = $flashMessenger->getMessages();
        }
                    
        return array(
            'form' => $form,
            'password' => $password,
            'mensaje' => $mensajes//$this->flashmessenger()->getMessages()
        );
        return $view;
    }

    
   

    public function getClientesTable() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\ClientesTable');
        }
        return $this->clientesTable;
    }
 

}