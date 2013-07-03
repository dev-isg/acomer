<?php
namespace Usuario\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Platform;
use Usuario\Model\Comentarios;



class ComentariosTable
{
    protected $tableGateway;
    

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
      
    }
    
    public function fetchAll()
    {
     
        $select = $this->tableGateway->getSql()->select()
             ->join(array('r'=>'ta_plato'),'ta_plato_in_id=r.in_id',array('va_nombre'))
             ->join(array('u'=>'ta_cliente'),'ta_cliente_in_id=u.in_id',array('va_nombre_cliente','va_email'))
             ->join(array('f'=>'ta_puntaje'),'ta_comentario.ta_puntaje_in_id=f.in_id',array('va_valor'));
             // ->where('(r.in_id LIKE "%'.$consulta.'%") OR (r.va_nombre LIKE "%'.$consulta.'%") OR (u.ch_distrito LIKE "%'.$consulta.'%")');//OR (ta_restaurante_in_id LIKE "%'.$consulta.'%") OR (ta_ubigeo_in_id LIKE "%'.$consulta.'%")
            
            $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
            $adapter=$this->tableGateway->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

      return $results;
    }
    /*
     * agregar y registrar el comentario posiblemente se mueva
     */
    public function agregarComentario($coment){
        
           $cliente=array(
                    'va_nombre_cliente'=>$coment['va_nombre'],
                    'va_email'=>$coment['va_email'],         
                );
           $insert = $this->tableGateway->getSql()->insert()->into('ta_cliente')
                    ->values($cliente);
            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insert);
            
            $statement->execute();    
//            $selectString2 = $this->tableGateway->getSql()->getSqlStringForSqlObject($insert);
//            $adapter=$this->tableGateway->getAdapter();
//            $result = $adapter->query($selectString2, $adapter::QUERY_MODE_EXECUTE);
            $idcliente=$this->tableGateway->getAdapter()->getDriver()->getLastGeneratedValue();//$this->tableGateway->getLastInsertValue();
//          var_dump($idcliente);Exit;

//           date_default_timezone_set('UTC');
            $comentario = array(
            'tx_descripcion' => $coment['tx_descripcion'],
            'Ta_plato_in_id' => $coment['Ta_plato_in_id'],
            'Ta_cliente_in_id' => $idcliente,//$coment->Ta_cliente_in_id,
            'Ta_puntaje_in_id' => $coment['Ta_puntaje_in_id'],
                'Ta_plato_in_id'=>35,
                'da_fecha'=>date('c')//date('Y-m-dTH:i:s.uZ')//'2013-12-12'
                );
           
         $id = (int) $coment['in_id'];
            if ($id == 0) {            
           $insertcoment= $this->tableGateway->getSql()->insert()->into('ta_comentario')
                    ->values($comentario);
            $statement2 = $this->tableGateway->getSql()->prepareStatementForSqlObject($insertcoment);
            $statement2->execute();   

             }
        
    }


     public function estadoComentario($id,$estado){
                $data = array(
                    'en_estado' => $estado,
                 );
         $this->tableGateway->update($data, array('in_id' => $id));

    }
    
     public function buscarComentario($datos,$estado,$puntaje){
        $adapter=$this->tableGateway->getAdapter();
           $sql = new Sql($adapter);
           if($datos=='' and $puntaje== ''){
             $select = $sql->select()
            ->from(array('f' => 'ta_comentario')) 
            ->join(array('r'=>'ta_plato'),'f.ta_plato_in_id=r.in_id',array('va_nombre'))
            ->join(array('u'=>'ta_cliente'),'f.ta_cliente_in_id=u.in_id',array('va_nombre_cliente','va_email'))
            ->join(array('m'=>'ta_puntaje'),'f.ta_puntaje_in_id=m.in_id',array('va_valor'))
            ->where(array('f.en_estado'=>$estado));
           }
         if($estado==''and $puntaje== ''){
             $select = $sql->select()
            ->from(array('f' => 'ta_comentario')) 
            ->join(array('r'=>'ta_plato'),'f.ta_plato_in_id=r.in_id',array('va_nombre'))
            ->join(array('u'=>'ta_cliente'),'f.ta_cliente_in_id=u.in_id',array('va_nombre_cliente','va_email'))
            ->join(array('m'=>'ta_puntaje'),'f.ta_puntaje_in_id=m.in_id',array('va_valor'))
            ->where(array('r.va_nombre'=>$datos));
           }
           if($estado==''and $datos== ''){
             $select = $sql->select()
            ->from(array('f' => 'ta_comentario')) 
            ->join(array('r'=>'ta_plato'),'f.ta_plato_in_id=r.in_id',array('va_nombre'))
            ->join(array('u'=>'ta_cliente'),'f.ta_cliente_in_id=u.in_id',array('va_nombre_cliente','va_email'))
            ->join(array('m'=>'ta_puntaje'),'f.ta_puntaje_in_id=m.in_id',array('va_valor'))
            ->where(array('f.ta_puntaje_in_id'=>$puntaje));
           }
           if($datos=='' and $puntaje != '' and $estado != '' ){
             $select = $sql->select()
           ->from(array('f' => 'ta_comentario')) 
            ->join(array('r'=>'ta_plato'),'f.ta_plato_in_id=r.in_id',array('va_nombre'))
            ->join(array('u'=>'ta_cliente'),'f.ta_cliente_in_id=u.in_id',array('va_nombre_cliente','va_email'))
            ->join(array('m'=>'ta_puntaje'),'f.ta_puntaje_in_id=m.in_id',array('va_valor'))
            ->where(array('f.en_estado'=>$estado,'f.ta_puntaje_in_id'=>$puntaje));
           }
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $rowset = $results;
         if (!$rowset) {
            throw new \Exception("No hay data");
            }
            return $rowset;
          }
          
         public function estadoRestaurante($id,$estado){
                $data = array(
                    'en_estado' => $estado,
                 );
         $this->tableGateway->update($data, array('in_id' => $id));
         }
         
         
         public function deleteComentario($id)
         {       
        $this->tableGateway->delete(array('in_id' => $id));
         }
    
}