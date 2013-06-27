<?php
namespace Platos\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;

use Platos\Model\Platos;




class PlatosTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    /*
     * 2 maneras distina de hacer joins
     */
    public function fetchAll(){

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('in_id','va_nombre','va_precio','en_estado','en_destaque'));
        $sqlSelect->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_va_nombre'=>'va_nombre'),'left');//, 'left'
        $sqlSelect->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array('ta_local_in_id'), 'left');
         $sqlSelect->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left');
         $sqlSelect->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurante_va_nombre'=>'va_nombre'), 'left');
        $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($sqlSelect);
          //var_dump($selectString);exit;
          /*
           * con este es = q el siguiente pero en este no muestra los campos q no quieres imprimir
           */
//            $adapter=$this->tableGateway->getAdapter();
//            $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
           /*
            * muestra todos los campos, los q no especificaste a imprimer los imprime como null
            */
        $resultSet = $this->tableGateway->select($sqlSelect);

        
//        return $resultSet;
        
        
//        
//            $sqlSelect = $this->tableGateway->getSql()->select();
//                $sqlSelect->columns(array('in_id', 'va_nombre', 'de_precio','en_estado'));
//                $sqlSelect->join(array('tp'=>'ta_tipo_plato'), 'tp.in_id = ta_plato.in_id', array('va_nombre'), 'left');
////                ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = in_id', array('ta_local_in_id'), 'left')
////                ->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left')
////                ->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('va_nombre'), 'left');
//////                ->where(array('in_id'=>1));
     
//         $resp=$this->tableGateway->select($sqlSelect);
        
        
            $array=array();
             foreach($resultSet as $result){
                 $array[]=$result;
             }
            var_dump($array);exit;
        return $resultSet;//$this->tableGateway->select($sqlSelect);
        // return $resultSet;   

    }
    /*
     * otra manera
     */
    
    public function fetchAllx(){
        
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('p' => 'ta_plato'))
                ->join(array('tp' => 'ta_tipo_plato'), 'p.in_id=tp.in_id ', array())//,array('va_nombre_rol'))
                ->where(array('p.in_id=tp.in_id '));           
        $selectString = $sql->getSqlStringForSqlObject($select);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

              $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('f' => 'ta_restaurante'))
                ->join(array('b' => 'ta_tipo_comida'), 'f.Ta_tipo_comida_in_id=b.in_id', array('va_nombre_tipo'))//,array('va_nombre_rol'))
                ->where(array('f.Ta_tipo_comida_in_id=b.in_id'));           
        $selectString = $sql->getSqlStringForSqlObject($select);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet;
        
                        $array=array();
             foreach($resultSet as $result){
                 $array[]=$result;
             }
            var_dump($array);exit;
    }
    
}

?>


