<?php

namespace Restaurante\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Restaurante\Model\Restaurante;        
use Restaurante\Form\RestauranteForm;       
use Restaurante\Model\RestauranteTable;  
class IndexController extends AbstractActionController
{
  protected $restauranteTable;
  

   
     public function indexAction() {
        $filtrar = $this->params()->fromPost('submit'); //$this->_request->getParams();
        $datos = $this->params()->fromPost('texto');
        $comida = $this->params()->fromPost('comida');
        $estado = $this->params()->fromPost('estado');
         if (isset($filtrar)) {
            $lista = $this->getRestauranteTable()->buscarRestaurante($datos,$comida,$estado);
        }
        else {

            $lista = $this->getRestauranteTable()->fetchAll();
        }

//    public function indexAction() 
//            {
//        $var=$this->getRestauranteTable()->buscar();

        return new ViewModel(array(
                    'restaurante' => $lista,
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
        $form = new RestauranteForm();
        $form->get('submit')->setValue('INSERTAR');
        $request = $this->getRequest();
        if ($request->isPost()) {
           $restaurante = new Restaurante();
            $form->setInputFilter($restaurante->getInputFilter());
            $form->setData($request->getPost());      
            if ($form->isValid()) {
                $restaurante->exchangeArray($form->getData());
                $this->getRestauranteTable()->guardarRestaurante($restaurante);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/restaurante');      
            }
           }     
        return array('form' => $form);
    }
            public function editarrestauranteAction()
     
    {
        $id = (int) $this->params()->fromRoute('in_id', 0);
        if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/restaurante/index/agregarrestaurante');  
        }
        try {
            $restaurante = $this->getUsuarioTable()->getRestaurante($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/restaurante'); 
        }
        $form  = new RestauranteForm();
        $form->bind($restaurante);
        $form->get('submit')->setAttribute('value', 'MODIFICAR');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($restaurante->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                  $this->getRestauranteTable()->guardarRestaurante($restaurante);
                $this->redirect()->toUrl('/restaurante');            
            }
        }
     return array(
            'in_id' => $id,
            'form' => $form,
        );
        
    }
    

//            public function cambiaestadoAction() {
//                   $id = $this->params()->fromQuery('id');
//                   $estado = $this->params()->fromQuery('estado');
//                   $this->getRestauranteTable()->estadoRestaurante((int) $id, $estado);
//                   $this->redirect()->toUrl('/restaurante/index');
//               }
               

}
