<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Application\Form\Formularios;
use Application\Model\Entity\Procesa;
use Application\Model\Usuario;
use Application\Model\Entity\Album;
use Zend\Json\Json;

use Platos\Model\Platos;
use Platos\Model\PlatosTable; 

class IndexController extends AbstractActionController
{
    protected $configTable;
    public $dbAdapter;
    public function indexAction()
    { 
       
        $view = new ViewModel();
        $this->layout('layout/layout-portada');
        $listades=$this->getConfigTable()->cantComentxPlato(1,'0,3',1);
        $listadeseg=$this->getConfigTable()->cantComentxPlato(1,'3,3',1);
        $listaval=$this->getConfigTable()->cantComentxPlato(2,3,2);
        $listault=$this->getConfigTable()->cantComentxPlato(2,3,3);
        //var_dump($listaval);
        $this->layout()->clase = 'Home';
        $view->setVariables(array('lista' => $listades,'listaseg'=>$listadeseg,'listaval'=>$listaval,'listault'=>$listault,'clase'=>'Home'));
         return $view;
    
//       
    
    }
    
            public function getConfigTable()
    {
        if (!$this->configTable) {
            $sm = $this->getServiceLocator();
            $this->configTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->configTable;
    }
    
    public function josAction()
    {  
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from(array('f' => 'ta_ubigeo')) 
            ->where(array('f.ch_provincia'=>'lima'));
             $selectString = $sql->getSqlStringForSqlObject($select);
          // echo $selectString;exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
    }
   public function detalleubicacionAction()
    { 
         $view = new ViewModel();
          $this->layout('layout/layout-portada');
         $distritos=$this->josAction();
                 $this->layout()->clase = 'Search';
        $view->setVariables(array('distritos' => $distritos));
         return $view;
    
    }
    

     public function verAction()
    { 
         $view = new ViewModel();
         $this->layout('layout/layout-portada');
         $distritos=$this->josAction();
         $lista=$this->getConfigTable()->cantComentarios(2,3);
                 $this->layout()->clase = 'Search';
         $view->setVariables(array('distritos' => $distritos , 'comentarios' => $lista));
        return $view;
    }
    
    public function mapaAction()
    { 
        $distrito = $this->params()->fromQuery('distrito');
        $plato = $this->params()->fromQuery('plato');
        $this->layout('layout/layout-portada'); 
         header('Content-Type: text/html; charset=utf-8');
                        $resultados = false;
                        $palabraBuscar = isset($plato) ? $plato : false ;
                        $list = 1000;
                          $fd = array (  
                            'fq'=> 'en_estado:activo AND distrito:'.$distrito,
                              'sort'=>'en_destaque desc',
                              'fl'=>'latitud,longitud,restaurante,name,plato_tipo',
                              'wt'=>'json');      
                        if ($palabraBuscar)
                        { 
                          require './vendor/SolrPhpClient/Apache/Solr/Service.php';
                          $solar = new \Apache_Solr_Service('192.168.1.44', 8983, '/solr/');
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $palabraBuscar = stripslashes($palabraBuscar);
                          }
                          try
                          {
                            $resultados = $solar->search($palabraBuscar, 0,$list, $fd );
                          }
                          catch (Exception $e)
                          {
                          
                                echo("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");          
                          }
                        }
<<<<<<< HEAD
                         $resultados->getRawResponse();
=======

                     
>>>>>>> cc699f09d036acccbe35d18e41a5b9cd2a22163b
                         $json = $resultados->getRawResponse(); 
                         $distritos=$this->josAction();
                    
                         return  new ViewModel(array('mapa' => $resultados->response->docs ,'hola'=>'siempre nosotros','json'=>$json,'distritos' => $distritos ,));
                        //$mapita = $this->jsonmapasaAction($json); 
                        
                   }
    
<<<<<<< HEAD
    public function jsonmapasaAction()    { 
        $distrito=  $this->params()->fromQuery('distrito');
        $view  = new viewModel();
        $view->setTerminal(true);
        $plato = $this->params()->fromQuery('plato');
                        $resultados = false;
                        $palabraBuscar = isset($plato) ? $plato : false ;
                        $list = 1000;
                          $fd = array (  
                            'fq'=> 'en_estado:activo AND distrito:'.$distrito,
                              'sort'=>'en_destaque desc',
                              'fl'=>'id,latitud,longitud,restaurante,name,plato_tipo',
                              'wt'=>'json');      
                        if ($palabraBuscar)
                        { 
                          require './vendor/SolrPhpClient/Apache/Solr/Service.php';
                          $solar = new \Apache_Solr_Service('192.168.1.44', 8983, '/solr/');
                          if (get_magic_quotes_gpc() == 1)
                          {
                            $palabraBuscar = stripslashes($palabraBuscar);
                          }
                          try
                          {
                            $resultados = $solar->search($palabraBuscar, 0,$list, $fd );
                          }
                          catch (Exception $e)
                          {
                          
                                echo("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");          
                          }
                        }
                     
                         echo $resultados->getRawResponse(); 
                    exit;
                        
                   }
=======
    public function jsonmapasaAction($s){
   echo $s;
        exit();

    }
>>>>>>> cc699f09d036acccbe35d18e41a5b9cd2a22163b

