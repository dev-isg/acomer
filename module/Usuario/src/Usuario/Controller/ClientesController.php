<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Usuario\Controller;

use Zend\Mvc\Controller\AbstractActionController;



use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\View\Model\JsonModel;
use Usuario\Model\Usuario;
use SanAuth\Controller\AuthController;
use Zend\Session\Container;
use Usuario\Model\ClientesTable;
use Usuario\Model\Clientes;
use Usuario\Form\ClientesForm;
use Zend\Form\Element;
use Zend\Validator\File\Size;
use Zend\Http\Header\Cookie;
use Zend\Http\Header;
use Zend\Db\Sql\Sql;
use Zend\Mail\Message;

//use Grupo\Controller\IndexController;

class ClientesController extends AbstractActionController {

    protected $clientesTable;
     protected $usuarioTable;
    protected $ruta;
    protected $_options;
    static $rutaStatic;
    static $rutaStatic2;
    static $rutaStatic3;
    protected $comentariosTable;
    protected $storage;
    protected $authservice;

    public function __construct() {
        $this->_options = new \Zend\Config\Config(include APPLICATION_PATH . '/config/autoload/global.php');
 
    }

    public function agregarclienteAction() {
        $view = new ViewModel();
        $this->layout('layout/layout-portada2');
       // $form = new ClientesForm();
     //   $form->get('submit')->setValue('Crear Usuario');
       // $request = $this->getRequest();
        //var_dump($request);exit;
       // if ($request->isPost()) {
             $va_nombre_cliente = $this->params()->fromPost('va_nombre_cliente');
             $va_email = $this->params()->fromPost('va_email');
             $va_notificacion = $this->params()->fromPost('va_notificacion');
             $va_contrasena = $this->params()->fromPost('va_contrasena');
             $clientes = array('va_nombre_cliente'=>$va_nombre_cliente,
              'va_email'  =>$va_email,
              'va_notificacion'=>$va_notificacion,
              'va_contrasena'=> $va_contrasena );
          //  $clientes = new Clientes();
            //$form->setInputFilter($clientes->getInputFilter());
         //   $form->setData($request->getPost());
//            if (!$form->isValid()) {
              //  $clientes->exchangeArray($form->getData());
              
                $correo = $this->getClientesTable()->verificaCorreo($va_email);
               
                if ($correo === false) { 
                    $this->getClientesTable()->guardarClientes($clientes, md5($va_nombre_cliente));
                    $this->correo($va_email, $va_nombre_cliente, md5($va_nombre_cliente));
                    $bienvenido = 'Bienvenido  <html><body><strong>' . $va_nombre_cliente . '</strong></body></html> a Listadelsabor';
                    $mensaje = 'Tu cuenta está casi lista para usarse, solo tienes que activarla desde tu correo electrónico';
                    return new JsonModel(array(
                                'menssage' => array('bienvenido' => $bienvenido, 'saludo' => $mensaje),
                                'success' => true
                            ));
                    exit;
                } else {
                    $mensaje = 'El correo electrónico ' . $va_email . ' ya esta asociado a un usuario';
                    return new JsonModel(array(
                                'menssage' => $mensaje,
                                'success' => false
                            ));
                    exit;
                }
//            } else {
//                foreach ($form->getInputFilter()->getInvalidInput() as $error) {
//                    print_r($error->getMessages());
//                }
//                return new JsonModel(array(
//                            'menssage' => $error->getMessages(),
//                            'success' => false
//                        ));
//                exit;
//            }
      //  }
        $view->setVariables(array(
            'form' => $form,
            'mensaje' => $mensaje,
//            'user' => $user,
//            'loginUrl'  =>$loginUrl,   
        ));
        return $view;
    }

