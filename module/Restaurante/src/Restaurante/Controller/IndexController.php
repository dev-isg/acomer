<?php

namespace Restaurante\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Zend\Db\Sql\Sql;
use Restaurante\Model\Restaurante;        
use Restaurante\Form\RestauranteForm;       
use Restaurante\Model\RestauranteTable;  
use Zend\Db\Adapter\Adapter;
use Platos\Model\PlatosTable; 
//use Classes\Filter\Alnum;



class IndexController extends AbstractActionController
{
  protected $restauranteTable;
  public $dbAdapter;
    protected $_options;
      protected $platosTable;
    
    public function __construct()
    {
    	$this->_options = new \Zend\Config\Config ( include APPLICATION_PATH . '/config/autoload/global.php' );
    }
     public function indexAction() {
         
        $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }

       
        $datos = $this->params()->fromPost('texto');
       // setcookie('plato',$datos);
        $comida = $this->params()->fromPost('comida');
        $estado = $this->params()->fromPost('estado');
        $lista = $this->getRestauranteTable()->fetchAll();
        $request = $this->getRequest();
         if ($request->isPost()) {
            // var_dump($datos);exit; 
            $lista = $this->getRestauranteTable()->buscarRestaurante($datos,$comida,$estado);
        }

         $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($lista));
         $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
         $paginator->setItemCountPerPage(10);
