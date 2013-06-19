<?php
namespace Local\Model;
use Zend\Db\TableGateway\TableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\AbstractResultSet;
//namespace Application\Modelo\Entity;


class Ubigeo extends TableGateway{
    protected $tableGateway;
    
    public function __construct(Adapter $adapter = null, $databaseSchema = null, 
    ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('ta_ubigeo', $adapter, $databaseSchema, 
            $selectResultPrototype);
    }
    
    public function getUbigeo(){
            $datos = $this->select();
            return $datos->toArray();     
    }
   public function getDepartamento($pais=1){
       $rowset=$this->select()->from($this, array('DISTINCT in_iddep'))
    ->where('in_idpais = ?', $pais);
      // var_dump($rowset->toArray());exit;
       return $rowset->toArray();
       
   }
   
   public function getProvincia($depart){
       $rowset=$this->select()->from($this, array('DISTINCT in_idprov'))
    ->where('in_iddep = ?', $depart);
      // var_dump($rowset->toArray());exit;
       return $rowset->toArray();
       
   }
   
      public function getDistrito($prov,$depart){
       $rowset=$this->select()->from($this, array('DISTINCT in_iddis'))
    ->where(array('in_idprov'=>$prov,'in_iddep'=>$depart));
      // var_dump($rowset->toArray());exit;
       return $rowset->toArray();
       
   }
    
    public function hcer(){
//                $sql = new Sql($adapter);
//        $select = $sql->select()
//                 ->from('ta_rol');
//      $selectString = $sql->getSqlStringForSqlObject($select);
//            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//
//       $row = $results->toArray();
    }
    
    
    
    
    

    
}