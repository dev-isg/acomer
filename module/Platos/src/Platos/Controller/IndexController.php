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

    public function indexAction() {
        $local = (int) $this->params()->fromQuery('id');
//        var_dump($restaurante);exit;
        $lista = $this->getPlatosTable()->fetchAll($local);
//                    $array=array();
//             foreach($lista as $result){
//                 $array[]=$result;
//             }
//            var_dump($array);exit;

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

    public function agregarplatosAction() {
        $local = (int) $this->params()->fromQuery('id');

//        $restaurante=(int) $this->params()->fromQuery('res', 35);
        $adpter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new PlatosForm($adpter, $local);
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $plato = new Platos();
            $form->setInputFilter($plato->getInputFilter());
//            $form->setData($request->getPost());
            //para que reconosca un archivo file en el form
            $form->setInputFilter($plato->getInputFilter());
            $nonFile = $request->getPost()->toArray();
            $File = $this->params()->fromFiles('va_imagen');
            $data = array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray()
            );


            $form->setData($data);
            // var_dump($this->getRequest()->getPost()->toArray());exit;
            // var_dump($data);exit;

            if ($form->isValid()) {
                //obtengo data de img
                $nonFile = $request->getPost()->toArray();
                $File = $this->params()->fromFiles('va_imagen');
                $plato->exchangeArray($form->getData());
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                if (!$adapter->isValid()) {
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach ($dataError as $key => $row) {
                        $error[] = $row;
                    }
                    $form->setMessages(array('imagen' => $error));
                } else {


                               $anchura = 407;
              $altura = 272; 
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
                       $copia = "C:/xampp/htdocs/acomer/public/imagenes/$nom-$imf";
                       imagejpeg($nuevaimagen,$copia);
                      $this->getPlatosTable()->guardarPlato($plato,$File,$local);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);   
                  }
                  if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/xampp/htdocs/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                      $this->getPlatosTable()->guardarPlato($plato,$File,$local);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);   
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/xampp/htdocs/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                    $this->getPlatosTable()->guardarPlato($plato,$File,$local);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local); 
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
                       $copia = "C:/xampp/htdocs/acomer/public/imagenes/$nom-$imf";
                       imagejpeg($nuevaimagen,$copia);
                     $this->getPlatosTable()->guardarPlato($plato,$File,$local);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local); 
                  }
                   if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/xampp/htdocs/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                     $this->getPlatosTable()->guardarPlato($plato,$File,$local);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local); 
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/xampp/htdocs/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                      $this->getPlatosTable()->guardarPlato($plato,$File,$local);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local);   
                  }
               }
                }
            }
        }
        return array('form' => $form, 'id' => $local);
    }


    
//    public function editarplatosAction()   
//    {   
//
////    /*
////     * editar platos
//     */

