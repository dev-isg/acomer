<?php
namespace Restaurante\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

use Zend\Db\Adapter\Platform;



class RestauranteTable
{
   

     protected $tableGateway;
    

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
      
    }
   
  
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function buscar()
    {

  
     
//    $rowset = $this->tableGateway->select(function (Select $select) {           
//            $select->where('(va_nombre LIKE "%'.'restaurante tres tenedores'.'%") OR (va_nombre LIKE "%'.'tres cuchillos'.'%")');     
//      });
      $var=2;
      $select = $this->tableGateway->getSql()->select()
        ->join('ta_tipo_comida', 'ta_tipo_comida_in_id=ta_tipo_comida.in_id')//,array('ta_tipo_comida_in_id'=>'va_nombre_tipo'))
        ->where('ta_restaurante.ta_tipo_comida_in_id='.$var);
     //echo $select->getSqlString();exit;
   //     $resultSet = $this->tableGateway->selectWith($select);
            $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
            $adapter=$this->tableGateway->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            
        $returnArray=array();
         foreach ($results as $result) {
            $returnArray[] = $result;
        }
   
        var_dump($returnArray);exit; 
   
        return $results;
    }
    
    
}