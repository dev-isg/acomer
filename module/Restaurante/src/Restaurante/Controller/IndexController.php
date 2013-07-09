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


class IndexController extends AbstractActionController
{
  protected $restauranteTable;
  public $dbAdapter;
    
     public function indexAction() {
        $filtrar = $this->params()->fromPost('submit'); 
        $datos = $this->params()->fromPost('texto');
        $comida = $this->params()->fromPost('comida');
        $estado = $this->params()->fromPost('estado');
         if (isset($filtrar)) {
            $lista = $this->getRestauranteTable()->buscarRestaurante($datos,$comida,$estado);
        }
        else {
            $lista = $this->getRestauranteTable()->fetchAll();
        }
        return array(
          'restaurante' => $lista,
            'comida' => $this->comidas()
        );
  
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
            $File = $this->params()->fromFiles('va_imagen');
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
              if($ancho>$alto)
              {
                  $altura =(int)($alto*$anchura/$ancho); 
                  if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagejpeg($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                  if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
               }
                   if($ancho<$alto)
              {
                  $anchura =(int)($ancho*$altura/$alto); 
                  if($info['extension']=='jpg'or $info['extension']=='JPG'or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagejpeg($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                   if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
               }
            }       
          }
        }     
        return array('form' => $form);
     }
 public function editarrestauranteAction()   
    {   
//     var_dump('hasta aka');
        $id = (int) $this->params()->fromRoute('in_id', 0);
        $va_nombre = $this->params()->fromRoute('va_nombre',0);
        //var_dump($id);exit;
               
        if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/restaurante/index/agregarrestaurante');  
        }
        try {
            $restaurante = $this->getRestauranteTable()->getRestaurante($id);
           // var_dump($restaurante);exit;
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

        $form->bind($restaurante);

        $form->get('submit')->setAttribute('value', 'MODIFICAR');
        $request = $this->getRequest();
        $comida = $this->params()->fromPost('va_modalidad');
        
        if ($request->isPost()) {

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
               $File = $this->params()->fromFiles('va_imagen');
               
             $anchura = 240;
              $altura = 143; 
              $imf =$File['name'];
              $info =  pathinfo($File['name']);
              $tamanio = getimagesize($File['tmp_name']);
              $ancho =$tamanio[0]; 
              $alto =$tamanio[1]; 
              if($ancho>$alto)
              {
                  $altura =(int)($alto*$anchura/$ancho); 
                  if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagejpeg($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                  if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
               }
                   if($ancho<$alto)
              {
                  $anchura =(int)($ancho*$altura/$alto); 
                  if($info['extension']=='jpg'or $info['extension']=='JPG'or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagejpeg($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                   if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');  
                  }
               }
                
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
     
     

     
     

        public function cambiaestadoAction() {
              $id = $this->params()->fromQuery('id');
              $estado = $this->params()->fromQuery('estado');
              $this->getRestauranteTable()->estadoRestaurante((int) $id, $estado);
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

}
