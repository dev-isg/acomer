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
                'Ta_plato_in_id'=>$coment['Ta_plato_in_id'],
                'da_fecha'=>date('c')//date('Y-m-dTH:i:s.uZ')//'2013-12-12'
                );
           
         $id = (int) $coment['in_id'];
            if ($id == 0) {            
           $insertcoment= $this->tableGateway->getSql()->insert()->into('ta_comentario')
                    ->values($comentario);
            $statement2 = $this->tableGateway->getSql()->prepareStatementForSqlObject($insertcoment);
            $statement2->execute();  
//                $cookie->id=$coment['Ta_plato_in_id'];
             }
             
                    $adapter2=$this->tableGateway->getAdapter();
        $promselect=$this->tableGateway->getAdapter()
                ->query('SELECT ta_comentario.*,SUM(ta_puntaje_in_id)AS SumaPuntaje ,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
                    ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS TotPuntaje
                    FROM ta_comentario
                    where ta_comentario.ta_plato_in_id='.$coment['Ta_plato_in_id'], $adapter2::QUERY_MODE_EXECUTE);
                        $prom=$promselect->toArray();
//        var_dump($coment['Ta_plato_in_id']);exit;

             
              $update = $this->tableGateway->getSql()->update()->table('ta_plato')
                        ->set(array('Ta_puntaje_in_id'=>$prom[0]['TotPuntaje']))
                        ->where(array('ta_plato.in_id'=>$coment['Ta_plato_in_id']));//$prom[0]['in_id']
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
              //  $platos = new \Platos\Model\PlatosTable();
                $this->cromSolr($coment['Ta_plato_in_id']);
                
                
    }

public function cromSolr($id)        
   {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
            ->from('ta_plato')
            ->join(array('c'=>'ta_comentario'),'c.ta_plato_in_id=ta_plato.in_id',array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(*)')),'left')
            ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_nombre'=>'va_nombre'),'left')
            ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
            ->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array('de_latitud','de_longitud','va_direccion'), 'left')
            ->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurant_nombre'=>'va_nombre','restaurant_estado'=>'en_estado'), 'left')
            ->join(array('tu'=>'ta_ubigeo'), 'tu.in_id = tl.ta_ubigeo_in_id', array('distrito'=>'ch_distrito'), 'left')
            ->where(array('ta_plato.in_id'=>$id));   
            $selectString = $sql->getSqlStringForSqlObject($selecttot);            
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);                      
            $plato=$results->toArray(); 
            require './vendor/SolrPhpClient/Apache/Solr/Service.php';
                                $solr = new \Apache_Solr_Service('192.168.1.38', 8983, '/solr');  
                                           if ($solr->ping())
                                        {
                                             $solr->deleteByQuery('id:'.$id);
                                             $document = new \Apache_Solr_Document();
                                             $document->id = $id;     
                                             $document->name = $plato[0]['va_nombre'];                                            
                                             $document->tx_descripcion = $plato[0]['tx_descripcion'];
                                             $document->va_precio = $plato[0]['va_precio'];
                                             $document->en_estado = $plato[0]['en_estado'];
                                             $document->plato_tipo = $plato[0]['tipo_plato_nombre'];
                                             $document->va_direccion = $plato[0]['va_direccion']; 
                                             $document->restaurante = $plato[0]['restaurant_nombre'];                                         
                                             $document->en_destaque = $plato[0]['en_destaque'];
                                             $document->latitud = $plato[0]['de_latitud'];                                         
                                             $document->longitud = $plato[0]['de_longitud'];
                                             $document->distrito = $plato[0]['distrito'];
                                             $document->va_imagen = $plato[0]['va_imagen'];
                                             $document->comentarios =$plato[0]['cantidad'];
                                             $document->restaurant_estado =$plato[0]['restaurant_estado'];
                                             $document->puntuacion = $plato[0]['Ta_puntaje_in_id'];
                                             $solr->addDocument($document);
                                             $solr->commit();
                                             $solr->optimize();
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