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
use Usuario\Model\ClientesTable;
use Usuario\Controller\ClientesController;
use Platos\Form\PlatosForm;
use Application\Form\Formularios;
use Zend\Form\Element;
use Zend\Validator\File\Size;
use Zend\Http\Header\Cookie;
use Zend\Http\Header;
use Zend\Db\Sql\Sql;
use SanAuth\Controller\AuthController; 

class IndexController extends AbstractActionController {

    protected $platosTable;
    protected $configTable;
    protected $authservice;
    protected $comentariosTable;
    protected $clientesTable;
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
 public function getConfigTable()
    {
        if (! $this->configTable) {
            $sm = $this->getServiceLocator();
            $this->configTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->configTable;
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
            
            $promoc= $this->params()->fromPost('va_promocion');
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
                        ->where(array('ta_local.in_id'=>$local))
                                ->group('ta_local.in_id');   
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
                  require './vendor/Classes/imageworkshop.php';
                 $alta =(int)($alto*$anchura/$ancho);
                  if($alta>272){$altura=272;}
                  else{$altura=$alta;}
                  if($info['extension']=='jpg' or $info['extension']=='JPG' or $info['extension']=='jpeg')      
                  {   $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom);
                  $name = $filtered.'-'.$imf2;
                $estampa = imagecreatefrompng($this->_options->upload->images . '/defecto/loguito.png');
                $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
               
                $margen_dcho =340;
                $margen_inf = 20;
                $sx = imagesx($estampa);
                $sy = imagesy($estampa);
 imagecopy($viejaimagen, $estampa,  $sx,$alto-100, 0, 0, imagesx($estampa), imagesy($estampa));      
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
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro,$promoc);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/plato/listar?id='.$local);
                             
                       }
                       else{   
//                           if($platos[0]['cantidad']>=5)
//                       }
//                                { echo 'cantidad maxima de platos';}
//                                else
//                                   {
                                     $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                                 imagejpeg($nuevaimagen,$principal);
                                                 imagejpeg($destaque,$destacado);
                                                 imagejpeg($generale,$general);
                                                 imagejpeg($viejaimagen,$original);  
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$local.'/' .$name;                
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro,$promoc);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/plato/listar?id='.$local);                                       
                                  // }        
                             }                                      
                         }
                    }
                   if($ancho<$alto)
              {require './vendor/Classes/Filter/Alnum.php';
                 $anchu =(int)($ancho*$altura/$alto);
                  if($anchu>407){$anchura=407;}
                  else{$anchura=$anchu;}
                  if($info['extension']=='jpg'or $info['extension']=='JPG'or $info['extension']=='jpeg')      
                  {  $nom = $nonFile['va_nombre']; 
                  $imf2 =  $valor.'.'.$info['extension'];
                  $filter   = new \Filter_Alnum();
                  $filtered = $filter->filter($nom); 
                   $name = $filtered.'-'.$imf2;
                    $estampa = imagecreatefrompng($this->_options->upload->images . '/defecto/loguito.png');
                $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);

                $margen_dcho =340;
                $margen_inf = 20;
                $sx = imagesx($estampa);
                $sy = imagesy($estampa);
 imagecopy($viejaimagen, $estampa,  $sx,$alto-100, 0, 0, imagesx($estampa), imagesy($estampa));      
                     $nuevaimagen = imagecreatetruecolor($anchura, $altura);         $destaque = imagecreatetruecolor($destacadox, $destacadoy);
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
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro,$promoc);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/plato/listar?id='.$local);                                   
                       }
                       else{   
//                           if($platos[0]['cantidad']>=5)
//                                { echo 'cantidad maxima de platos';}
//                                else
//                                   {
                                     $principal = $this->_options->upload->images . '/plato/principal/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $destacado = $this->_options->upload->images . '/plato/destacado/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $general = $this->_options->upload->images . '/plato/general/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                     $original = $this->_options->upload->images .  '/plato/original/'.$array[0]['Ta_restaurante_in_id'].'/'.$local.'/' . $name;
                                                 imagejpeg($nuevaimagen,$principal);
                                                 imagejpeg($destaque,$destacado);
                                                 imagejpeg($generale,$general);
                                                 imagejpeg($viejaimagen,$original);  
                             $nombre = $array[0]['Ta_restaurante_in_id'].'/'.$local.'/' .$name;                
                             $this->getPlatosTable()->guardarPlato($plato,$nombre,$local,$plato_otro,$promoc);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/plato/listar?id='.$local);                                       
                                 //  }        
                          } 
                   }    
                  }
                }
            }
                 else {   
              $plato->exchangeArray($form->getData());
              $adapter = new \Zend\File\Transfer\Adapter\Http();
              $name = 'platos-default.png';
              $this->getPlatosTable()->guardarPlato($plato,$name,$local,$plato_otro,$promoc);
              return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/plato/listar?id='.$local);
               }
        }
        }
        return array('form' => $form, 'id' => $local);
    }
    
    
        public function tipo_plato($id)
                {   $this->dbAdapter =$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
                    $adapter = $this->dbAdapter;
                    $sql = new Sql($adapter);
                    $select = $sql->select()
                        ->from('ta_tipo_plato')
                    ->where(array('in_id' => $id));
                    $selectString = $sql->getSqlStringForSqlObject($select);
                    $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                    return $results;       
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
       $va_nombre = 'prueba';
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
            getRequest()->getBaseUrl().'/plato/listar'); 
             
        }
      $adpter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form  = new PlatosForm($adpter,$idlocal);
        $form->get('va_imagen')->setValue($comeya);
        /////////////////////PROMOCIONES////////////////////
       // $form->get('Ta_tipo_plato_in_id')->setOptions(array($platotipo[0]['in_id'] =>$platotipo[0]['va_nombre']  ));
        $form->bind($restaurante);
        $promobind =  $this->getPlatosTable()->promocionxPlato($id)->toArray();

        $aux = array();
        foreach ($promobind as $value) {
            $aux[$value['ta_tag_in_id']] = $value['ta_tag_in_id'];
            $form->get('va_promocion')->setAttribute('value', $aux);    
        }

