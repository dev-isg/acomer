<?php
namespace Local\Model;
use Zend\Db\TableGateway\TableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
//namespace Application\Modelo\Entity;
use Zend\Db\Adapter\Platform\PlatformInterface;



use Zend\Db\Adapter\Platform;

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

       $select=$this->getSql()->select()
               ->columns(array('in_iddep','ch_departamento'))
               ->where(array('in_idpais'=>$pais))
                ->group('in_iddep')->order('ch_departamento ASC'); 
            $selectString = $this->getSql()->getSqlStringForSqlObject($select);
            $adapter=$this->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

       return $results->toArray();
       
   }
   
   public function getProvincia($depart){
      
              $select=$this->getSql()->select()
               ->columns(array('in_iddep','in_idprov','ch_provincia'))
               ->where(array('in_iddep'=>$depart))
                ->group('in_idprov')->order('ch_provincia ASC'); 
            $selectString = $this->getSql()->getSqlStringForSqlObject($select);
            $adapter=$this->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            
       return $results->toArray();
       
   }
   
      public function getDistrito($prov,$depart){

        $select=$this->getSql()->select()
               ->columns(array('in_iddep','in_idprov','in_iddis','ch_distrito'))
               ->where(array('in_iddep'=>$depart,'in_idprov'=>$prov))
                ->group('in_iddis')->order('ch_distrito ASC'); 
            $selectString = $this->getSql()->getSqlStringForSqlObject($select);
            $adapter=$this->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            
       return $results->toArray();
       
   }

      
   public function getServicios(){
       $select=$this->getAdapter()->query("SELECT * FROM ta_servicio_local")->execute();
//            $select=$this->getAdapter()->select()->from('ta_servicio_local');
//            $selectString = $this->getSql()->getSqlStringForSqlObject($select);
//            $adapter=$this->getAdapter();
//            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//            
            
                 $returnArray=array();
        foreach ($select as $result) {
            $returnArray[] = $result;
        }
//        var_dump($returnArray);exit;
        
       return $returnArray;//->toArray();
       
   }
    

//                $sql = new Sql($adapter);
//        $select = $sql->select()
//                 ->from('ta_rol');
//      $selectString = $sql->getSqlStringForSqlObject($select);
//            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//
//       $row = $results->toArray();

    
    
    
    
    

    
}