    public function rolesAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $u = new Album($adapter);
        $s=$u->rolAll($adapter);
        $array = array('hola'=>'desde sql',
                        'yea'=>$u->rolAll($adapter)); 
       return new ViewModel($array);
    }
    
    public function addAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $id =(int)$this->params()->fromRoute('in_id',0);
        //var_dump($id);exit;
        $u = new Album($adapter);
        $array = array('artist'=>'sandra' , 
                        'title'=>'ss');
        $u->deleteAlbum($id);

       return new ViewModel($array);
    }
    public function delAction()
    { 
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $id =(int)$this->params()->fromRoute('in_id',0);
        $u = new Album($adapter);
        $u->deleteAlbum($id);
         $valores=array
            ( 
                'url'=>$this->getRequest()->getBaseUrl(),
                'in_id'=>$id );
            return new ViewModel($valores);

    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/index/index');
       
    }
    
    public function actualizarusuarioAction()
    { 
        /* $id = (int) $this->params()->fromRoute('in_id', 0);
        if (!$id) {
            return $this->redirect()
           ->toUrl($this->getRequest()
           ->getBaseUrl().'/application/index/actualizarusuario');
        }
        try {
            $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $u = new Album($adapter);
             $u->obtenerUsuario($id); 
        }
        catch (\Exception $ex) {
            return $this->redirect()
           ->toUrl($this->getRequest()
           ->getBaseUrl().'/application/index/index');
        }
             */

         if($this->getRequest()->isPost())
        {
             $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $u = new Album($adapter);
             $data = $this->request->getPost();
             $u->updateAlbum($id,$data);
             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/index/actualizarusuario/1');
        }
        else
        {    
             $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $u = new Album($adapter);
             $datos=$u->obtenerUsuario($id); 
             $form=new Formularios("form");
               $dao = array  ('nombre'=>$datos['va_nombre'],
              'apellido'=>$datos['va_apellidos'],
              'pass'=>$datos['va_contrasenia'],
              'email'=>$datos['va_email'],
              'rol'=>$datos['Ta_rol_in_id']);
          //var_dump($dao);exit;
              //var_dump($values);exit;
              // $form->populate($values);
            // $va=$form->bind($datos);           
            // $form->setAttribute($values);
             $valores=array
            ( "titulo"=>"Actualizar Usuario",
                "form"=>$form,
                'url'=>$this->getRequest()->getBaseUrl(),
                'in_id'=>$id,
                 'ye' => $dao );
            return new ViewModel($valores);
        }
    }

    public function joinAction()
    {  
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from(array('f' => 'ta_usuario')) 
            ->join(array('b' => 'ta_rol'),'f.Ta_rol_in_id = b.in_id')
           ->where(array('nombre = "peru"','id = 3'));
            $selectString = $sql->getSqlStringForSqlObject($select);
            //echo $selectString;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return new ViewModel(array('hola'=>'desde sql','yea'=>$results));
    }
    
     public function agregarusuarioAction()
    { 
         if($this->getRequest()->isPost())
        {
             $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
             $adapter = $this->dbAdapter; 
             $u = new Album($adapter);
             $data = $this->request->getPost();
             $u->addAlbum($data);
             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/index/agregarusuario/1');
        }
        else
        {
             $form=new Formularios("form");
             $id = (int) $this->params()->fromRoute('in_id', 0);
             $valores=array
            ( "titulo"=>"Registro de Usuario",
                "form"=>$form,
                'url'=>$this->getRequest()->getBaseUrl(),
                'in_id'=>$id );
            return new ViewModel($valores);
        }
    }
    
    
    
   
}
