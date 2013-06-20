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
    
    public function listar($consulta = null){//,$id
//            $rowset = $this->tableGateway->select(function (Select $select) {           
//            $select->join(array('r'=>'ta_restaurante'),'ta_restaurante_in_id=r.in_id')
//                   ->join(array('u'=>'ta_ubigeo'),'ta_ubigeo_in_id=u.in_id',array('ch_pais','ch_departamento','ch_provincia','ch_distrito'))    
//                   ->where('ta_restaurante_in_id=r.in_id');
//             });        
              $select = $this->tableGateway->getSql()->select()
             ->join(array('r'=>'ta_restaurante'),'ta_restaurante_in_id=r.in_id',array('va_nombre'))
             ->join(array('u'=>'ta_ubigeo'),'ta_ubigeo_in_id=u.in_id',array('ch_pais','ch_departamento','ch_provincia','ch_distrito'))
              ->where('(r.in_id LIKE "%'.$consulta.'%") OR (r.va_nombre LIKE "%'.$consulta.'%") OR (u.ch_distrito LIKE "%'.$consulta.'%")');//OR (ta_restaurante_in_id LIKE "%'.$consulta.'%") OR (ta_ubigeo_in_id LIKE "%'.$consulta.'%")
              //->where(array('r.in_id'=>$id));//('ta_restaurante_in_id='.'1');//r.in_id
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
    
    public function guardarLocal(Local $local, $servicio){
         var_dump($servicio);exit;
          $data = array(
           'va_telefono'         => $local->va_telefono,
           'va_horario'   => $local->va_horario,
           'de_latitud'            => $local->de_latitud,
           'de_longitud'         => $local->de_longitud,
           'va_rango_precio'            => $local->va_rango_precio,  
           'va_horario_opcional'  => $local->va_horario_opcional,
            'va_direccion' => $local->va_direccion,
           'ta_restaurante_in_id' => $local->ta_restaurante_in_id,
            'ta_ubigeo_in_id' => $local->ta_ubigeo_in_id
                    
        );
          

          
          foreach($data as $index=>$valor){
                if(empty($data[$index])){
                    $data[$index]=1;
                    //if($index=='cantidad')$datosAviso['cantidad']=1;
                }
            }
            
          //print_r($data);exit;
        $id = (int)$local->in_id;
        //var_dump($id);exit;
        if ($id == 0) {
            
            
            $inservicio = $this->tableGateway->getSql()->insert()->into('ta_servicio_local')
                    ->values(array('va_nombre'=>$servicio));
            $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($inservicio);
            $adapter=$this->tableGateway->getAdapter();
            $resultserv = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            
            $idserv=$this->tableGateway->getLastInsertValue();
            
            $this->tableGateway->insert($data);
            $idlocal=$this->tableGateway->getLastInsertValue();
            
            $insert = $this->tableGateway->getSql()->insert()->into('ta_local_has_ta_servicio_local')
                    ->values(array('ta_local_in_id'=>$idlocal,'ta_servicio_local_in_id'=>$idserv));
            $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($insert);
            $adapter=$this->tableGateway->getAdapter();
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            
            
            
            
            
        } else {
//            if ($this->getRestaurante($id)) {
//                $this->tableGateway->update($data, array('in_id' => $id));
//            } else {
//                throw new \Exception('no existe el usuario');
//            }
        }
    }
}
