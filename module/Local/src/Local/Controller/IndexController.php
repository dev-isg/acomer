<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonModule for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Local\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
//use Usuario\Model\Usuario;          // <-- Add this import
use Local\Form\LocalForm;        // <-- Add this import
use Local\Model\LocalTable;
use Local\Model\Ubigeo;

class IndexController extends AbstractActionController
{
     protected $localTable;
     protected $ubigeoTable;
    
    public function indexAction()
    {
        //$this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        //$u=new Ubigeo($this->dbAdapter);
       // var_dump($u->getUbigeo());exit;

       $filtrar = $this->params()->fromPost('submit');
       $id = (int) $this->params()->fromRoute('in_id', 0);
       //var_dump($id);exit;
       if(isset($filtrar)){
           $consulta=$this->params()->fromPost('texto');
               $lista =  $this->getLocalTable()->listar($consulta);         
           }else{
               $lista =  $this->getLocalTable()->listar($id);//$id
           }
//       $lista =  $this->getLocalTable()->listar();
//      var_dump($lista);exit;
        return new ViewModel(array(
                    'locales' => $lista,
            'in_id'=>$id
                ));
       //var_dump($this->getLocaTable()->fetchAll());exit;
       // return array();
    }
    
    public function agregarlocalAction(){
        $form = new LocalForm();
        return array('form' => $form);
    }
    
    public function editarlocalAction(){
        
    }
    
    public function eliminarlocalAction(){
        $id = $this->params()->fromPost('id');
        $this->getLocalTable()->eliminarLocal((int) $id);
        $this->redirect()->toUrl('/local/index');
    }
    public function jsonubigeoAction(){
        $this->getUbigeoTable()->getDepartamento();
        $ubigeo=$this->getUbigeoTable()->getUbigeo();
        
        echo Json::encode($ubigeo);
        exit();
        //print_r($a);exit;
    }
    
    public function jsondepartamentoAction(){
   
        $ubigeo=$this->getUbigeoTable()->getDepartamento();
//        var_dump($this->getUbigeoTable()->getDepartamento());exit;
        echo Json::encode($ubigeo);
        exit();
    }
    
        public function jsonprovinciaAction(){
         $iddepar=$this->params()->fromQuery('iddepa');
        $ubigeo=$this->getUbigeoTable()->getProvincia($iddepar);
//        var_dump($this->getUbigeoTable()->getProvincia($iddepar));exit;
        echo Json::encode($ubigeo);
        exit();
    }

    
        public function jsondistritoAction(){
          $iddepar=$this->params()->fromQuery('iddepa');
          $idprovi=$this->params()->fromQuery('iddpro');
        $ubigeo=$this->getUbigeoTable()->getDistrito($idprovi,$iddepar);
        var_dump($this->getUbigeoTable()->getDistrito($idprovi,$iddepar));exit;
        echo Json::encode($ubigeo);
        exit();
    }


    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /module-specific-root/skeleton/foo
        return array();
    }
    
    public function getLocalTable()
    {
        if (!$this->localTable) {
            $sm = $this->getServiceLocator();
            $this->localTable = $sm->get('Local\Model\LocalTable');
        }
        return $this->localTable;
    }
    
    public function getUbigeoTable(){
            if (!$this->ubigeoTable) {
            $sm = $this->getServiceLocator();
            $this->ubigeoTable = $sm->get('Local\Model\Ubigeo');
        }
        return $this->ubigeoTable;
    }
}