//    public function editarplatosAction()
//     
//    {   
//        $id = (int) $this->params()->fromRoute('in_id', 38);//fromRoute('in_id', 0);
//        $va_nombre = $this->params()->fromRoute('va_nombre',0);//fromRoute('va_nombre',0);
////      
//         
//        if (!$id) {
//           return $this->redirect()->toUrl($this->
//            getRequest()->getBaseUrl().'/platos/index/agregarplatos');  
//        }
//        try {
//
//            $plato = $this->getPlatosTable()->getPlato($id);
////            var_dump($plato);exit;
//        }
//        catch (\Exception $ex) {
//
//            return $this->redirect()->toUrl($this->
//            getRequest()->getBaseUrl().'/platos'); 
//        }
//           $adpter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
//        $form  = new PlatosForm($adpter);
//        $form->bind($plato);
//        $form->get('submit')->setAttribute('value', 'MODIFICAR');
//        $request = $this->getRequest();
//        
//        if ($request->isPost()) {
//                           
//            $form->setInputFilter($plato->getInputFilter());
//            $nonFile = $request->getPost()->toArray();
//            $File    = $this->params()->fromFiles('va_imagen');
//            $data    = array_merge_recursive(
//                        $this->getRequest()->getPost()->toArray(),          
//                       $this->getRequest()->getFiles()->toArray()
//                   ); 
////            var_dump($data);exit;
//            $form->setData($data); 
////            var_dump($form->isValid());exit;
//            if (true) {
//                
////                $nonFile = $request->getPost()->toArray();
////               $File = $this->params()->fromFiles('va_imagen');
//               
//                $adapter = new \Zend\File\Transfer\Adapter\Http();
////                $adapter->setDestination('C:\xampp\htdocs\acomer\public\imagenes');
////                 echo 'hola';exit;
//               //  $adapter->setDestination(dirname(__DIR__).'/public/imagenes');
////                  if ($adapter->receive($File['name'])) { //echo 'dddds';exit;
//                        //$restaurante->exchangeArray($form->getData());
//                     
//                      $plato2=$request->getPost()->toArray();
//                      $data2    = array_merge_recursive($plato2,array('in_id'=>$id));
////                         $this->getPlatosTable()->guardarPlato($plato,$File);//,35
//                       $this->getPlatosTable()->editarPlato($data2 ,$File,1);
//                $this->redirect()->toUrl('/platos');
////                    }
//                
//            }
//        }
// 
//     return array(
//            'in_id' => $id,
//            'va_nombre' => $va_nombre,
//            'form' => $form,
//        );
//        
//    }
//    


    public function editarplatosAction()   
    {   
//     var_dump('hasta aka');
        $id = (int) $this->params()->fromRoute('in_id', 0);
        $va_nombre = 'prueba';//$this->params()->fromRoute('va_nombre',0);
        $idlocal=(int) $this->params()->fromRoute('id_pa', 0);
//          var_dump($id);exit;
               
        if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/restaurante/index/agregarrestaurante');  
        }
        try {
            $restaurante = $this->getPlatosTable()->getPlato($id);
//            var_dump($restaurante);exit;
        }
        catch (\Exception $ex) {
            return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/platos'); 
             
        }
      $adpter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form  = new PlatosForm($adpter,$idlocal);

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
//            var_dump($form->isValid());
            if ($form->isValid()) {
//                   ECHO 'HELLO';EXIT;
                $nonFile = $request->getPost()->toArray();
               $File = $this->params()->fromFiles('va_imagen');
               
                       $anchura = 407;
              $altura = 272; 
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
                   $this->getPlatosTable()->guardarPlato($restaurante,$File);
                      $this->redirect()->toUrl('/platos/index?id='.$idlocal);  
                  }
                  if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                 $this->getPlatosTable()->guardarPlato($restaurante,$File);
                      $this->redirect()->toUrl('/platos/index?id='.$idlocal);     
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                 $this->getPlatosTable()->guardarPlato($restaurante,$File);
                      $this->redirect()->toUrl('/platos/index?id='.$idlocal);  
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
                    $this->getPlatosTable()->guardarPlato($restaurante,$File);
                      $this->redirect()->toUrl('/platos/index?id='.$idlocal);  
                  }
                   if($info['extension']=='png')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefrompng($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagepng($nuevaimagen,$copia);
                    $this->getPlatosTable()->guardarPlato($restaurante,$File);
                      $this->redirect()->toUrl('/platos/index?id='.$idlocal);  
                  }
                  if($info['extension']=='gif')      
                  {   $nom = $nonFile['va_nombre'];
                      $viejaimagen=  imagecreatefromgif($File['tmp_name']);
                      $nuevaimagen = imagecreatetruecolor($anchura, $altura);
                       imagecopyresized($nuevaimagen, $viejaimagen, 0, 0, 0, 0, $anchura, $altura, $ancho, $alto);
                       $copia = "C:/source/zf2/acomer/public/imagenes/$nom-$imf";
                       imagegif($nuevaimagen,$copia);
                       $this->getPlatosTable()->guardarPlato($restaurante,$File);
                      $this->redirect()->toUrl('/platos/index?id='.$idlocal);  
                  }
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
        $this->layout('layout/layout-portada');
        $id = $this->params()->fromQuery('id');
        $plato = $this->params()->fromQuery('q');
        $listarecomendacion = $this->getPlatosTable()->getPlatoxRestaurant($id)->toArray();

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
                $form->setData($datos);
                if ($form->isValid()) {
                    $this->getComentariosTable()->agregarComentario($form->getData());
                    setcookie('id' . $id, 1);
//                    $form->clearAttributes();
                    $form->setData(array('va_nombre' => '', 'va_email' => '', 'tx_descripcion' => '')); 
                    $this->redirect()->toUrl('/platos/index/verplatos?id='.$id);
                }
            } 
        } 
//        else {
//          $form->setData(array('va_nombre' => '', 'email' => '', 'tx_descripcion' => ''));  
//        }

//    var_dump($listarcomentarios);Exit;


        $formu = new Formularios();
        $comidas = $this->joinAction()->toArray();
        $com = array();
        foreach ($comidas as $y) {
            $com[$y['ch_distrito']] = $y['ch_distrito'];
        }
        $formu->get('distrito')->setValueOptions($com);
        $formu->get('q')->setValue($plato);
        $formu->get('submit')->setValue('Buscar');
        $this->layout()->clase = 'Detalle';
        
         $listarcomentarios = $this->getPlatosTable()->getComentariosxPlatos($id);

         $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($listarcomentarios));
         $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
         $paginator->setItemCountPerPage(10);
              
        $view->setVariables(array('lista' => $listarecomendacion, 'comentarios' => $paginator, 'form' => $form, 'formu' => $formu,
            'servicios' => $servicios,
            'pagos' => $pagos, 'locales' => $locales, 'cantidad' => $this->getCount($listarcomentarios),'variable'=>$id));
        return $view;
    }

    public function joinAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('ta_ubigeo');
        $select->where(array('ch_provincia' => 'LIMA'));
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
