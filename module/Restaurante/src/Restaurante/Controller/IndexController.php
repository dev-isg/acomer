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
  
    public function indexAction() 
            {
        return new ViewModel(array(
            'restaurante' => $this->getRestauranteTable()->fetchAll(),
        ));
    }
  
    public function getRestauranteTable() {
        if (!$this->restauranteTable) {
            $sm = $this->getServiceLocator();
            $this->restauranteTable = $sm->get('Restaurante\Model\RestauranteTable');
        }
        return $this->restauranteTable;
    }

    public function agregarrestauranteAction()
    {
        $form = new RestauranteForm();
        $form->get('submit')->setValue('INSERTAR');
        $request = $this->getRequest();
        if ($request->isPost()) {
           $datos =$this->request->getPost();
           $pass1 = $datos['va_contrasenia'];
           $pass2 = $datos['va_contrasenia2'];
           $usuario = new Usuario();
            $form->setInputFilter($usuario->getInputFilter());
            $form->setData($request->getPost());
              
            if ($form->isValid()) {
                $usuario->exchangeArray($form->getData());
                if($pass1==$pass2){
                $this->getUsuarioTable()->guardarUsuario($usuario);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario');      
            }
             }
        }
      
        return array('form' => $form);
    }
   



}