/////////////////////////////////////////////////////////////////////////////////
        $form->get('submit')->setAttribute('value', 'MODIFICAR');
        $request = $this->getRequest();
       
        if ($request->isPost()) {
            $promoc= $this->params()->fromPost('va_promocion');
            
            
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
                $nonFile = $request->getPost()->toArray();
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
                        ->where(array('ta_local.in_id'=>$idlocal))
                                ->group('ta_local.in_id');   
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
                  $alta =(int)($alto*$anchura/$ancho);
                  if($alta>272){$altura=272;}
                  else{$altura=$alta;}
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
                    
 $estampa = imagecreatefrompng($this->_options->upload->images . '/defecto/loguito.png');
                $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
         
                $margen_dcho =340;
                $margen_inf = 20;
                $sx = imagesx($estampa);
                $sy = imagesy($estampa);
imagecopy($viejaimagen, $estampa,  $sx,$alto-100, 0, 0, imagesx($estampa), imagesy($estampa));      
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
                       $this->getPlatosTable()->guardarPlato($restaurante,$nombre,$idlocal,$plato_otro,$promoc);
                    $this->redirect()->toUrl('/plato/listar?id='.$idlocal);   
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
                  $anchu =(int)($ancho*$altura/$alto);
                  if($anchu>407){$anchura=407;}
                  else{$anchura=$anchu;}
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
                       
                       $estampa = imagecreatefrompng($this->_options->upload->images . '/defecto/loguito.png');
                $viejaimagen=  imagecreatefromjpeg($File['tmp_name']);
                $margen_dcho =340;
                $margen_inf = 20;
                $sx = imagesx($estampa);
                $sy = imagesy($estampa);
imagecopy($viejaimagen, $estampa,  $sx,$alto-100, 0, 0, imagesx($estampa), imagesy($estampa));      
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
                       $this->getPlatosTable()->guardarPlato($restaurante,$nombre,$idlocal,$plato_otro,$promoc);
                    $this->redirect()->toUrl('/plato/listar?id='.$idlocal);   
                  }
               }
                }
              
            
            }
                 else {  
              $platos = $this->getPlatosTable()->getPlato($id);
              $adapter = new \Zend\File\Transfer\Adapter\Http();
             $name = $platos->va_imagen;
              $this->getPlatosTable()->guardarPlato($restaurante,$name,$idlocal,$plato_otro,$promoc);
                    $this->redirect()->toUrl('/plato/listar?id='.$idlocal); 
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
        $this->getPlatosTable()->eliminarPlato((int) $id, $estado);
        $this->redirect()->toUrl('/plato/listar');
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

    
    
    public function consultaAction($limit,$id,$tipo)       
    {
          $texto  = 'en_destaque:si';
            $palabraBuscar = isset($texto) ? $texto : false;
                $query = "($palabraBuscar)";
                $fq = array(
                //    'sort' => 'random_' . uniqid() . ' asc, puntuacion desc',
                    'fq' => 'en_estado:activo  AND restaurant_estado:activo AND -id:'.$id.'AND plato_tipo:'.$tipo,
                    'wt' => 'json'
                );
                $resultados = false;
                if ($query) {
                    $solr = \Classes\Solr::getInstance()->getSolr();    
                    if (get_magic_quotes_gpc() == 1) {
                        $query = stripslashes($query);}
                    try { $resultados = $solr->search($query, 0, $limit, $fq);
                    } catch (Exception $e) {
                  echo ("<div>ingrese algun valor</div>"); }} 
                  
             return $resultados->response->docs;     
    }
    
      
    public function verplatosAction() 
      { 
        $view = new ViewModel();
                if($_GET['callback'] )
                {  
                    header('Content-type: application/x-javascript');
                    header("Status: 200");
                       $idplato = (int) $this->params()->fromQuery('id');    
                       $nombre = $this->params()->fromQuery('va_nombre');
                       $email = $this->params()->fromQuery('va_email');
                       $comentario = $this->params()->fromQuery('tx_descripcion');
                       $puntaje  = (int) $this->params()->fromQuery('Ta_puntaje_in_id');
                       $validar = explode('http://', $comentario);
                       if(count($validar)==2)
                       {$result = array('resultado'=>false);
                                echo "jsonpCallback(".json_encode($result).")"; }
                       else{$comentario =$comentario;
                       $envia = array('Ta_plato_in_id'=> $idplato,
                                       'va_nombre'=>$nombre,
                                       'va_email'=>$email,
                                       'tx_descripcion'=>$comentario,
                                       'Ta_puntaje_in_id'=>$puntaje);
                       
                         $cantidad=$this->getClientesTable()->usuario1($email);
                         if(count($cantidad)==0)
                          {$this->getClientesTable()->agregarComentariomovil($envia);
                           ClientesController::correomovill($email,$nombre); }
                           else{$this->getClientesTable()->agregarComentariomovil($envia);}      
                         $this->getPlatosTable()->cromSolr($idplato,'');  
                                $result = array('resultado'=>true);
                                echo "jsonpCallback(".json_encode($result).")"; }
                                exit();
                                $view->setTerminal(true);
                                return $view;     
               }
        $datos =$this->params()->fromRoute();
        $storage = new \Zend\Authentication\Storage\Session('Auth');
        $session=$storage->read();
        if ($session){           
                    $participa=$this->getClientesTable()->compruebarUsuariox($session->in_id);
                    $activo=$participa->en_estado;}
    if (!isset($session)) { 
        $face = new \Usuario\Controller\ClientesController();
        $facebook = $face->facebook();
        $this->layout()->loginUrl = $facebook['loginUrl'];
        $this->layout()->user = $facebook['user']; 
        if($facebook['id_facebook']){
            $url = $_SERVER['REQUEST_URI']; 
          //$url='/plato/'.$datos['nombre'].'/'.$datos['nombre'];
           // $url='/plato/'.$datos['nombre'];
        $id_face=$this->getClientesTable()->usuarioface($facebook['email']); 
                  if(count($id_face)>0)
                         {if($id_face[0]['id_facebook']=='')  
                        {
                           $this->getClientesTable()->idfacebook($id_face[0]['in_id'],$facebook['id_facebook'],$facebook['logoutUrl']);
                            AuthController::sessionfacebook($facebook['email'], $facebook['id_facebook'],$url); 
                        }     
                         else{
                            $this->getClientesTable()->idfacebook2($id_face[0]['in_id'],$facebook['logoutUrl']);
                                               AuthController::sessionfacebook($facebook['email'], $facebook['id_facebook'],$url); }   }
                         else{
                           $this->getClientesTable()->insertarusuariofacebbok($facebook['name'],$facebook['email'],$facebook['id_facebook'],$facebook['logoutUrl']);  
                                               AuthController::sessionfacebook($facebook['email'], $facebook['id_facebook'],$url); }
       }}  
        
        $urlerror =  $datos['nombre'];
        $nombre = explode('-', $datos['nombre']); 
        $id = array_pop($nombre);
          $listarecomendacion = $this->getPlatosTable()->getPlatoxRestaurant($id)->toArray(); 
//          if(count($listarecomendacion)<1)
//          {$this->redirect()->toUrl('/');}
          $texto = 'restaurante:"'.$listarecomendacion[0]['restaurant_nombre'].'"'; 
                $limit = 10;
                $palabraBuscar = isset($texto) ? $texto : false;
                $query = "($palabraBuscar)";
                $fq = array(
                  //  'sort' => 'random_' . uniqid() . ' asc',
                    'fq' => 'en_estado:activo AND restaurant_estado:activo AND -id:'.$listarecomendacion[0]['in_id'] ,
                    'wt' => 'json'
                );
                $results = false;
                if ($query) {
                    $solr = \Classes\Solr::getInstance()->getSolr();
                    if (get_magic_quotes_gpc() == 1) {
                        $query = stripslashes($query);}
                    try { $results = $solr->search($query, 0, $limit, $fq);
                    } catch (Exception $e) {
                  echo ("<div>ingrese algun valor</div>"); }}
                  if(count($results->response->docs)<10)     
                  {if(count($results->response->docs)==0)
                    {  $consulta = $this->consultaAction(10,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs; }
                    elseif(count($results->response->docs)==1)
                    { $consulta = $this->consultaAction(9,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;}
                     elseif(count($results->response->docs)==2)
                    { $consulta = $this->consultaAction(8,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']);  
                    $resultados =$results->response->docs;
                    } elseif(count($results->response->docs)==3)
                    {$consulta = $this->consultaAction(7,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;
                    }  elseif(count($results->response->docs)==4)
                    {$consulta = $this->consultaAction(6,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;
                    }  elseif(count($results->response->docs)==5)
                    {$consulta = $this->consultaAction(5,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;
                    }  elseif(count($results->response->docs)==6)
                    {$consulta = $this->consultaAction(4,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;
                    }  elseif(count($results->response->docs)==7)
                    {$consulta = $this->consultaAction(3,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;
                    }  elseif(count($results->response->docs)==8)
                    {$consulta = $this->consultaAction(2,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;
                    }  elseif(count($results->response->docs)==9)
                    {$consulta = $this->consultaAction(1,$listarecomendacion[0]['in_id'], $listarecomendacion[0]['tipo_plato_nombre']); 
                    $resultados =$results->response->docs;
                    }   }
                  else{  $resultados =$results->response->docs;}
                  
        $servicios = $this->getPlatosTable()->getServicioxPlato($id);
        $locales = $this->getPlatosTable()->getLocalesxRestaurante($listarecomendacion[0]['restaurant_id']);
        $pagos = $this->getPlatosTable()->getPagoxPlato($id);
        $form = new \Usuario\Form\ComentariosForm();
        $canonical = new \Application\View\Helper\Canonical;
        $canonicalurl = new \Application\View\Helper\CanonicalUrl;
        $resta=$canonicalurl($canonical($listarecomendacion[0]['restaurant_nombre']));
        $form->get('submit')->setValue('Agregar');
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($session) {
                $datos = $this->getRequest()->getPost()->toArray();
                $datos['Ta_plato_in_id'] = $id;
                $datos['tx_descripcion'] = htmlspecialchars($datos['tx_descripcion']);
                $validar = explode('http://', $datos['tx_descripcion']);
               // if(count($validar)==2){
//                return $this->redirect()->toUrl('/plato/'.$urlerror.'?m=1');
//                }
               // else {
                $form->setData($datos);
                if (!$form->isValid()) {
                    $this->getComentariosTable()->agregarComentario($form->getData(),$participa->in_id);
                    $this->getComentariosTable()->cromSolar($id,''); 
                    $datos =$this->params()->fromRoute(); 
                   if($datos['tx_descripcion']){$this->redirect()->toUrl('/plato/'.$resta.'/'.$datos['nombre']);}
                   
                  }
                //}
            }
        }   
        
        $this->layout()->clase = 'Detalle';
        $listarcomentarios = $this->getPlatosTable()->getComentariosxPlatos($id);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($listarcomentarios));
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(10);    
        $config = $this->getServiceLocator()->get('Config');                                
        $this->layout()->title=$listarecomendacion[0]['va_nombre'];   
        $this->layout()->image=$listarecomendacion[0]['va_imagen']=='platos-default.png'?$config['host']['images']. '/defecto/' . $listarecomendacion[0]['va_imagen']:$config['host']['images'] . '/plato/principal/' . $listarecomendacion[0]['va_imagen'];
        $this->layout()->description=trim($listarecomendacion[0]['restaurant_nombre']).'-'.trim($listarecomendacion[0]['tx_descripcion']).'-'.trim($listarecomendacion[0]['va_direccion']).'-'.trim($listarecomendacion[0]['va_direccion_referencia'].'-('.trim($listarecomendacion[0]['distrito']).')- teléfono:'.trim($listarecomendacion[0]['va_telefono']));
        $this->layout()->url=$config['host']['ruta'].'/plato/'.$resta.'/'.$datos['nombre'];
        $listatitle=trim($listarecomendacion[0]['va_nombre']).':'.
                trim($listarecomendacion[0]['tx_descripcion']).':'.
                trim($listarecomendacion[0]['tipo_plato_nombre']).':'.
                trim($listarecomendacion[0]['restaurant_nombre']).':'.
                trim($listarecomendacion[0]['distrito']).' │ ';
       $menu = $this->menu();
       $view->setVariables(array('lista' => $listarecomendacion, 'comentarios' => $paginator, 'form' => $form, 
            'servicios' => $servicios,'urlplato'=>$id,'urlnombre'=>$datos['nombre'],
            'pagos' => $pagos, 'locales' => $locales, 'cantidad' => $this->getCount($listarcomentarios),'variable'=>$id,
            'listatitle'=>$listatitle, 'masplatos' => $resultados
             ,'listades' => $consulta,'menus'=>$menu,'session'=>$session,'resta'=>$resta,
            'participa'=>$activo,));
       
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
    public function getClientesTable() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\ClientesTable');
        }
        return $this->clientesTable;
    }


    public function getCount($val) {

//        $aux=$val->toArray();
        //var_dump($aux[0]['num']);Exit;

        return $val->count(); //$aux[0]['num'];//
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
            ->from('ta_plato')
               ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_nombre' => 'va_nombre'), 'left')
                    ->join(array('pl' => 'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
                    ->join(array('tl' => 'ta_local'), 'tl.in_id = pl.ta_local_in_id', array('de_latitud'), 'left')
                    ->join(array('tr' => 'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurant_nombre' => 'va_nombre'), 'left')    
             ->where(array('ta_plato.en_estado'=>'activo','tr.en_estado'=>'activo'));
            $selectS = $sql->getSqlStringForSqlObject($select);  
            $resul = $adapter->query($selectS, $adapter::QUERY_MODE_EXECUTE);
            $plato=$resul->toArray();
           foreach ($plato as $result)     
            { 
            $this->getPlatosTable()->comen($result['in_id']);    
             $this->getPlatosTable()->cromSolr($result['in_id'],1); 
            }
           echo 'cron finalizado';exit;
        }  
 public function menu()
    {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from('ta_menu')
  ->where(array('en_estado'=>'activo'))
                ->order('in_orden ASC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
         return $results->toArray();
    }
    public function getAuthService() {
        if (!$this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }
        return $this->authservice;
    }
 public function verplatos2Action() 
      {
       $datos =$this->params()->fromRoute();  
       $nombre = explode('-', $datos['nombre']); 
       $id = array_pop($nombre);
       $listarecomendacion = $this->getPlatosTable()->getPlatoxRestaurant($id)->toArray(); 
       $restaurante=$listarecomendacion[0]['restaurant_nombre'];
       $canonical = new \Application\View\Helper\Canonical;
       $canonicalurl = new \Application\View\Helper\CanonicalUrl;
        $resta=$canonicalurl($canonical($restaurante));
       return $this->redirect()->toUrl('/plato/'.$resta.'/'.$datos['nombre']); }
}
