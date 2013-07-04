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
use Zend\Form\Element;
use Zend\Validator\File\Size;
  

use Zend\Db\Sql\Sql;



class IndexController extends AbstractActionController
{
    protected $platosTable;
    protected $comentariosTable;

    public function indexAction()
    {   
        $local=(int) $this->params()->fromQuery('id');
//        var_dump($restaurante);exit;
        $lista=$this->getPlatosTable()->fetchAll($local);
        return new ViewModel(array(
            'platos' => $lista,
            'idlocal'=>$local,
        ));
    }

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return array();
    }
 
    
    public function agregarplatosAction()
    {
        $local=(int) $this->params()->fromQuery('id');
     
//        $restaurante=(int) $this->params()->fromQuery('res', 35);
        $adpter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $form = new PlatosForm($adpter,$local);      
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $plato = new Platos();
            $form->setInputFilter($plato->getInputFilter());
//            $form->setData($request->getPost());
            //para que reconosca un archivo file en el form
           $form->setInputFilter($plato->getInputFilter());
           $nonFile = $request->getPost()->toArray();
           $File    = $this->params()->fromFiles('va_imagen');
           $data    = array_merge_recursive(
                        $this->getRequest()->getPost()->toArray(),          
                       $this->getRequest()->getFiles()->toArray()
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
                if (!$adapter->isValid()){
                     $dataError = $adapter->getMessages();
                     $error = array();
                     foreach($dataError as $key=>$row)
                     {
                         $error[] = $row;
                     }
                     $form->setMessages(array('imagen'=>$error ));
                } else {
                    
                    $adapter->setDestination('C:\xampp\htdocs\acomer\public\imagenes');
                     if ($adapter->receive($File['name'])) {
                        $plato->exchangeArray($form->getData());
                    }
                  
                    $this->getPlatosTable()->guardarPlato($plato,$File,$local);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos?id='.$local); 
                }
//                //guardo en bd
//                $this->getPlatosTable()->guardarPlato($plato);

                // Redirect to list of albums
               // return $this->redirect()->toRoute('album');
            }
        }
        
        
        return array('form' => $form,'id'=>$local);
        
    }
    
    
   /*
    * editar platos
    */
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
        $va_nombre = $this->params()->fromRoute('va_nombre',0);
        $idlocal=(int) $this->params()->fromRoute('id_pa', 0);
        //var_dump($id);exit;
               
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
/*
        
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $adapter = $this->dbAdapter;
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from('ta_tipo_plato');
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $tiplatos=$results;//->toArray();
            
        $com = array();
        foreach($tiplatos as $y){
            $com[$y['in_id']] = $y['va_nombre'];
        }
        $form->get('Ta_tipo_plato_in_id')->setValueOptions($com);*/


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
               
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                //$adapter->setDestination('C:\source\zf2\acomer\public\imagenes');
                
               //  $adapter->setDestination(dirname(__DIR__).'/public/imagenes');
                  if ($adapter->receive($File['name'])) { //echo 'dddds';exit;
                        //$restaurante->exchangeArray($form->getData());
                        // $this->getRestauranteTable()->guardarRestaurante($restaurante,$comida,$File);
               $this->getPlatosTable()->guardarPlato($restaurante,$File);
                      $this->redirect()->toUrl('/platos');
                    }
                
            }
        }
 
     return array(
            'in_id' => $id,
            'va_nombre' => $va_nombre,
            'form' => $form,
        );
        
    }
    /*
     * Eliminar plato
     */
        public function eliminarAction()
    {
          
        $id = $this->params()->fromQuery('id');
        $estado = $this->params()->fromQuery('estado');

        $this->getPlatosTable()->estadoPlato((int) $id, $estado);
        $this->redirect()->toUrl('/platos/index');
        
//        $id = $this->params()->fromPost('id');
//        $this->getPlatosTable()->eliminarPlato((int) $id);
//        $this->redirect()->toUrl('/platos/index');
    }
    /*
     * cambiar el destaque del plato
     */
        public function cambiaestadoAction() {
        $id = $this->params()->fromQuery('id');
        $estado = $this->params()->fromQuery('estado');
        $this->getPlatosTable()->destaquePlato((int) $id, $estado);

    }
    /*
     * 
     */
    public function listacomentariosAction(){
        $listarecomendacion=$this->getPlatosTable()->cantComentxPlato();

//        for($i=0;$i<count($listarecomendacion);$i++){
//            
//        }
//        var_dump($listarecomendacion[27]);exit;
        
                return new ViewModel(array(
            'lista' => $listarecomendacion
        ));
//        return array('lista'=>$listarecomendacion);
    }
    
    public function verplatosAction(){
        $view = new ViewModel();
//        $view->setTerminal(true);
        $this->layout('layout/layout-portada');
        $id=$this->params()->fromQuery('id');
        $listarecomendacion=$this->getPlatosTable()->getPlatoxRestaurant($id)->toArray();
        $listarcomentarios=$this->getPlatosTable()->getComentariosxPlatos($id);//->toArray();
        $servicios=$this->getPlatosTable()->getServicioxPlato($id);
        $locales=$this->getPlatosTable()->getLocalesxRestaurante($listarecomendacion[0]['restaurant_id']);
        $pagos=$this->getPlatosTable()->getPagoxPlato($id);
//       var_dump($locales->toArray());exit;
         $form=new \Usuario\Form\ComentariosForm();
         $form->get('submit')->setValue('Agregar');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $datos=$this->getRequest()->getPost()->toArray();
            $datos['Ta_plato_in_id']=$id;
            $form->setData($datos);
            if ($form->isValid($datos)) {
//                var_dump($datos);Exit;
                $this->getComentariosTable()->agregarComentario($datos); 
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos/index/verplatos?id='.$id); 
            }
        }
        
        
        
        $this->layout()->clase = 'Detalle';
        $view->setVariables(array('lista' => $listarecomendacion,'comentarios'=>$listarcomentarios,'form'=>$form,
               'servicios'=>$servicios,
               'pagos'=>$pagos,'locales'=>$locales));
        return $view;
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
        public function getPlatosTable()
    {
        if (!$this->platosTable) {
            $sm = $this->getServiceLocator();
            $this->platosTable = $sm->get('Platos\Model\PlatosTable');
        }
        return $this->platosTable;
    }
    
       
}