    public function grupoparticipoAction() {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $renderer->inlineScript()->prependFile($this->_options->host->base . '/js/main.js')
                ->prependFile($this->_options->host->base . '/js/masonry/post-like.js')
                ->prependFile($this->_options->host->base . '/js/masonry/superfish.js')
                ->prependFile($this->_options->host->base . '/js/masonry/prettify.js')
                ->prependFile($this->_options->host->base . '/js/masonry/retina.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.masonry.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.infinitescroll.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/custom.js');
        $categoria = $this->getGrupoTable()->tipoCategoria();
        $this->layout()->categorias = $categoria;
        if ($_COOKIE['tipo'] or $_GET['tipo'] or $_GET['valor']) {
            if ($_COOKIE['tipo'] == 'Eventos' or $_GET['tipo'] == 'Eventos' or $_GET['valor'] == 'Eventos') {
                $this->layout()->active1 = 'active';
            } else {
                $this->layout()->active = 'active';
            }
        } else {
            $this->layout()->active = 'active';
        }
        $id = $this->params()->fromQuery('id');
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $id = $storage->read()->in_id; // $this->params()->fromQuery('id');
        $valor = $this->headerAction($id);
        $usuariosgrupos = $this->getUsuarioTable()->usuariosgrupos($id);
        if (count($usuariosgrupos) == 0) {
            $mensaje = 'Aún no participas en ningún grupo, ¿qué esperas para participar en uno?';
        }
//       $categorias = $this->getUsuarioTable()
//                        ->categoriasunicas($id)->toArray();
//        for ($i = 0; $i < count($categorias); $i++) {
//            $otrosgrupos = $this->getUsuarioTable()->grupossimilares($categorias[$i]['idcategoria'], $categorias[$i]['id']);
//        }

        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($usuariosgrupos));
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(12);
        return array(
            'grupo' => $valor,
            'grupospertenece' => $paginator,
            //'otrosgrupos' => $otrosgrupos,
            'mensaje' => $mensaje
        );
    }

    public function misgruposAction() {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $renderer->inlineScript()->prependFile($this->_options->host->base . '/js/main.js')
                ->prependFile($this->_options->host->base . '/js/masonry/post-like.js')
                ->prependFile($this->_options->host->base . '/js/masonry/superfish.js')
                ->prependFile($this->_options->host->base . '/js/masonry/prettify.js')
                ->prependFile($this->_options->host->base . '/js/masonry/retina.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.masonry.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.infinitescroll.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/custom.js');

        $categorias = $this->getGrupoTable()->tipoCategoria();
        $this->layout()->categorias = $categorias;
        if ($_COOKIE['tipo'] or $_GET['tipo'] or $_GET['valor']) {
            if ($_COOKIE['tipo'] == 'Eventos' or $_GET['tipo'] == 'Eventos' or $_GET['valor'] == 'Eventos') {
                $this->layout()->active1 = 'active';
            } else {
                $this->layout()->active = 'active';
            }
        } else {
            $this->layout()->active = 'active';
        }
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $renderer->inlineScript()->prependFile($this->_options->host->base . '/js/main.js');
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $id = $storage->read()->in_id;
        $misgrupos = $this->getGrupoTable()->misgrupos($id);
        if (count($misgrupos) == 0) {
            $mensaje = 'Aún no has creado ningún grupo, ¿qué esperas para crear uno?';
        }
        $valor = $this->headerAction($id);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($misgrupos));
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(12);
        return array(
            'grupo' => $valor,
            'misgrupos' => $paginator,
            'mensaje' => $mensaje
        );
    }

    public function eventosparticipoAction() {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $renderer->inlineScript()
                ->prependFile($this->_options->host->base . '/js/main.js')
                ->prependFile($this->_options->host->base . '/js/masonry/post-like.js')
                ->prependFile($this->_options->host->base . '/js/masonry/superfish.js')
                ->prependFile($this->_options->host->base . '/js/masonry/prettify.js')
                ->prependFile($this->_options->host->base . '/js/masonry/retina.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.masonry.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.infinitescroll.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/custom.js');
        $categorias = $this->getGrupoTable()->tipoCategoria();
        $this->layout()->categorias = $categorias;
        if ($_COOKIE['tipo'] or $_GET['tipo'] or $_GET['valor']) {
            if ($_COOKIE['tipo'] == 'Eventos' or $_GET['tipo'] == 'Eventos' or $_GET['valor'] == 'Eventos') {
                $this->layout()->active1 = 'active';
            } else {
                $this->layout()->active = 'active';
            }
        } else {
            $this->layout()->active = 'active';
        }
//        $id = $this->params()->fromQuery('id');
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $id = $storage->read()->in_id;

        $eventosusuario = $this->getEventoTable()->usuarioseventos($id);
        if (count($eventosusuario) == 0) {
            $mensaje = 'Aún no participas en ningún evento, ¿qué esperas para participar en  uno?';
        }
//         $index=new \Usuario\Controller\IndexController();
        $valor = $this->headerAction($id);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($eventosusuario));
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(12);
        return array(
            'grupo' => $valor,
            'eventos' => $paginator,
            'mensaje' => $mensaje
        );
    }

    public function miseventosAction() {


        $renderer = $this->serviceLocator->get('Zend\View\Renderer\RendererInterface');
        $renderer->inlineScript()
                ->prependFile($this->_options->host->base . '/js/main.js')
                ->prependFile($this->_options->host->base . '/js/masonry/post-like.js')
                ->prependFile($this->_options->host->base . '/js/masonry/superfish.js')
                ->prependFile($this->_options->host->base . '/js/masonry/prettify.js')
                ->prependFile($this->_options->host->base . '/js/masonry/retina.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.masonry.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/jquery.infinitescroll.min.js')
                ->prependFile($this->_options->host->base . '/js/masonry/custom.js');
        $categorias = $this->getGrupoTable()->tipoCategoria();
        $this->layout()->categorias = $categorias;
        if ($_COOKIE['tipo'] or $_GET['tipo'] or $_GET['valor']) {
            if ($_COOKIE['tipo'] == 'Eventos' or $_GET['tipo'] == 'Eventos' or $_GET['valor'] == 'Eventos') {
                $this->layout()->active1 = 'active';
            } else {
                $this->layout()->active = 'active';
            }
        } else {
            $this->layout()->active = 'active';
        }
        $id = $this->params()->fromQuery('id');
        $storage = new \Zend\Authentication\Storage\Session('Auth');
//           var_dump($storage->read()->va_imagen);exit;
        $id = $storage->read()->in_id;
        $miseventos = $this->getEventoTable()->miseventos($id);
        if (count($miseventos) == 0) {
            $mensaje = 'Aún no has creado ningún evento, ¿qué esperas para crear uno?';
        }
        $valor = $this->headerAction($id);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($miseventos));
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(12);

        return array
            (
            'grupo' => $valor,
            'miseventos' => $paginator,
            'mensaje' => $mensaje
        );
    }

    public function correomovill($mail, $usuario) {

        $results = $this->getClientesTable()->generarPassword($mail);
        $usuario = $this->getClientesTable()->getUsuarioxEmail($mail);

        $config = $this->getServiceLocator()->get('Config');
        $bodyHtml = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml">
                                               <head>
                                               <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
                                               </head>
                                               <body>
                                                    <div style="color: #7D7D7D"><br />
                                                    Hola ' . ucwords($usuario->va_nombre_cliente) . ',<br /><br />  
                                                    Para poder culminar tu registro debes ingresar una contraseña debes hacer hacer <a href="' . $config['host']['base'] . '/?value=' . utf8_decode($results) . '">Clic Aquí</a><br /><br /> 
                                                    o copiar la siguiente url en su navegador:<br /><br />' . $config['host']['base'] . '/?value=' . utf8_decode($results) . '          
                                                     </div>
                                                     <br /><br /><br />
                                                     <p>Listadelsabor te da la bienvenida</p>
                                                     <br />
                                                     <img src="' . $config['host']['img'] . '/img/logo.png" title="listadelsabor.com"/>
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

    public function correo($correo, $usuario, $valor) {
        $message = new Message();
        $message->addTo($correo, $usuario)
                ->setFrom('listadelsabor@innovationssystems.com', 'listadelsabor.com')
                ->setSubject('Confirmación de Registro en Listadelsabor.com');
        $bodyHtml = '<!DOCTYPE html><html xmlns="http://www.w3.org/1999/xhtml">
                                               <head>
                                               <meta http-equiv="Content-type" content="text/html;charset=UTF-8"/>
                                               </head>
                                               <body>
                                                    <div style="color: #7D7D7D"><br />
                                                     Hola  <strong style="color:#133088; font-weight: bold;">' . $usuario . ',</strong><br /><br />
                                        Tu cuenta en <a href="' . self::$rutaStatic3 . '">listadelsabor.com</a> está casi lista para usar.<br /><br />
                                        Activa tu cuenta haciendo <a href="' . self::$rutaStatic3 . '/auth?token=' . $valor . ' ">"click aqui"</a> <br /><br />
                                        O copia la siguiente dirección en tu navegador:<br /><br />
                                        <a href="' . self::$rutaStatic3 . '/?token=' . $valor . ' ">' . self::$rutaStatic3 . '/?token=' . $valor . '</a>
                                        <br /><br /><br />
                                        <a href="' . self::$rutaStatic3 . '"><img src="' . self::$rutaStatic2 . '/logo.png" title="listadelsabor.pe"/></a>
                                         
                                                     </div>
                                               </body>
                                               </html>';
        $bodyPart = new \Zend\Mime\Message();
        $bodyMessage = new \Zend\Mime\Part($bodyHtml);
        $bodyMessage->type = 'text/html';
        $bodyPart->setParts(array(
            $bodyMessage
        ));
        $message->setBody($bodyPart);
        $message->setEncoding('UTF-8');

        $transport = $this->getServiceLocator()->get('mail.transport');
        $transport->send($message);
    }

    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }

        return $this->authservice;
    }

    public function getSessionStorage() {
        if (!$this->storage) {
            $this->storage = $this->getServiceLocator()->get('SanAuth\Model\MyAuthStorage');
        }

        return $this->storage;
    }

    public function facebook() {
        require './vendor/facebook/facebook.php';
        $facebook = new \Facebook(array(
                    'appId' => $this->_options->facebook->appId,
                    'secret' => $this->_options->facebook->secret,
                    'cookie' => false,
                    'scope' => 'email,publish_stream'
                ));
        $user = $facebook->getUser();
        if ($user) {
            try {
                $user_profile = $facebook->api('/me');
            } catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }
        }


        if ($user) {
            $logoutUrl = $facebook->getLogoutUrl();
            $id_facebook = $user_profile['id'];
            $name = $user_profile['name'];
            $email = $user_profile['email'];
            $naitik = $facebook->api('/naitik');
//           //$clientesTable = $this->facxe($id_facebook);
//           $sm = $this->getServiceLocator();
//           $this->clientesTable = $sm->get('Usuario\Model\ClientesTable');
//           var_dump($this->clientesTable);exit;
//
//            if (count($id_face) > 0) {
//                $correo = $id_face[0]['va_email'];
//                if ($id_face[0]['id_facebook'] == '') {
//                    $this->getClientesTable()->idfacebook($id_face[0]['in_id'], $id_facebook, $logoutUrl);
//                    AuthController::sessionfacebook($correo, $id_facebook);
//                } else {
//                    $this->getClientesTable()->idfacebook2($id_face[0]['in_id'], $logoutUrl);
//                    AuthController::sessionfacebook($correo, $id_facebook);
//                }
//            } else {
//                $this->getClientesTable()->insertarusuariofacebbok($name, $email, $id_facebook, $logoutUrl);
//                AuthController::sessionfacebook($email, $id_facebook);
          //  }
        } else {
            // $url  = $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/');
            $loginUrl = $facebook->getLoginUrl(array('scope' => 'email,publish_stream,read_friendlists',
           //  'redirect_uri' => $this->_options->host->ruta . '/'
                    ));
        }

        return array(
            'user' => $user,
              'id_facebook'=> $id_facebook,     
               'logoutUrl'  =>$logoutUrl,
               'name'=>$name,
               'email'=>$email,      
            'loginUrl' => $loginUrl
        );
    }

    public function getClientesTable() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\ClientesTable');
            $config = $sm->get('Config');
            self::$rutaStatic = $config['host']['images'];
            self::$rutaStatic2 = $config['host']['img'];
            self::$rutaStatic3 = $config['host']['ruta'];
        }
        return $this->clientesTable;
    }

    
//    public function facxe($id_face)
//    {
//       $sql = new Sql($adapter);
//        $select = $sql->select();
//        $select->from('foo');
//        $select->where(array('id' => 2));
//
//        $statement = $sql->prepareStatementForSqlObject($select);
//        $results = $statement->execute();
//    }

  
    public function logueoface($id_facebook, $logoutUrl, $name, $email) {
        $id_face = $this->getClientesTable()->usuarioface($id_facebook);
        if (count($id_face) > 0) {
            if ($id_face[0]['id_facebook'] == '') {
                $this->getClientesTable()->idfacebook($id_face[0]['in_id'], $id_facebook, $logoutUrl);
                $this->layout()->face = $name;
            } else {
                $this->getClientesTable()->idfacebook2($id_face[0]['in_id'], $logoutUrl);
                $this->layout()->face = $name;
            }
        } else {
            $this->getClientesTable()->insertarusuariofacebbok($name, $email, $id_facebook, $logoutUrl);
            $this->layout()->face = $name;
        }
    }

}
