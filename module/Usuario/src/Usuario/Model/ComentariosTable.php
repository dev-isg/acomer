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
use Platos\Model\PlatosTable;



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
            $select->order('ta_comentario.in_id DESC');
            $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
            $adapter=$this->tableGateway->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    $res=$results->buffer();
      return $res;
    }
    /*
     * agregar y registrar el comentario posiblemente se mueva
     */
  
       
     public function cromSolar($id,$caso=null) {
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $selecttot = $sql->select()
                ->from('ta_plato')
                ->join(array('c' => 'ta_comentario'), 'c.ta_plato_in_id=ta_plato.in_id', array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(c.in_id)')), 'left')
                    ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_nombre' => 'va_nombre'), 'left')
                    ->join(array('pl' => 'ta_plato_has_ta_local'), 'pl.Ta_plato_in_id = ta_plato.in_id', array(), 'left')
                    ->join(array('tl' => 'ta_local'), 'tl.in_id = pl.Ta_local_in_id', array('latitud'=>'de_latitud', 'longitud'=>'de_longitud', 'direccion'=>'va_direccion','telefono'=>'va_telefono'), 'left')
                    ->join(array('tr' => 'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurant_nombre' => 'va_nombre', 'restaurant_estado' => 'en_estado'), 'left')
                    ->join(array('tc' => 'ta_tipo_comida'), 'tc.in_id = tr.Ta_tipo_comida_in_id', array('nombre_tipo_comida' => 'va_nombre_tipo'), 'left')                                      
                    ->join(array('tu' => 'ta_ubigeo'), 'tu.in_id = tl.ta_ubigeo_in_id', array('distrito' => 'ch_distrito','departamento' => 'ch_departamento'), 'left')
                    ->where(array('ta_plato.in_id' => $id));
                 $selecttot->group('ta_plato.in_id');
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $plato = $results->toArray();
         $selectto = $sql->select()
                ->from('ta_plato')  
                    ->join(array('tpt' => 'ta_plato_has_ta_tag'), 'tpt.Ta_plato_in_id = ta_plato.in_id', array('tag_id'=>'ta_tag_in_id'), 'left')
                    ->join(array('tt' => 'ta_tag'), 'tt.in_id =tpt.ta_tag_in_id', array('tag'=>'va_nombre'), 'left')
                    ->where(array('ta_plato.in_id' => $id));
        $selectStrin = $sql->getSqlStringForSqlObject($selectto);
        $result = $adapter->query($selectStrin, $adapter::QUERY_MODE_EXECUTE);
        $tag = $result->toArray();
       $solr = \Classes\Solr::getInstance()->getSolr();
        if ($solr->ping()){
            if($caso!==1)
           { $solr->deleteByQuery('id:' . $id);}
            $document = new \Apache_Solr_Document();
            $document->id = $id;
            $document->name = $plato[0]['va_nombre'];
            $document->tx_descripcion = $plato[0]['tx_descripcion'];
            $document->va_precio = $plato[0]['va_precio'];
            $document->en_estado = $plato[0]['en_estado'];
            $document->plato_tipo = $plato[0]['tipo_plato_nombre'];
            $document->va_direccion = $plato[0]['direccion'];
            $document->restaurante = $plato[0]['restaurant_nombre'];
            $document->tipo_comida = $plato[0]['nombre_tipo_comida'];
            $document->en_destaque = $plato[0]['en_destaque'];
            $document->va_telefono = $plato[0]['telefono'];
            $document->latitud = $plato[0]['latitud'];
            $document->longitud = $plato[0]['longitud'];
            $document->departamento = $plato[0]['departamento'];
            foreach ($tag as $resultado)
            {$document->setMultiValue('tag',$resultado['tag']);  }
            $document->distrito = $plato[0]['distrito'];
            $document->va_imagen = $plato[0]['va_imagen'];
            $document->comentarios = $plato[0]['cantidad'];
            $document->restaurant_estado = $plato[0]['restaurant_estado'];
            $document->puntuacion = $plato[0]['Ta_puntaje_in_id']; 
            $solr->addDocument($document);
            $solr->commit();
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
            ->join(array('r'=>'ta_plato'),'f.ta_plato_in_id=r.in_id',array('va_nombre','in_id'))
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
            ->where(array('r.va_nombre LIKE ?'=>'%'.$datos.'%'));
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
           $select->order('f.in_id DESC');
            $selectString = $sql->getSqlStringForSqlObject($select);

            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $rowset = $results->buffer();
            //var_dump($rowset);exit;
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
         
         
         public function deleteComentario($id, $idplato)
         {       
        $this->tableGateway->delete(array('in_id' => $id));
        
        
        $adapter2=$this->tableGateway->getAdapter();
        $promselect=$this->tableGateway->getAdapter()
                ->query('SELECT SUM(ta_puntaje_in_id)AS SumaPuntaje ,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
                    ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS TotPuntaje
                    FROM ta_comentario
                    where  ta_comentario.ta_plato_in_id='.$idplato, $adapter2::QUERY_MODE_EXECUTE);
                        $prom=$promselect->toArray();
             
              $update = $this->tableGateway->getSql()->update()->table('ta_plato')
                        ->set(array('Ta_puntaje_in_id'=>$prom[0]['TotPuntaje']))
                        ->where(array('ta_plato.in_id'=>$idplato));//$prom[0]['in_id']
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
               
         }
         
         
           public function agregarComentario($coment,$id){

            $comentario = array(
            'tx_descripcion' => $coment['tx_descripcion'],
            'Ta_plato_in_id' => $coment['Ta_plato_in_id'],
            'Ta_cliente_in_id' => $id,
            'Ta_puntaje_in_id' => $coment['Ta_puntaje_in_id'],
                'da_fecha'=>  $fecha = date("Y-m-d h:m:s")
                ); 
         $id = (int) $coment['in_id'];
            if ($id == 0) {            
           $insertcoment= $this->tableGateway->getSql()->insert()->into('ta_comentario')
                    ->values($comentario);
            $statement2 = $this->tableGateway->getSql()->prepareStatementForSqlObject($insertcoment);
            $statement2->execute();  
             }
             
                    $adapter2=$this->tableGateway->getAdapter();
         $promselect=$this->tableGateway->getAdapter()
                ->query('SELECT SUM(ta_puntaje_in_id)AS SumaPuntaje ,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
                    ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS TotPuntaje
                    FROM ta_comentario
                    where  ta_comentario.ta_plato_in_id='.$coment['Ta_plato_in_id'], $adapter2::QUERY_MODE_EXECUTE);
                        $prom=$promselect->toArray();
             
              $update = $this->tableGateway->getSql()->update()->table('ta_plato')
                        ->set(array('Ta_puntaje_in_id'=>$prom[0]['TotPuntaje']))
                        ->where(array('ta_plato.in_id'=>$coment['Ta_plato_in_id']));//$prom[0]['in_id']
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
            
    
             
    }
         
//    public function usuarioface($id_face)
//    {
//        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $selecttot = $sql->select()->from('ta_cliente')
//                ->where(array('id_facebook'=>$id_face));
//        $selectString = $sql->getSqlStringForSqlObject($selecttot);
//        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//        return $resultSet->toArray();
//    }
//     public function idfacebook($id,$idfacebook,$logout)
//    {
//        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $selecttot = $sql->update('ta_cliente')
//                ->set(array('id_facebook'=>$idfacebook,'va_logout'=>$logout))
//                ->where(array('in_id'=>$id));
//        $selectString = $sql->getSqlStringForSqlObject($selecttot);
//                   $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//    }
//public function idfacebook2($id,$logout)
//    {
//        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $selecttot = $sql->update('ta_cliente')
//                ->set(array('va_logout'=>$logout))
//                ->where(array('in_id'=>$id));
//        $selectString = $sql->getSqlStringForSqlObject($selecttot);
//                   $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//    }
//    
//     public function insertarusuariofacebbok($nombre,$email,$idfacebook,$logout)
//    {   
//      $contrasena = sha1($idfacebook) ;
//         $fecha = date("Y-m-d h:m:s");  
//        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $selecttot = $sql->insert()
//                ->into('ta_cliente')
//                ->values(array('va_nombre_cliente'=>$nombre,'va_email'=>$email,'id_facebook'=>$idfacebook,
//                    'en_estado'=>'activo','va_contrasena'=>$contrasena
//                   ,'va_logout'=>$logout,'va_fecha_ingreso'=>$fecha,'va_notificacion'=>'si'));
//        $selectString = $sql->getSqlStringForSqlObject($selecttot);
//      $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//     }
}