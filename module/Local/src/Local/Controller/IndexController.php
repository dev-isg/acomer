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
use Local\Model\Local;          // <-- Add this import
use Local\Form\LocalForm;        // <-- Add this import
use Local\Model\LocalTable;
use Local\Model\Ubigeo;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Db\TableGateway\TableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;
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
       if(!empty($id)){
       if(isset($filtrar)){
           $consulta=$this->params()->fromPost('texto');
               $lista =  $this->getLocalTable()->listar($consulta);         
           }else{
               $lista =  $this->getLocalTable()->listar($id);//$id
           }
       }else{
           if(isset($filtrar)){
           $consulta=$this->params()->fromPost('texto');
               $lista =  $this->getLocalTable()->listar($consulta);         
           }else{
               $lista =  $this->getLocalTable()->listar();//$id
           }
           
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
           $id=$this->params()->fromQuery('id');
        $form->get('submit')->setValue('INSERTAR');
        $request = $this->getRequest();
        
        $servi=$this->getUbigeoTable()->getServicios();
        
        $array = array();
        foreach($servi as $y){
            $array[$y['in_id']] = $y['va_nombre'];
           
        }
//        for($i=1;$i<=count($array);$i++){
//            $form->get('servicio')->setAttributes(array('id'   => 'servicio'.$i));
//        }
         
        //'id'   => 'servicio'
//        foreach($array as $key=>$value){
//        $form->get('servicio')->setAttributes(array('id'   => $array[$key]));
//        }
        $form->get('servicio')->setValueOptions($array);
        
        if ($request->isPost()) {
            $servicio=$this->params()->fromPost('servicio');
            //  var_dump($servicio);exit;
           $local = new Local();
            //$form->setInputFilter($local->getInputFilter());
            $form->setData($request->getPost());     
//             $form->get('pais');
//             $form->get('departamento');
//              $form->get('provincia');
//               $form->get('distrito');
    //               $hiddenControl = $form->get('ta_restaurante_in_id');
    //               $hiddenControl->setAttribute('value', $id);
    //               $form->add($hiddenControl);
              // var_dump($hiddenControl);exit;
           if ($form->isValid()) {
              
                $local->exchangeArray($form->getData());
                $this->getLocalTable()->guardarLocal($local,$servicio);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/local');          
             }
             else{
               
                $local->exchangeArray($form->getData());
                //var_dump($local);exit;
                $this->getLocalTable()->guardarLocal($local,$servicio);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/local');   
 
                 
                 
             }
        }
        

        return array('form' => $form,'id'=>$id);
    }
    
    public function editarlocalAction(){
        
      $id = (int) $this->params()->fromQuery('id', 0);
      //var_dump($id);exit;
        if (!$id) {
           return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/local/index/agregarlocal'); 
        }
        //$local = $this->getLocalTable()->getLocal($id);
        try {
            $local = $this->getLocalTable()->getLocal($id);//->toArray();
        }
        catch (\Exception $ex) {
            return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/local'); 
        }
//        
//             $array=array();
//             foreach($local as $result){
//                 $array[]=$result;
//             }
//            
//         foreach($array as $index=>$valor){
//                if(empty($array[$index])){
//                    $array[$index]=1;
//                }
//            }
        
       // var_dump($local);exit;
        $form  = new LocalForm();
       // var_dump($local[0]['in_idpais']);EXIT;
//        $form->get('pais')->setValueOptions(array($local[0]['in_idpais']));
//        $aux[]=$local[0]['in_iddep'];
//        $form->get('departamento')->setValueOptions(array(
//             array(
//                     '0' => 'Apple',
//                     '1' => 'Orange',
//                     '2' => 'Lemon'
//             )));
       $a= array(
            
                     '0' => 'Apple',
                     '1' => 'Orange',
                     '2' => 'Lemon'
             );
       
           $b= array(
               array(
            
                     '0' => 'Apple',
                     '1' => 'Orange',
                     '2' => 'Lemon'
             ));
//           var_dump($form->get('departamento')->setValue(''));exit; 
        $servi=$this->getUbigeoTable()->getServicios();
        $array = array();
        foreach($servi as $y){
            $array[$y['in_id']] = $y['va_nombre'];
                 $form->get('servicio')->setValue($y['in_id']   );
           
        }
        
      // var_dump($local);exit;
        $form->get('servicio')->setValueOptions($array);
   
        
//        $form->get('pais')->setValue($local['in_idpais']);
//        $form->get('departamento')->setValueOptions(array($local['in_iddep']));//setValue($local['in_iddep']);
//        $form->get('provincia')->setValueOptions(array($local['in_idprov']));
//        $form->get('distrito')->setValueOptions(array($local['in_iddis']));
        
        $hiddenpais = new Element\Hidden('h_pais');
        $hiddenpais->setValue($local['in_idpais']);
        $hiddenpais->setAttribute('id', 'h_pais');
        $form->add($hiddenpais);
        
        $hiddendepa = new Element\Hidden('h_departamento');
        $hiddendepa->setValue($local['in_iddep']);
        $hiddendepa->setAttribute('id', 'h_departamento');
        $form->add($hiddendepa);
        
        $hiddenprov = new Element\Hidden('h_provincia');
        $hiddenprov->setValue($local['in_idprov']);
        $hiddenprov->setAttribute('id', 'h_provincia');
        $form->add($hiddenprov);
        
        $hiddendist = new Element\Hidden('h_distrito');
        $hiddendist->setValue($local['in_iddis']);
        $hiddendist->setAttribute('id', 'h_distrito');
        $form->add($hiddendist);
        
       // var_dump($form->get('servicio'));exit;
       // $form->get('servicio')->setValueOptions($b);
//        var_dump($local);exit;
        $form->bind($local);
//        echo 'hello world';exit;
                   
        $form->get('submit')->setAttribute('value', 'MODIFICAR');

        $request = $this->getRequest();
        if ($request->isPost()) {

            //$form->setInputFilter($local->getInputFilter());
            $form->setData($request->getPost());
           
            $servicio=$this->params()->fromPost('servicio');
            
           // if ($form->isValid()) {
                $this->getLocalTable()->guardarLocal($local,$servicio);
                          return $this->redirect()->toUrl($this->
            getRequest()->getBaseUrl().'/local/index/index');
            //}
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
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

        echo Json::encode($ubigeo);
        exit();
    }
    
    public function jsonserviciosAction(){
        $servicios=$this->getUbigeoTable()->getServicios();
       // var_dump($servicios);exit;
        echo Json::encode($servicios);
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
