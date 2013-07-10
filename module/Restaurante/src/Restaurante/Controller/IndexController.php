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
        
            $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($lista));
         $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
         $paginator->setItemCountPerPage(10);
         
        return array(
          'restaurante' => $paginator,//$lista,
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
                  //var_dump($name);exit;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$name";
                       imagejpeg($nuevaimagen,$copia);
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
                       $copia = "C:/source/zf2/acomer/public/imagenes/$name";
                       imagejpeg($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
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
                  //var_dump($name);exit;
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$name";
                       imagejpeg($nuevaimagen,$copia);
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
                       $copia = "C:/source/zf2/acomer/public/imagenes/$name";
                       imagejpeg($nuevaimagen,$copia);
                       $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$name);
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
            $this->estadoRestauranteSolarAction($result['plato']);
            $this->cambiaestadoLocalRestauranteAction($result['plato'],$estado);
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
    
             public function estadoRestauranteSolarAction($id) {
           $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $sql = new Sql($adapter);
            $selecttot = $sql->select()
                ->from('ta_plato')
                ->join(array('c' => 'ta_comentario'), 'c.ta_plato_in_id=ta_plato.in_id', array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(c.in_id)')), 'left')
                    ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_nombre' => 'va_nombre'), 'left')
                    ->join(array('pl' => 'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
                    ->join(array('tl' => 'ta_local'), 'tl.in_id = pl.ta_local_in_id', array('de_latitud', 'de_longitud', 'va_direccion'), 'left')
                    ->join(array('tr' => 'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurant_nombre' => 'va_nombre', 'restaurant_estado' => 'en_estado'), 'left')
                    ->join(array('tu' => 'ta_ubigeo'), 'tu.in_id = tl.ta_ubigeo_in_id', array('distrito' => 'ch_distrito'), 'left')
                    ->where(array('ta_plato.in_id' => $id));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $plato = $results->toArray();
        require './vendor/SolrPhpClient/Apache/Solr/Service.php';
        $solr = new \Apache_Solr_Service('192.168.1.38', 8983, '/solr');
        if ($solr->ping()){
            $solr->deleteByQuery('id:' . $id);
            $document = new \Apache_Solr_Document();
            $document->id = $id;
            $document->name = $plato[0]['va_nombre'];
            $document->tx_descripcion = $plato[0]['tx_descripcion'];
            $document->va_precio = $plato[0]['va_precio'];
            $document->en_estado = $plato[0]['en_estado'];
            $document->plato_tipo = $plato[0]['tipo_plato_nombre'];
            $document->va_direccion = $plato[0]['va_direccion'];
            $document->restaurante = $plato[0]['restaurant_nombre'];
            $document->en_destaque = $plato[0]['en_destaque'];
            $document->latitud = $plato[0]['de_latitud'];
            $document->longitud = $plato[0]['de_longitud'];
            $document->distrito = $plato[0]['distrito'];
            $document->va_imagen = $plato[0]['va_imagen'];
            $document->comentarios = $plato[0]['cantidad'];
            $document->restaurant_estado = $plato[0]['restaurant_estado'];
            $document->puntuacion = $plato[0]['Ta_puntaje_in_id'];
            $solr->addDocument($document);
            $solr->commit();
            $solr->optimize();
        }
    }

}
