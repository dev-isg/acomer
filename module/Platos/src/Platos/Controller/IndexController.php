<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Platos\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\Json\Json;
use Platos\Model\Platos;
use Platos\Model\PlatosTable;
use Platos\Form\PlatosForm;
use Application\Form\Formularios;
use Zend\Form\Element;
use Zend\Validator\File\Size;
use Zend\Http\Header\Cookie;
use Zend\Http\Header;
use Zend\Db\Sql\Sql;

class IndexController extends AbstractActionController {

    protected $platosTable;
    protected $comentariosTable;
    protected $_options;
	public function __construct()
	{
		$this->_options = new \Zend\Config\Config ( include APPLICATION_PATH . '/config/autoload/global.php' );
	}
    public function indexAction() {
        
        $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }
        
        $basePath = $this->getRequest()->getBasePath();
        $local = (int) $this->params()->fromQuery('id');
        $lista = $this->getPlatosTable()->fetchAll($local);
                $request = $this->getRequest();              
        if ($request->isPost()) {
            $consulta=$this->params()->fromPost('texto');
            $lista = $this->getPlatosTable()->fetchAll($local,$consulta);           
        }
        return new ViewModel(array(
                    'platos' => $lista,
                    'idlocal' => $local,
                ));
    }

    public function fooAction() {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return array();
    }
   public function restaurante($id)
        {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $sql = new Sql($adapter);
            $select = $sql->select()
                ->from('ta_local')
            ->where(array('in_id' => $id));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;       
     }
    public function agregarplatosAction() {     
       $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }       
        $local = (int) $this->params()->fromQuery('id');
        $adpter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new PlatosForm($adpter, $local);
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) { 
             $datos =$this->request->getPost();
             $plato_otro = $datos['va_otros'];
            $plato = new Platos();
            $form->setInputFilter($plato->getInputFilter());
            $nonFile = $request->getPost()->toArray();
            $File = $this->params()->fromFiles('va_imagen');
            $data = array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()
            );
            $form->setData($data);       
            if ($form->isValid()) {
                $nonFile = $request->getPost()->toArray();
//                $File = $this->params()->fromFiles('va_imagen');
        if($File['name']!='')
          {
            $plato->exchangeArray($form->getData());
            $adapter = new \Zend\File\Transfer\Adapter\Http();
                if (!$adapter->isValid()) {
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach ($dataError as $key => $row) {
                        $error[] = $row;
                    }
                    $form->setMessages(array('imagen' => $error));
                }
                else {
                       $restaurante = $this->restaurante($local);
                       $rowset = $restaurante;
                       $array = array();
                       foreach($rowset as $resul){
                       $array[]=$resul; }                         
                       $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                        $adapter = $this->dbAdapter;
                        $sql = new Sql($adapter);
                        $select = $sql->select()
                        ->from('ta_local')
                       ->join(array('tl'=>'ta_plato_has_ta_local'), 'ta_local.in_id = tl.Ta_local_in_id',array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(tl.Ta_plato_in_id)')), 'left')   
                        ->where(array('ta_local.in_id'=>$local));   
                        $selectString = $sql->getSqlStringForSqlObject($select); 
                        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                        $plat =$results;
                         $platos=array();
                        foreach ($plat as $result) 
                        { $platos[] = $result;}      
              $anchura = 407;
              $altura = 272;
              $destacadox =215;
              $destacadoy =155;
              $generalx =145;
              $generaly =112;
              $imf =$File['name'];
              $info =  pathinfo($File['name']);   
              $tamanio = getimagesize($File['tmp_name']);
              $ancho =$tamanio[0]; 
              $alto =$tamanio[1]; 
              $valor  = uniqid();
              if($ancho>$alto)
              {
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
                      $destaque = imagecreatetruecolor($destacadox, $destacadoy);
                      $generale = imagecreatetruecolor($generalx, $generaly);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       imagecopyresized($destaque, $viejaimagen, 0, 0, 0, 0, $destacadox, $destacadoy,$ancho, $alto);
                       imagecopyresized($generale, $viejaimagen, 0, 0, 0, 0, $generalx, $generaly,$ancho, $alto);
                    if($platos[0]['cantidad']<=0)
                       {    mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777); 
                            mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777); 
                                mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777); 
                                       $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                       $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                       $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                       $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                            imagejpeg($nuevaimagen,$principal);
                                            imagejpeg($destaque,$destacado);
                                            imagejpeg($generale,$general);
                                            imagejpeg($viejaimagen,$original);
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$local.'/' .$name;               
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);                                   
                       }
                       else{    if($platos[0]['cantidad']>=5)
                                { echo 'cantidad maxima de platos';}
                                else
                                   {
                                     $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                                 imagejpeg($nuevaimagen,$principal);
                                                 imagejpeg($destaque,$destacado);
                                                 imagejpeg($generale,$general);
                                                 imagejpeg($viejaimagen,$original);  
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$local.'/' .$name;                
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);                                       
                                   }        
                             }                                      
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
                      $destaque = imagecreatetruecolor($destacadox, $destacadoy);
                      $generale = imagecreatetruecolor($generalx, $generaly);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       imagecopyresized($destaque, $viejaimagen, 0, 0, 0, 0, $destacadox, $destacadoy,$ancho, $alto);
                       imagecopyresized($generale, $viejaimagen, 0, 0, 0, 0, $generalx, $generaly,$ancho, $alto);
                      if($platos[0]['cantidad']<=0)
                       {    mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777); 
                            mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777); 
                                mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' , 0777); 
                                       $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                       $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                       $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                       $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                            imagejpeg($nuevaimagen,$principal);
                                            imagejpeg($destaque,$destacado);
                                            imagejpeg($generale,$general);
                                            imagejpeg($viejaimagen,$original);
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$local.'/' .$name;               
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);                                   
                       }
                       else{    if($platos[0]['cantidad']>=5)
                                { echo 'cantidad maxima de platos';}
                                else
                                   {
                                     $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                                 imagejpeg($nuevaimagen,$principal);
                                                 imagejpeg($destaque,$destacado);
                                                 imagejpeg($generale,$general);
                                                 imagejpeg($viejaimagen,$original);  
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$local.'/' .$name;                
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);                                       
                                   }        
                          } 
                   }    
                  }
                }
            }
                 else {   
              $plato->exchangeArray($form->getData());
              $adapter = new \Zend\File\Transfer\Adapter\Http();
              $name = 'platos-default.png';
              $this->getPlatosTable()->guardarPlato($plato,$name,$local,$plato_otro);
              return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);
               }
        }
        }
        return array('form' => $form, 'id' => $local);
    }
    
    

    public function editarplatosAction()   
    {   
        
       $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }
        $id = (int) $this->params()->fromRoute('in_id', 0);
        $platicos =  $this->platicos($id)->toArray();
       $comeya =$platicos[0]['va_imagen'];
       $va_nombre = 'prueba';//$this->params()->fromRoute('va_nombre',0);
        $idlocal=(int) $this->params()->fromRoute('id_pa', 0);
     if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/restaurante/index/agregarrestaurante');  
        }
        try {
            $restaurante = $this->getPlatosTable()->getPlato($id);
            
        }
        catch (\Exception $ex) {
            return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/platos'); 
             
        }
      $adpter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form  = new PlatosForm($adpter,$idlocal);
        
        $form->get('va_imagen')->setValue($comeya);
        $form->bind($restaurante);

        $form->get('submit')->setAttribute('value', 'MODIFICAR');
        $request = $this->getRequest();
        $comida = $this->params()->fromPost('va_modalidad');
        
        if ($request->isPost()) {
            $datos =$this->request->getPost();
             $plato_otro = $datos['va_otros'];
            $form->setInputFilter($restaurante->getInputFilter());
            $nonFile = $request->getPost()->toArray();
            $File    = $this->params()->fromFiles('va_imagen');
            $data    = array_merge_recursive(
                        $this->getRequest()->getPost()->toArray(),          
                       $this->getRequest()->getFiles()->toArray()
                   ); 
            $form->setData($data);
      
  if ($form->isValid()) {
                //obtengo data de img
                $nonFile = $request->getPost()->toArray();
//                $File = $this->params()->fromFiles('va_imagen');
        if($File['name']!='')
          {
            $adapter = new \Zend\File\Transfer\Adapter\Http();
            
                if (!$adapter->isValid()) {
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach ($dataError as $key => $row) {
                        $error[] = $row;
                    }
                    $form->setMessages(array('imagen' => $error));
                } else {
 
                    $restaura = $this->restaurante($idlocal);
                       $rowset = $restaura;
                       $array = array();
                       foreach($rowset as $resul){
                       $array[]=$resul; }                         
                       $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                        $adapter = $this->dbAdapter;
                        $sql = new Sql($adapter);
                        $select = $sql->select()
                        ->from('ta_local')
                       ->join(array('tl'=>'ta_plato_has_ta_local'), 'ta_local.in_id = tl.Ta_local_in_id',array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(tl.Ta_plato_in_id)')), 'left')   
                        ->where(array('ta_local.in_id'=>$idlocal));   
                        $selectString = $sql->getSqlStringForSqlObject($select); 
                        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                        $plat =$results;
                         $platos=array();
                        foreach ($plat as $result) 
                        { $platos[] = $result;}
              $anchura = 407;
              $altura = 272;
              $destacadox =215;
              $destacadoy =155;
              $generalx =145;
              $generaly =112;
              $imf =$File['name'];
              $info =  pathinfo($File['name']);
              $tamanio = getimagesize($File['tmp_name']);
              $ancho =$tamanio[0]; 
              $alto =$tamanio[1]; 
              $valor  = uniqid();
              
             $va = $this->getPlatosTable()->getPlato($id);
             $imagen_antigua = $va->va_imagen;  
              if($ancho>$alto)
              {
                $eliminar = $this->_options->upload->images . '/plato/destacado/' . $imagen_antigua;
                $eliminar1 = $this->_options->upload->images . '/plato/general/' . $imagen_antigua;
                $eliminar2 = $this->_options->upload->images . '/plato/original/' . $imagen_antigua;
                $eliminar3 = $this->_options->upload->images . '/plato/principal/' . $imagen_antigua;
                  unlink($eliminar);
                  unlink($eliminar1);
                  unlink($eliminar2);
                  unlink($eliminar3);  
                  
          
                                       
                  require './vendor/Classes/Filter/Alnum.php';
                  $altura =(int)($alto*$anchura/$ancho); 
                  if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom);
                  $name = $filtered.'-'.$imf2;
                  
                  
                     if(!is_dir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777))
                       { mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777); 
                            mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777); 
                                mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777); 
                                       $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                       $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                       $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                       $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;}
                    
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);  
                      $destaque = imagecreatetruecolor($destacadox, $destacadoy);
                      $generale = imagecreatetruecolor($generalx, $generaly);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       imagecopyresized($destaque, $viejaimagen, 0, 0, 0, 0, $destacadox, $destacadoy,$ancho, $alto);
                       imagecopyresized($generale, $viejaimagen, 0, 0, 0, 0, $generalx, $generaly,$ancho, $alto);                                      
                     $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                     $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                     $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                     $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                                 imagejpeg($nuevaimagen,$principal);
                                                 imagejpeg($destaque,$destacado);
                                                 imagejpeg($generale,$general);
                                                 imagejpeg($viejaimagen,$original);             
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' .$name; 
                       $this->getPlatosTable()->guardarPlato($restaurante,$nombre,$idlocal,$plato_otro);
                    $this->redirect()->toUrl('/platos/index?id='.$idlocal);   
                  }
               }
  
                   if($ancho<$alto)
              {$eliminar = $this->_options->upload->images . '/plato/destacado/' . $imagen_antigua;
                $eliminar1 = $this->_options->upload->images . '/plato/general/' . $imagen_antigua;
                $eliminar2 = $this->_options->upload->images . '/plato/original/' . $imagen_antigua;
                $eliminar3 = $this->_options->upload->images . '/plato/principal/' . $imagen_antigua;
                  unlink($eliminar);
                  unlink($eliminar1);
                  unlink($eliminar2);
                  unlink($eliminar3);
                  require './vendor/Classes/Filter/Alnum.php';
                  $anchura =(int)($ancho*$altura/$alto); 
                  if($info['extension']=='jpg'or $info['extension']=='JPG'or $info['extension']=='jpeg')      
                  {  $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom); 
                   $name = $filtered.'-'.$imf2;
                   if(!is_dir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777))
                       { mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777); 
                            mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                            mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777); 
                                mkdir($this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777);
                                mkdir($this->_options->upload->images . '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' , 0777); 
                                       $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                       $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                       $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                       $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;}
                       
                      $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       $destaque = imagecreatetruecolor($destacadox, $destacadoy);
                      $generale = imagecreatetruecolor($generalx, $generaly);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                        imagecopyresized($destaque, $viejaimagen, 0, 0, 0, 0, $destacadox, $destacadoy,$ancho, $alto);
                       imagecopyresized($generale, $viejaimagen, 0, 0, 0, 0, $generalx, $generaly,$ancho, $alto);                                      
                     $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                     $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                     $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                     $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' . $name;
                                                 imagejpeg($nuevaimagen,$principal);
                                                 imagejpeg($destaque,$destacado);
                                                 imagejpeg($generale,$general);
                                                 imagejpeg($viejaimagen,$original);  
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$idlocal.'/' .$name; 
                       $this->getPlatosTable()->guardarPlato($restaurante,$nombre,$idlocal,$plato_otro);
                    $this->redirect()->toUrl('/platos/index?id='.$idlocal);   
                  }
               }
                }
              
            
            }
                 else {  
              $platos = $this->getPlatosTable()->getPlato($id);
              $adapter = new \Zend\File\Transfer\Adapter\Http();
             $name = $platos->va_imagen;
              $this->getPlatosTable()->guardarPlato($restaurante,$name,$idlocal,$plato_otro);
                    $this->redirect()->toUrl('/platos/index?id='.$idlocal); 
               }
            }      
        }
 
     return array(
            'in_id' => $id,
            'va_nombre' => $va_nombre,
            'form' => $form,
         'idlocal'=>$idlocal
        );
        
    }
   public function platicos($id)
        {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $adapter = $this->dbAdapter;
            $sql = new Sql($adapter);
            $select = $sql->select()
                ->from('ta_plato')
            ->where(array('in_id' => $id));
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;       
     }
     
     
     
    public function eliminarAction() {

        $id = $this->params()->fromQuery('id');
        $estado = $this->params()->fromQuery('estado');

//        $this->getPlatosTable()->estadoPlato((int) $id, $estado);
//        $this->redirect()->toUrl('/platos/index');
//        $id = $this->params()->fromPost('id');
        $this->getPlatosTable()->eliminarPlato((int) $id, $estado);
        $this->redirect()->toUrl('/platos/index');
    }

    /*
     * cambiar el destaque del plato
     */

    public function cambiaestadoAction() {
        $id = $this->params()->fromQuery('id');
        $estado = $this->params()->fromQuery('estado');
        $this->getPlatosTable()->destaquePlato((int) $id, $estado);
        exit();
    }

    /*
     * 
     */

    public function listacomentariosAction() {
        $listarecomendacion = $this->getPlatosTable()->cantComentxPlato();

//        for($i=0;$i<count($listarecomendacion);$i++){
//            
//        }
//        var_dump($listarecomendacion[27]);exit;

        return new ViewModel(array(
                    'lista' => $listarecomendacion
                ));
//        return array('lista'=>$listarecomendacion);
    }

    public function verplatosAction() {

        $view = new ViewModel();
//        $this->layout('layout/layout-portada');
        $datos =$this->params()->fromRoute();  
        $nombre = explode('-', $datos['nombre']);   
        $id = array_pop($nombre);
        if(!$this->getPlatosTable()->getPlato($id)){
            $this->redirect()->toUrl('/');
        }
        $listarecomendacion = $this->getPlatosTable()->getPlatoxRestaurant($id)->toArray();   
//        var_dump($listarecomendacion);Exit;
        $servicios = $this->getPlatosTable()->getServicioxPlato($id);
        $locales = $this->getPlatosTable()->getLocalesxRestaurante($listarecomendacion[0]['restaurant_id']);
        $pagos = $this->getPlatosTable()->getPagoxPlato($id);
        $form = new \Usuario\Form\ComentariosForm();
        $form->get('submit')->setValue('Agregar');
        $request = $this->getRequest();

        if ($request->isPost()) {
    
            if (!isset($_COOKIE['id' . $id])) {
                $datos = $this->getRequest()->getPost()->toArray();
                $datos['Ta_plato_in_id'] = $id;
                $datos['tx_descripcion'] = htmlspecialchars($datos['tx_descripcion']);
                $datos['va_nombre'] = htmlspecialchars($datos['va_nombre']);
                $datos['va_email'] = htmlspecialchars($datos['va_email']);
                $form->setData($datos);
                if ($form->isValid()) {
                    $this->getComentariosTable()->agregarComentario($form->getData());
                    setcookie('id' . $id, 1);
//                    $form->clearAttributes();
                    $form->setData(array('va_nombre' => '', 'va_email' => '', 'tx_descripcion' => '')); 
                    //$this->redirect()->toUrl('/plato?id='.$id);
                    $datos =$this->params()->fromRoute(); 
                    
                    $this->redirect()->toUrl('/plato/'.$datos['nombre']);
                }
            }
        } 
         $formu = new Formularios();
         $comidas = $this->joinAction()->toArray();
         $this->layout()->comidas=$comidas;
         $com = array();
         foreach ($comidas as $y) {
             $com[$y['va_distrito']] = $y['va_distrito'];
         }  
         if($_COOKIE['distrito']){ $formu->get('distrito')->setValue($_COOKIE['distrito']);}
         else{ $formu->get('distrito')->setValue($comidas[41]['va_distrito']);}
        $formu->get('distrito')->setValueOptions($com);
        $formu->get('q')->setValue($_COOKIE['q']);     
        $formu->get('submit')->setValue('Buscar');
        $this->layout()->clase = 'Detalle';
      //  $this->headTitle('ss');
        $listarcomentarios = $this->getPlatosTable()->getComentariosxPlatos($id);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($listarcomentarios));
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);    
        $config = $this->getServiceLocator()->get('Config');
