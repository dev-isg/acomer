<?php
namespace Local\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

use Zend\Db\Adapter\Platform;

class LocalTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll(){
        $resultSet = $this->tableGateway->select();

        return $resultSet; 
    }
    
    public function listar($consulta=null,$id){
//            $rowset = $this->tableGateway->select(function (Select $select) {           
//            $select->join(array('r'=>'ta_restaurante'),'ta_restaurante_in_id=r.in_id')
//                   ->join(array('u'=>'ta_ubigeo'),'ta_ubigeo_in_id=u.in_id',array('ch_pais','ch_departamento','ch_provincia','ch_distrito'))    
//                   ->where('ta_restaurante_in_id=r.in_id');
//             });        
              $select = $this->tableGateway->getSql()->select()
             ->join(array('r'=>'ta_restaurante'),'ta_restaurante_in_id=r.in_id',array('va_nombre'))
             ->join(array('u'=>'ta_ubigeo'),'ta_ubigeo_in_id=u.in_id',array('ch_pais','ch_departamento','ch_provincia','ch_distrito'))
              ->where('(r.va_nombre LIKE "%'.$consulta.'%") OR (u.ch_distrito LIKE "%'.$consulta.'%") OR (ta_restaurante_in_id LIKE "%'.$consulta.'%") OR (ta_ubigeo_in_id LIKE "%'.$consulta.'%")')
  ->where(array('r.in_id'=>$id));//('ta_restaurante_in_id='.'1');//r.in_id
              $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
            $adapter=$this->tableGateway->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
 
//             $array=array();
//             foreach($results as $result){
//                 $array[]=$result;
//             }
//             
//    var_dump($array);exit;
      return $results;
      
    }
    
    public function eliminarLocal($id){
        
        $this->tableGateway->delete(array('in_id' => $id));
 
    }
    
    public function guardarLocal(Local $local){
                $data = array(
           'va_nombre'         => $restaurante->va_nombre,
           'va_razon_social'   => $restaurante->va_razon_social,
           'va_web'            => $restaurante->va_web,
           'va_imagen'         => $restaurante->va_imagen,
           'va_ruc'            => $restaurante->va_ruc,  
           'Ta_tipo_comida_in_id'  => $restaurante->Ta_tipo_comida_in_id  
        );

        $id = (int)$restaurante->in_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getRestaurante($id)) {
                $this->tableGateway->update($data, array('in_id' => $id));
            } else {
                throw new \Exception('no existe el usuario');
            }
        }
    }
}