//          $this->layout('layout/layout-portada');
        return new ViewModel(array(
          'restaurante' => $paginator,//$lista,
            'comida' => $this->comidas(),
            'texto'=>$datos
        ));
  
    }
  
    public function getRestauranteTable() {
        if (!$this->restauranteTable) {
            $sm = $this->getServiceLocator();
            $this->restauranteTable = $sm->get('Restaurante\Model\RestauranteTable');
        }
        return $this->restauranteTable;
    }

   public function getrestauranteoidAction(){
        //$this->_helper->layout->disableLayout();
      $id=$this->params()->fromQuery('id');
      $datos=$this->getRestauranteTable()->getRestaurante($id);
            
       echo Json::encode($datos);
        exit();
      
      
   
    }
    

     
 public function agregarrestauranteAction()
    {  
        $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }
        
        $form = new RestauranteForm();
        $medio =  $this->medio()->toArray();
        $medi = array();
        foreach($medio as $yes){
            $medi[$yes['in_id']] = $yes['va_nombre'];
        }
        $comidas =  $this->comidas()->toArray();
        $com = array();
        foreach($comidas as $y){
            $com[$y['in_id']] = $y['va_nombre_tipo'];
        }
        $form->get('Ta_tipo_comida_in_id')->setValueOptions($com);
        $form->get('va_modalidad')->setValueOptions($medi);
        $form->get('submit')->setValue('INSERTAR');
        $request = $this->getRequest();
        $comida = $this->params()->fromPost('va_modalidad');
        if ($request->isPost()) {
           $restaurante = new Restaurante();
           $form->setInputFilter($restaurante->getInputFilter());
           $nonFile = $request->getPost()->toArray();
           $File    = $this->params()->fromFiles('va_imagen');
           $data    = array_merge_recursive(
                        $this->getRequest()->getPost()->toArray(),          
                       $this->getRequest()->getFiles()->toArray()
                   ); 
    $form->setData($data);     
    if ($form->isValid()) { 
            $nonFile = $request->getPost()->toArray();
        if($File['name']!='')
          {
            $restaurante->exchangeArray($form->getData());
            $adapter = new \Zend\File\Transfer\Adapter\Http();
          if (!$adapter->isValid()){
                     $dataError = $adapter->getMessages();
                     $error = array();
                     foreach($dataError as $key=>$row)
                     {
                         $error[] = $row;
                     }
                     $form->setMessages(array('imagen'=>$error ));
          } else {
              $anchura = 240;
              $altura = 143; 
              $imf =$File['name'];
              $info =  pathinfo($File['name']);
              $tamanio = getimagesize($File['tmp_name']);
              $ancho =$tamanio[0]; 
              $alto =$tamanio[1]; 
              $valor  = uniqid();
              if($ancho>$alto)
              {//echo 'ddd';exit;
                  require './vendor/Classes/Filter/Alnum.php';
                  $altura =(int)($alto*$anchura/$ancho); 
                  if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom);
                  $name = $filtered.'-'.$imf2;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = $this->_options->upload->images . '/restaurante/principal/' . $name;
                       $origen = $this->_options->upload->images . '/restaurante/original/' . $name;
                       imagejpeg($nuevaimagen,$copia);
                       imagejpeg($viejaimagen,$origen);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }

               }
                   if($ancho<$alto)
              {require './vendor/Classes/Filter/Alnum.php';
                  $anchura =(int)($ancho*$altura/$alto); 
                  if($info['extension']=='jpg'or $info['extension']=='JPG'or $info['extension']=='jpeg')      
                  {  $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom); 
                   $name = $filtered.'-'.$imf2;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = $this->_options->upload->images . '/restaurante/principal/' . $name;
                       $origen = $this->_options->upload->images . '/restaurante/original/' . $name;
                       imagejpeg($nuevaimagen,$copia);
                       imagejpeg($viejaimagen,$origen);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }

               }
            }       
          }
          else {   
              $restaurante->exchangeArray($form->getData());
              $name = 'default-img.jpg';
              $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante'); }
           }
          } 
             
        return array('form' => $form);
     }
 public function editarrestauranteAction()   
    {   
        $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }
        $id = (int) $this->params()->fromRoute('in_id', 0);
        $va_nombre = $this->params()->fromRoute('va_nombre',0);
        //var_dump($id);exit;
               
        if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/restaurante/index/agregarrestaurante');  
        }
        try {
            $restaurante = $this->getRestauranteTable()->getRestaurante($id);
            
        }
        catch (\Exception $ex) {
            return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/restaurante'); 
             
        }
      
        $form  = new RestauranteForm();
         $medio =  $this->medio()->toArray();
        $medi = array();
        foreach($medio as $yes){
            $medi[$yes['in_id']] = $yes['va_nombre'];
        }
        $comidas =  $this->comidas()->toArray();
        $com = array();
        foreach($comidas as $y){
            $com[$y['in_id']] = $y['va_nombre_tipo'];
        }
        $form->get('Ta_tipo_comida_in_id')->setValueOptions($com);
        $form->get('va_modalidad')->setValueOptions($medi);
        //$form->get('va_imagen')->setValue($comidas[0]['']);

        $form->bind($restaurante);


        $form->get('submit')->setValue('Editar');
        $request = $this->getRequest();
        $comida = $this->params()->fromPost('va_modalidad');
        
        if ($request->isPost()) {
           
            $File    = $this->params()->fromFiles('va_imagen');
            $form->setInputFilter($restaurante->getInputFilter());
            $nonFile = $request->getPost()->toArray();
            
            $data    = array_merge_recursive(
                        $this->getRequest()->getPost()->toArray(),          
                       $this->getRequest()->getFiles()->toArray()
                   ); 
            $form->setData($data); 
         if ($form->isValid()) { 
            $nonFile = $request->getPost()->toArray();
        if($File['name']!='')
          {
            $adapter = new \Zend\File\Transfer\Adapter\Http();
          if (!$adapter->isValid()){
                     $dataError = $adapter->getMessages();
                     $error = array();
                     foreach($dataError as $key=>$row)
                     {
                         $error[] = $row;
                     }
                     $form->setMessages(array('imagen'=>$error ));
          } else {
              $anchura = 240;
              $altura = 143; 
              $imf =$File['name'];
              $info =  pathinfo($File['name']);
              $tamanio = getimagesize($File['tmp_name']);
              $ancho =$tamanio[0]; 
              $alto =$tamanio[1]; 
              $valor  = uniqid();
              $imagen_restaurante=$this->getRestauranteTable()->getRestaurante($id);
              $imagen = $imagen_restaurante->va_imagen;
              if($ancho>$alto)
              { 
                $eliminar = $this->_options->upload->images . '/restaurante/original/' . $imagen;
                $eliminar1 = $this->_options->upload->images . '/restaurante/principal/' . $imagen;
                  unlink($eliminar);
                  unlink($eliminar1);
                  require './vendor/Classes/Filter/Alnum.php';
                  $altura =(int)($alto*$anchura/$ancho); 
                  if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom);
                  $name = $filtered.'-'.$imf2;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = $this->_options->upload->images . '/restaurante/principal/' . $name;
                       $origen = $this->_options->upload->images . '/restaurante/original/' . $name;
                       imagejpeg($nuevaimagen,$copia);
                       imagejpeg($viejaimagen,$origen);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }

               }
                   if($ancho<$alto)
              {$eliminar = $this->_options->upload->images . '/restaurante/original/' . $imagen;
                $eliminar1 = $this->_options->upload->images . '/restaurante/principal/' . $imagen;
                  unlink($eliminar);
                  unlink($eliminar1);
                       require './vendor/Classes/Filter/Alnum.php';
                  $anchura =(int)($ancho*$altura/$alto); 
                  if($info['extension']=='jpg'or $info['extension']=='JPG'or $info['extension']=='jpeg')      
                  {  $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom); 
                   $name = $filtered.'-'.$imf2;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = $this->_options->upload->images . '/restaurante/principal/' . $name;
                       $origen = $this->_options->upload->images . '/restaurante/original/' . $name;
                       imagejpeg($nuevaimagen,$copia);
                       imagejpeg($viejaimagen,$origen);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }

               }
            }       
          }
          else {   
              $restaura=$this->getRestauranteTable()->getRestaurante($id);
              $name = $restaura->va_imagen;//'default-img.jpg';
              $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante'); }
           }
        }
 
     return array(
            'in_id' => $id,
            'va_nombre' => $va_nombre,
            'form' => $form,
        );
        
    }
        public function restaurantemedio($id)
    {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select() 
           ->from(array('f' => 'ta_restaurante_has_ta_medio_pago')) 
            ->join(array('b' => 'Ta_medio_pago'),'f.Ta_medio_pago_in_id = b.in_id',array('va_nombre'))
           ->where(array('f.Ta_restaurante_in_id'=>$id)); 
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;       
     }
 
      public function medio()
    {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from('ta_medio_pago');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
            
     }
     

     public function mediAction(){
         $id=$this->params()->fromQuery('in_id');
        $ubigeo=$this->getRestauranteTable()->medio($id);
//        var_dump($this->getUbigeoTable()->getProvincia($iddepar));exit;
        echo Json::encode($ubigeo);
        exit();
    }
     

    public function comidas()
    {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from('ta_tipo_comida');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
            
     }
     
   public function getPlatosTable() {
        if (!$this->platosTable) {
            $sm = $this->getServiceLocator();
            $this->platosTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->platosTable;
    }
    

       public function cambiaestadoLocalRestauranteAction($id, $estado){
        $this->getPlatosTable()->eliminarPlato($id, $estado);
       }
        public function cambiaestadoAction() 
        {
            $id = $this->params()->fromQuery('id');
            $estado = $this->params()->fromQuery('estado');
            $this->getRestauranteTable()->estadoRestaurante((int) $id, $estado);      
            $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $sql = new Sql($adapter);
            $select = $sql->select()
            ->from('ta_local')
            ->join(array('tl'=>'ta_plato_has_ta_local'), 'ta_local.in_id = tl.Ta_local_in_id', array('plato'=>'Ta_plato_in_id'))
            ->where(array('ta_local.Ta_restaurante_in_id'=>$id));   
            $selectString = $sql->getSqlStringForSqlObject($select); 
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);  
            $plato=$results->toArray();
            foreach ($plato as $result) 
            {
            $this->cambiaestadoLocalRestauranteAction($result['plato'],$estado);
             $this->getPlatosTable()->cromSolr($result['plato'],'');
            }
            $this->redirect()->toUrl('/restaurante/index');
         }    
             
    public function jsoncomidaAction() {

        $datos = $this->getRestauranteTable()->comidas();
        echo Json::encode($datos);
        exit();
    }
    public function medioAction()
        {

        $datos = $this->getRestauranteTable()->medio();
        echo Json::encode($datos);
        //var_dump($datos);
        exit();
    }

     public function eliminarsolarAction() {
          
       $solr = \Classes\Solr::getInstance()->getSolr();
        if ($solr->ping()){
           $solr->deleteByQuery('*:*');
            $solr->commit();
            $solr->optimize();
          echo 'cron finalizado eliminar';exit;
        }
    }
    
      public function cronsolarAction()
        {
        $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $sql = new Sql($adapter); 
            $select = $sql->select()
            ->from('ta_plato');
            $selectS = $sql->getSqlStringForSqlObject($select);   
            $resul = $adapter->query($selectS, $adapter::QUERY_MODE_EXECUTE);
            $plato=$resul->toArray();
            foreach ($plato as $result)     
            {$this->getPlatosTable()->cromSolr($result['in_id'],'');  }
           echo 'cron finalizado';exit;
        }

    }
                        
                                  
                             
