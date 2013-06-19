<?php

namespace Usuario\Controller;



use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Usuario\Model\Usuario;          // <-- Add this import
use Usuario\Form\UsuarioForm;       // <-- Add this import
use Usuario\Model\UsuarioTable;  
class IndexController extends AbstractActionController
{
  protected $usuarioTable;
  
    public function indexAction() {
        $filtrar = $this->params()->fromPost('submit'); //$this->_request->getParams();
        $datos = $this->params()->fromPost('texto');
        $tipo = $this->params()->fromPost('listado');
        if (isset($filtrar)) {

            $lista = $this->getUsuarioTable()->buscarUsuario($datos, $tipo);
        } else {

            $lista = $this->getUsuarioTable()->fetchAll();
        }
 return new ViewModel(array(
                    'usuarios' => $lista,
                ));

    }
   public function agregarusuarioAction()
    {
        $form = new UsuarioForm();
        $form->get('submit')->setValue('INSERTAR');
        $request = $this->getRequest();
        if ($request->isPost()) {
           $datos =$this->request->getPost();
           //var_dump($datos);exit;
           $pass1 = $datos['va_contrasenia'];
           $pass2 = $datos['va_contrasenia2'];
           $usuario = new Usuario();
            $form->setInputFilter($usuario->getInputFilter());
            $form->setData($request->getPost());
              
            if ($form->isValid()) {
                $usuario->exchangeArray($form->getData());
                if($pass1==$pass2){
                $this->getUsuarioTable()->guardarUsuario($usuario);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario');      
            }
             }
        }
      
        return array('form' => $form);
    }
    
    
          public function editarusuarioAction()
     
    {
        $id = (int) $this->params()->fromRoute('in_id', 0);
        //var_dump($id);exit;
        if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/usuario/index/agregarusuario');  
        }
        try {
            $usuario = $this->getUsuarioTable()->getUsuario($id);
           // var_dump($usuario);exit;
        }
        catch (\Exception $ex) {
            return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/usuario'); 
        }
        $form  = new UsuarioForm();
        $form->bind($usuario);
        $form->get('submit')->setAttribute('value', 'MODIFICAR');
         
       // $form->get('password')->setAttribute('renderPassword', true);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $datos =$this->request->getPost();
            $pass1 = $datos['va_contrasenia'];
            $pass2 = $datos['va_contrasenia2'];
            $form->setInputFilter($usuario->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                 if($pass1==$pass2){
                $this->getUsuarioTable()->guardarUsuario($usuario);
                $this->redirect()->toUrl('/usuario');
                }
            }
        }

     return array(
            'in_id' => $id,
            'form' => $form,
        );
        
    }
    public function reseAction()
    {
      
        $array = array('hola'=>'LISTADO DE USUARIOS',
                        'yea' => $this->getUsuarioTable()->todosUsuarios(),);
       return new ViewModel($array);
    }

    //obitenen el estado de la bd
    public function jsonestadoAction() {

        $datos = $this->getUsuarioTable()->estado();
        echo Json::encode($datos);
        exit();
    }

    public function eliminarusuAction() {
        $id = $this->params()->fromPost('id');
        $this->getUsuarioTable()->deleteUsuario((int) $id);
        $this->redirect()->toUrl('/usuario/index');
    }

    public function cambiaestadoAction() {
        $id = $this->params()->fromQuery('id');
        $estado = $this->params()->fromQuery('estado');
        $this->getUsuarioTable()->estadoUsuario((int) $id, $estado);
        $this->redirect()->toUrl('/usuario/index');
    }

    public function editarAction() {
         $id = $this->params()->fromPost('id');
         $data=$this->params()->fromPost('datos');
        $this->getUsuarioTable()->editarUsuario($id,$data);
        // var_dump($datos);exit;
//               return new ViewModel(array(
//                    $datos
//                ));
      //  return $datos;
    }

    public function getUsuarioTable() {
        if (!$this->usuarioTable) {
            $sm = $this->getServiceLocator();
            $this->usuarioTable = $sm->get('Usuario\Model\UsuarioTable');
        }
        return $this->usuarioTable;
    }

    
    public function getusuarioidAction(){
        //$this->_helper->layout->disableLayout();
      $id=$this->params()->fromQuery('id');
      $datos=$this->getUsuarioTable()->getUsuario($id);
            
       echo Json::encode($datos);
        exit();
      
      
   
    }
    //------------------------pruebas no usados----------------------------------------------
   public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return array();
    }

    public function listarAction(){
        //echo 'holla mundddo';exit;
        $request = $this->getRequest();
         if ($request->isPost()) {
       //$datos=$request->post()->toArray();
       $datos=$this->params()->fromPost('texto');
       $nom=$this->params()->fromPost('listado');
       //$rol=$this->params()->fromPost('rol');
       
       $tipo=$nom;//(isset($nom))?$nom:$rol;
       // var_dump($tipo);exit;

       //$this->redirect()->toUrl('http://zf2.isg.com:81/usuario/index');

      $val=$this->getUsuarioTable()->buscarUsuario($datos,$tipo);

              return new ViewModel(array(
            'lista' => $val  
        ));
      //echo  $this->_request->getParam('texto');exit;
    }
  }
  //retorna json 

      public function jsonlistarAction(){
        
        //$request = $this->getRequest();
        $datos=$this->getUsuarioTable()->listar();
         //var_dump(Json::encode(array('datos'=>$datos)));exit; 
        echo Json::encode($datos);
        exit();

  }
  
    public function jsonrolAction(){
        
        //$request = $this->getRequest();
        $datos=$this->getUsuarioTable()->estado();
         //var_dump(Json::encode(array('datos'=>$datos)));exit; 
        echo Json::encode($datos);
        exit();

  }
    public function listarvariosAction(){
         $echo = new IndexController();
        $mas =$echo->rolesAction();
        var_dump($mas);exit;
      $datos=$this->getUsuarioTable()->listar2();
      var_dump($datos);exit;
    }
    //imprimer con roles desde sql del zend
    public function moreAction(){

        $datos=$this->getUsuarioTable()->moretablas();
      
        return new viewModel();
        
    }

    public function obtonerjoinAction(){
      $id=$this->params()->fromQuery('id');
      //var_dump($id);exit;
      $datos=$this->getUsuarioTable()->getAlbum($id);
      var_dump($datos);exit;
      
    }


    public function rolesAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $u = new Usuario($adapter);
        $s=$u->rolAll($adapter);
        $array = array('hola'=>'desde sql',
                        'yea'=>$u->rolAll($adapter)); 
       return new ViewModel($array);
    }




}
