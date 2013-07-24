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
use Zend\Http\Response;

use PHPExcel; 
use PHPExcel\Reader\Excel5; 


use Zend\Db\TableGateway\TableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;
class IndexController extends AbstractActionController
{
     protected $localTable;
     protected $ubigeoTable;
    
    public function indexAction()
    {
        
        $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/usuario/index/login');
        }
//        $this->layout('layout/layout22');
        //$this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        //$u=new Ubigeo($this->dbAdapter);
       // var_dump($u->getUbigeo());exit;
        
       $filtrar = $this->params()->fromPost('submit');
        $id = (int) $this->params()->fromRoute('in_id', 0);

//       if(!empty($id)){
//       if(isset($filtrar)){
//           $consulta=$this->params()->fromPost('texto');
//               $lista =  $this->getLocalTable()->listar($consulta);         
//           }else{
//               $lista =  $this->getLocalTable()->listar($id);//$id
//           }
//       }else{
//           if(isset($filtrar)){
//           $consulta=$this->params()->fromPost('texto');
//               $lista =  $this->getLocalTable()->listar($consulta);         
//           }else{
//               $lista =  $this->getLocalTable()->listar();//$id
//           }
//           
//       }
       
       $lista =  $this->getLocalTable()->listar($id);
       $request = $this->getRequest();
        if ($request->isPost()) {
             $consulta=$this->params()->fromPost('texto');
            $lista =  $this->getLocalTable()->listar($id,$consulta);   

        }
        
         $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($lista));
         $page=(int)$this->params()->fromQuery('page', 1);
         $paginator->setCurrentPageNumber($page);
         $paginator->setItemCountPerPage(10);
        return new ViewModel(array(
                  'locales' =>$paginator,
                  
                  'in_id'=>$id
                ));
    }

    
    public function agregarlocalAction() {
       $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }
        $form = new LocalForm();
        $id = $this->params()->fromQuery('id');


        $form->get('submit')->setValue('INSERTAR');
        $request = $this->getRequest();

        $servi = $this->getUbigeoTable()->getServicios();

        $array = array();
        foreach ($servi as $y) {
            $array[$y['in_id']] = $y['va_nombre'];
        }
//        $form->get('servicio')->setOptions(array('required'=>false));
        $form->get('servicio')->setValueOptions($array);

        if ($request->isPost()) {
            $idretau=$this->params()->fromPost('ta_restaurante_in_id',0);
            
            $servicio = $this->params()->fromPost('servicio', 0);
            $local = new Local();
            
            $form->setInputFilter($local->getInputFilter());
            $form->setData($request->getPost());
//            var_dump($form->isValid());exit;
            if ($form->isValid()) {
                
                $local->exchangeArray($form->getData());
                $this->getLocalTable()->guardarLocal($local, $servicio);
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/local/index/index/'.$idretau);
            } else {
                // var_dump($request->getPost());exit;
//                $local->exchangeArray($form->getData());
//                $this->getLocalTable()->guardarLocal($local, $servicio);
//                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/local/index/index/'.$idretau);
            }
        }


        return array('form' => $form, 'id' => $id);
    }
    
 
    
    public function editarlocalAction() {
       $auth = new \Zend\Authentication\AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/usuario/index/login');
        }
        $id = (int) $this->params()->fromQuery('id', 0);
        $idrest=(int) $this->params()->fromRoute('in_id', 0);
      
        if (!$id) {
            return $this->redirect()->toUrl($this->
                                    getRequest()->getBaseUrl() . '/local/index/agregarlocal');
        }

        try {
           
            $local = $this->getLocalTable()->getLocal($id); //->toArray();

        } catch (\Exception $ex) {

            return $this->redirect()->toUrl($this->
                                    getRequest()->getBaseUrl() . '/local');
        }

        $form = new LocalForm();

        $servi = $this->getUbigeoTable()->getServicios();
        $array = array();
        foreach ($servi as $y) {
            $array[$y['in_id']] = $y['va_nombre'];
        }

        $form->get('servicio')->setValueOptions($array);

        $form->get('pais')->setValue($local['in_idpais']);

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
        $form->bind($local);
        $form->get('submit')->setAttribute('value', 'MODIFICAR');

        $request = $this->getRequest();
        
       //$this->getLocalTable()->editarLocal($id,$data);
        
        if ($request->isPost()) {
            
            
            $aux=$this->getRequest()->getPost()->toArray();
              $this->getLocalTable()->editarLocal($aux,$id);
             return $this->redirect()->toUrl($this->
                                        getRequest()->getBaseUrl() . '/local/index/index/'.$idrest);

//           $form->setInputFilter($local->getInputFilter());
//            $form->setData($request->getPost());
//
//            $servicio = $this->params()->fromPost('servicio');
//
//            if ($form->isValid()) {
//
//                $this->getLocalTable()->editarLocal($id,$local);//guardarLocal($local, $servicio);
//
//                return $this->redirect()->toUrl($this->
//                                        getRequest()->getBaseUrl() . '/local/index/index');
//            } else {
//                //$this->getLocalTable()->guardarLocal($local, $servicio);
//                echo 'no validado';
//                exit;
//            }
        }

        return array(
            'id' => $id,
            'form' => $form,
            'id_re'=>$idrest,
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
        //$servicios=$this->getUbigeoTable()->getServicios();
         $id = (int) $this->params()->fromQuery('id',0);
        $servicios=$this->getLocalTable()->getServiciosId($id);
//        var_dump($servicios);exit;
        echo Json::encode($servicios);
        exit();
    }
    
public function LoginAction() 
{ 

//    use Classes\PHPExcel; 
//use Classes\PHPExcel\Reader\Excel5; 
    error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
 require './vendor/Classes/PHPExcel.php';
 include './vendor/Classes/PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
$objPHPExcel = new \PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A4', 'Miscellaneous glyphs')
            ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8');
header('Content-Disposition: attachment;filename="01simple.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('01simple.xlsx');//save('php://output');



//https://gist.github.com/nebiros/288725
//http://zend-framework-community.634137.n4.nabble.com/intergrate-PHPWord-and-PHPExcel-in-ZF2-td4659566.html
   exit;
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
