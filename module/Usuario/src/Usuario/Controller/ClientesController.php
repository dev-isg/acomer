<?php

namespace Usuario\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Http\Request;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Usuario\Model\Usuario;          // <-- Add this import
use Usuario\Form\UsuarioForm;       // <-- Add this import
use Usuario\Model\UsuarioTable;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use PHPExcel;
use PHPExcel\Reader\Excel5;

class ClientesController extends AbstractActionController {

    protected $clientesTable;

    public function clientesAction() {
        $consulta = $this->params()->fromPost('texto');
        if ($this->getRequest()->isPost()) {
            $clientes = $this->getTableClientes()->getCliente($consulta);
        } else {
            $clientes = $this->getTableClientes()->getCliente();
        }

        return new ViewModel(array(
                    'clientes' => $clientes,
                ));
    }

    public function excelAction() {
        $clientes = $this->getTableClientes()->getCliente();
       $lista=$clientes->toArray();
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
//        var_dump($lista[0]['in_id']);
        

        $cont=1;
        for($i=0;$i<count($lista);$i++,$cont++){
//        var_dump($lista[$i]['in_id']);
                               $objPHPExcel->setActiveSheetIndex(0)
                  ->setCellValue('A'.$cont,$lista[$i]['in_id'])
                ->setCellValue('B'.$cont,$lista[$i]['va_nombre'])
                ->setCellValue('C'.$cont,$lista[$i]['va_email']);
        }
     
       
               



//                    ->setCellValue('B1',$clientes[0]["in_id"])
//                  ->setCellValue('C1',$clientes[0]["in_id"]);
    
//                ->setCellValue('A1', 'Hello')
//                ->setCellValue('B2', 'world!')
//                ->setCellValue('C1', 'Hello')
//                ->setCellValue('D2', 'world!');

// Miscellaneous glyphs, UTF-8
//        $objPHPExcel->setActiveSheetIndex(0)
//                ->setCellValue('A4', 'Miscellaneous glyphs')
//                ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

// Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('01simple.xlsx'); //save('php://output');
//https://gist.github.com/nebiros/288725
//http://zend-framework-community.634137.n4.nabble.com/intergrate-PHPWord-and-PHPExcel-in-ZF2-td4659566.html
        exit;
    }

    public function getTableClientes() {
        if (!$this->clientesTable) {
            $sm = $this->getServiceLocator();
            $this->clientesTable = $sm->get('Usuario\Model\Cliente');
        }
        return $this->clientesTable;
    }

}