<?php
namespace Restaurante\Model;



use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Platform;
use Zend\Http\Request;


class RestauranteTable
{
   
     protected $tableGateway;
     public $dbAdapter;
    

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }
   

    public function fetchAll()
    {

      $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('f' => 'ta_restaurante'))
                ->join(array('b' => 'ta_tipo_comida'), 'f.ta_tipo_comida_in_id=b.in_id', array('va_nombre_tipo'))//,array('va_nombre_rol'))
              ->where(array('f.ta_tipo_comida_in_id=b.in_id'))
               ->order('in_id DESC');
        
        $selectString = $sql->getSqlStringForSqlObject($select);
    
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $resultSet->buffer();
        return $resultSet;
    }
     public function getRestaurante($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('in_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    public function getRestauranteRuc($ruc)
    {
      
        $rowset = $this->tableGateway->select(array('va_ruc' => $ruc));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
  public function guardarRestaurante(Restaurante $restaurante, $comida ,$imagen)
    {
        $data = array(
           'va_nombre'         => $restaurante->va_nombre,
           'va_razon_social'   => $restaurante->va_razon_social,
           'va_web'            => $restaurante->va_web,
           'va_imagen'         => $imagen,
           'va_ruc'            => $restaurante->va_ruc,
           'Ta_tipo_comida_in_id'  => $restaurante->Ta_tipo_comida_in_id );
        $id = (int)$restaurante->in_id;
        
        if ($id == 0) 
          {
                   //     var_dump($comida);exit;
                $this->tableGateway->insert($data); 
                
                $idRestaurante=$this->tableGateway->getLastInsertValue();
          
                    if($comida != '')
                    { 
                    foreach($comida as $key=>$value)
                      {             
                        $insert = $this->tableGateway->getSql()->insert()->into('ta_restaurante_has_ta_medio_pago')
                        ->values(array('Ta_restaurante_in_id'=>$idRestaurante,'Ta_medio_pago_in_id'=>$value));
                        $selectString2 = $this->tableGateway->getSql()->getSqlStringForSqlObject($insert);
                        $adapter=$this->tableGateway->getAdapter();
                        $result = $adapter->query($selectString2, $adapter::QUERY_MODE_EXECUTE);
                      }
                    }            
           }
        else 
             {
                if ($this->getRestaurante($id)) 
                   {
                    $this->tableGateway->update($data, array('in_id' => $id));
                     if($comida != '')
                    {   
                        $borrar = $this->tableGateway->getSql()->delete()->from('ta_restaurante_has_ta_medio_pago')
                                ->where(array('Ta_restaurante_in_id'=>$id));
                        $selectStri = $this->tableGateway->getSql()->getSqlStringForSqlObject($borrar);
                        $adapter=$this->tableGateway->getAdapter();
                        $result = $adapter->query($selectStri, $adapter::QUERY_MODE_EXECUTE); 
                        foreach($comida as $key=>$value)
                          {               
                               $insertar = $this->tableGateway->getSql()->insert()->into('ta_restaurante_has_ta_medio_pago')
                                       ->values(array('Ta_restaurante_in_id'=>$id,'Ta_medio_pago_in_id'=>$value));
                               $selectString3 = $this->tableGateway->getSql()->getSqlStringForSqlObject($insertar);
                               $adapter=$this->tableGateway->getAdapter();
                               $result = $adapter->query($selectString3, $adapter::QUERY_MODE_EXECUTE);
                         }
                   }
               }
               else 
                   {
                    throw new \Exception('error al crear el restaurante');
                   }
           }
    }

     public function buscarRestaurante($datos,$comida,$estado){
        $adapter=$this->tableGateway->getAdapter();
           $sql = new Sql($adapter);
        
           if($comida=='' and $estado == ''){
          
             $select = $sql->select()
            ->from(array('f' => 'ta_restaurante')) 
            ->join(array('b' => 'ta_tipo_comida'),'f.ta_tipo_comida_in_id = b.in_id',array('va_nombre_tipo'))
           ->where(array('f.va_nombre'=>$datos));
           }

           if($datos=='' and $estado == ''){

             $select = $sql->select()
            ->from(array('f' => 'ta_restaurante')) 
            ->join(array('b' => 'ta_tipo_comida'),'f.ta_tipo_comida_in_id = b.in_id',array('va_nombre_tipo'))
           ->where(array('f.ta_tipo_comida_in_id'=>$comida));
           }
       else if($datos=='' and $comida == ''){
             
             $select = $sql->select()
            ->from(array('f' => 'ta_restaurante')) 
            ->join(array('b' => 'ta_tipo_comida'),'f.ta_tipo_comida_in_id = b.in_id',array('va_nombre_tipo'))
           ->where(array('f.en_estado'=>$estado));
           }
        else if($datos=='' and $comida != '' and $estado != '' ){
           
             $select = $sql->select()
            ->from(array('f' => 'ta_restaurante')) 
            ->join(array('b' => 'ta_tipo_comida'),'f.ta_tipo_comida_in_id = b.in_id',array('va_nombre_tipo'))
            ->where(array('f.en_estado'=>$estado))
            ->where(array('f.ta_tipo_comida_in_id'=>$comida,'f.en_estado'=>$estado));
           }
           else if($datos!='' and $comida != '' and $estado != '' ){
            $select = $sql->select()
            ->from(array('f' => 'ta_restaurante')) 
            ->join(array('b' => 'ta_tipo_comida'),'f.ta_tipo_comida_in_id = b.in_id',array('va_nombre_tipo'))
//            ->where(array('f.en_estado'=>$estado))
//             ->where(array('f.Ta_tipo_comida_in_id'=>$comida,'f.en_estado'=>$estado))->where->and->like('f.va_nombre', '%'.$datos.'%');
            ->where(array('f.ta_tipo_comida_in_id'=>$comida,'f.en_estado'=>$estado,'f.va_nombre'=>$datos));
//            ->where->like('f.va_nombre', '%'.$datos);
           
           }
            $selectString = $sql->getSqlStringForSqlObject($select);
            
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $rowset = $results;
         if (!$rowset) {
            throw new \Exception("No hay data");
        }
        $rowset->buffer();
        return $rowset;
    }


         public function estadoRestaurante($id,$estado){
                $data = array(
                    'en_estado' => $estado,
                 );
                // var_dump($estado);exit;
         $this->tableGateway->update($data, array('in_id' => $id));
         
         

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
    
   public function rolA($adapter)
    { $sql = new Sql($adapter);
        $select = $sql->select()
                 ->from('ta_cliente');
      $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

       $row = $results->toArray();
        if (!$row) {
            throw new \Exception("No existe registro con el parametro $id");
        }
        return $row;  
    }

    
    public function comidas(){
        
        $datos=$this->tableGateway->getAdapter()->query("SELECT * FROM ta_tipo_comida")->execute();
                $returnArray=array();
        foreach ($datos as $result) {
            $returnArray[] = $result;
        }
        
        return  $returnArray;
        
    }
    
      public function medio($id){
        
        $datos=$this->tableGateway->getAdapter()->query("SELECT `f`.*, `b`.`va_nombre` AS `va_nombre` FROM `ta_restaurante_has_ta_medio_pago` AS `f` 
INNER JOIN `Ta_medio_pago` AS `b` ON `f`.`Ta_medio_pago_in_id` = `b`.`in_id` WHERE `f`.`Ta_restaurante_in_id` = $id ")->execute();
                $returnArray=array();
        foreach ($datos as $result) {
            $returnArray[] = $result;
        }
        return  $returnArray;
        
    }
    

}