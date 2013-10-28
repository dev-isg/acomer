<?php

namespace Usuario\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
//use Usuario\Model\Usuario;          // <-- Add this import
use Usuario\Form\ClienteForm;       // <-- Add this import
use Usuario\Model\UsuarioTable;
use Zend\Db\Sql\Sql;
use SanAuth\Controller\AuthController; 
use Zend\Db\Adapter\Adapter;
use PHPExcel;
use PHPExcel\Reader\Excel5;



class ClienteController extends AbstractActionController {

    protected $clientesTable;

    public function clientesAction() {
        $consulta = $this->params()->fromPost('texto');
        $clientes = $this->getTableClientes()->getCliente();
        
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($clientes));
         $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
         $paginator->setItemCountPerPage(10);

        
        if ($this->getRequest()->isPost()) {
            
            $clientes = $this->getTableClientes()->getCliente($consulta);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\Iterator($clientes));
         $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
         $paginator->setItemCountPerPage(10);

        }
                return new ViewModel(array(
                    'clientes' => $paginator,
                ));

                
    }
    public function agregarclientesAction(){
        $form = new ClienteForm();
         $form->get('submit')->setValue('Agregar');
        $request = $this->getRequest();
        
        if ($request->isPost()) {
//            $album = new Album();
//            $form->setInputFilter($album->getInputFilter());
//            $form->setData($request->getPost());
            $datos=$this->getRequest()->getPost()->toArray();
//            var_dump($datos);exit;
            $form->setData($datos);
//            var_dump($form->isValid($datos));
            if ($form->isValid($datos)) {
//                $album->exchangeArray($form->getData());
               
                $this->getTableClientes()->addCliente($datos); 
           
//                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/platos'); 
            }
        }
        return array('form' => $form);
        
   
    }

    public function excelAction() {
//       $view =new ViewModel();
//       $view->setTerminal(true);
       
//        $clientes = $this->getTableClientes()->getCliente();
//       $lista=$clientes->toArray(); 

        if (PHP_SAPI == 'cli')
            die('This example should only be run from a Web Browser');
        require './vendor/Classes/PHPExcel.php';
        include './vendor/Classes/PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();

// Set document properties
//        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
//                ->setLastModifiedBy("Maarten Balliauw")
//                ->setTitle("Office 2007 XLSX Test Document")
//                ->setSubject("Office 2007 XLSX Test Document")
//                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
//                ->setKeywords("office 2007 openxml php")
//                ->setCategory("Test result file");
        
// Add some data
//        $cont=1;
//        for($i=0;$i<count($lista);$i++,$cont++){
//                    $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A'.$cont,$lista[$i]['in_id'])
//                ->setCellValue('B'.$cont,$lista[$i]['va_nombre_cliente'])
//                ->setCellValue('C'.$cont,$lista[$i]['va_email']);
//        }
     
        $objPHPExcel->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=\"01simple.xlsx\"");
header("Cache-Control: max-age=0");


        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('01simple.xlsx'); 
//        $objWriter->save('php://output');
        echo file_get_contents('01simple.xlsx');
        
        exit;
        exit;
         
    }
    
      public function exportarexcelAction() {
        $clientes = $this->getTableClientes()->getCliente();
        $view =new ViewModel();
        $view->setTerminal(true);
        $view->setVariables(array('clientes' => $clientes));
        return $view;
   
//               return new ViewModel(array(
//                    'clientes' => $clientes,
//                ));
                
      }

    public function getTableClientes() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\Cliente');
        }
        return $this->clientesTable;
    }

}