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

    
   



}