//        var_dump($config['host']['images'].'/'.);exit;
//           if ($this->lista[0]['va_imagen']=='platos-default.png')
//              {echo $config['host']['images']. '/defecto/' . $listarecomendacion[0]['va_imagen'];}
//            else{echo $config['host']['images'] . '/plato/principal/' . $listarecomendacion[0]['va_imagen'];}
            
                                                    
        $this->layout()->title=$listarecomendacion[0]['va_nombre'];   
        $this->layout()->image=$listarecomendacion[0]['va_imagen']=='platos-default.png'?$config['host']['images']. '/defecto/' . $listarecomendacion[0]['va_imagen']:$config['host']['images'] . '/plato/principal/' . $listarecomendacion[0]['va_imagen'];
        $this->layout()->description=$listarecomendacion[0]['tx_descripcion'];
        $this->layout()->url=$config['host']['ruta'].'/plato/'.$datos['nombre'];

        
        
        
        $view->setVariables(array('lista' => $listarecomendacion, 'comentarios' => $paginator, 'form' => $form, 'formu' => $formu,
            'servicios' => $servicios,'urlplato'=>$id,'urlnombre'=>$datos['nombre'],
            'pagos' => $pagos, 'locales' => $locales, 'cantidad' => $this->getCount($listarcomentarios),'variable'=>$id));
        
        return $view;
    }

    public function joinAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('ta_distrito') ;   
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        //var_dump($results);exit;
        return $results;
    }

    public function getComentariosTable() {
        if (!$this->comentariosTable) {
            $s = $this->getServiceLocator();
            $this->comentariosTable = $s->get('Usuario\Model\ComentariosTable');
        }
        return $this->comentariosTable;
    }

    /*
     * para acceder a mi service manager
     */

    public function getPlatosTable() {
        if (!$this->platosTable) {
            $sm = $this->getServiceLocator();
            $this->platosTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->platosTable;
    }

    public function getCount($val) {

//        $aux=$val->toArray();
        //var_dump($aux[0]['num']);Exit;

        return $val->count(); //$aux[0]['num'];//
    }

}
