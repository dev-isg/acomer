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



class IndexController extends AbstractActionController
{
    protected $platosTable;
    public function indexAction()
    {
        $lista=$this->getPlatosTable()->fetchAll();
        return new ViewModel(array(
            'platos' => $lista
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
        $restaurante=(int) $this->params()->fromQuery('id', 35);
        $form = new PlatosForm();      
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
                        
            if (!$form->isValid()) {
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
//                    var_dump('hola');exit;
                    $this->getPlatosTable()->guardarPlato($plato,$File,$restaurante);
                    return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos'); 
                }
//                //guardo en bd
//                $this->getPlatosTable()->guardarPlato($plato);

                // Redirect to list of albums
               // return $this->redirect()->toRoute('album');
            }
        }
        
        
        return array('form' => $form);
        
    }
    
    
   /*
    * editar platos
    */
    public function editarplatosAction()
     
    {   
        $id = (int) $this->params()->fromRoute('in_id', 0);
        $va_nombre = $this->params()->fromRoute('va_nombre',0);

        if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/platos/index/agregarplatos');  
        }
        try {
            $plato = $this->getPlatosTable()->getPlato($id);
           // var_dump($restaurante);exit;
        }
        catch (\Exception $ex) {
            return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/platos'); 
        }
      
        $form  = new PlatosForm();
        $form->bind($plato);
        $form->get('submit')->setAttribute('value', 'MODIFICAR');
        $request = $this->getRequest();
        
        if ($request->isPost()) {

            $form->setInputFilter($plato->getInputFilter());
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
               
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setDestination('C:\source\zf2\acomer\public\imagenes');
                
               //  $adapter->setDestination(dirname(__DIR__).'/public/imagenes');
                  if ($adapter->receive($File['name'])) { //echo 'dddds';exit;
                        //$restaurante->exchangeArray($form->getData());
                         $this->getRestauranteTable()->guardarRestaurante($plato,$File);
                $this->redirect()->toUrl('/restaurante');
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
        $this->redirect()->toUrl('/plato/index');
        
//        $id = $this->params()->fromPost('id');
//        $this->getPlatosTable()->eliminarPlato((int) $id);
//        $this->redirect()->toUrl('/platos/index');
    }
    /*
     * cambiar el destaque del plato
     */
//        public function cambiaestadoAction() {
//        $id = $this->params()->fromQuery('id');
//        $estado = $this->params()->fromQuery('estado');
//        $this->getPlatosTable()->estadoPlato((int) $id, $estado);
//        $this->redirect()->toUrl('/plato/index');
//    }
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
