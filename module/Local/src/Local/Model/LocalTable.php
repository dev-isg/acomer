<?php
namespace Local\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Update;

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
    
    public function listar($id,$consulta = null){
        
              $select = $this->tableGateway->getSql()->select()
             ->join(array('r'=>'ta_restaurante'),'ta_restaurante_in_id=r.in_id',array('va_nombre'))
             ->join(array('u'=>'ta_ubigeo'),'ta_ubigeo_in_id=u.in_id',array('ch_pais','ch_departamento','ch_provincia','ch_distrito'))
              ->where('(r.in_id LIKE "%'.$id.'%") AND ((r.va_nombre LIKE "%'.$consulta.'%") OR (u.ch_distrito LIKE "%'.$consulta.'%"))')//OR (ta_restaurante_in_id LIKE "%'.$consulta.'%") OR (ta_ubigeo_in_id LIKE "%'.$consulta.'%")
              ->order('in_id DESC');
              //->where(array('r.in_id'=>$id));//('ta_restaurante_in_id='.'1');//r.in_id
              $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
//              var_dump($selectString);exit;
            $adapter=$this->tableGateway->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results->buffer();

      return $results;
      
    }
    

    
    public function editarLocal($local,$id){

          $pais=$local['pais'];
          $departamento=$local['departamento'];
          $provincia=$local['provincia'];
          $distrito=$local['distrito'];
          
           $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);
          $idubigeo=$sql->select()->from('ta_ubigeo')
                  ->columns(array('in_id'))
                  ->where(array('in_idpais'=>$pais,'in_iddep'=>$departamento,'in_idprov'=>$provincia,'in_iddis'=>$distrito));
          $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);

            $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);
            $convertir=$result->toArray();
            
         $data = array(
           'va_telefono'         => $local['va_telefono'],
           'va_horario'   => $local['va_horario'],
           'de_latitud'            => $local['de_latitud'],
           'de_longitud'         => $local['de_longitud'],
           'va_rango_precio'            => $local['va_rango_precio'],  
           'va_horario_opcional'  => $local['va_horario_opcional'],
            'va_direccion' => $local['va_direccion'],
            'va_direccion_referencia'=>$local['va_direccion_referencia'],
           //'ta_restaurante_in_id' => $local['ta_restaurante_in_id'],
            'ta_ubigeo_in_id' => $convertir[0]['in_id'],  
           'va_dia'=>$local['va_dia'],
               'va_email'=>$local['va_email']
        );

        
            $idupda=$sql
                  ->update('ta_local')->set($data)
                  ->where(array('in_id'=>$id));
          $selectString3 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idupda);
//          var_dump($selectString3);exit;
          $result3 = $adapter->query($selectString3, $adapter::QUERY_MODE_EXECUTE);
          
            
            $adapte=$this->tableGateway->getAdapter();
            $sq = new Sql($adapte);
            $select = $sq->select()
            ->from('ta_local')
            ->join(array('tl'=>'ta_plato_has_ta_local'), 'ta_local.in_id = tl.Ta_local_in_id', array('plato'=>'Ta_plato_in_id'))
            ->where(array('ta_local.in_id'=>$id));   
            $selectString = $sql->getSqlStringForSqlObject($select); 
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);  
            $plato=$results->toArray();
            foreach ($plato as $result) {
            $this->estadoRestauranteSolarAction($result['plato'],'');
             }
           return $result3; 
 
        }
    

    
    public function eliminarLocal($id){
        
        $this->tableGateway->delete(array('in_id' => $id));
 
    }
    
    public function guardarLocal(Local $local, $servicio){
        //var_dump('hola');exit;
//         var_dump($servicio);exit;
          $pais=$local->pais;
          $departamento=$local->departamento;
          $provincia=$local->provincia;
          $distrito=$local->distrito;
          
           $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);      
          $idubigeo=$sql->select()->from('ta_ubigeo')
                  ->columns(array('in_id'))
                  ->where(array('in_idpais'=>$pais,'in_iddep'=>$departamento,'in_idprov'=>$provincia,'in_iddis'=>$distrito));
          $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);

            $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);
            $convertir=$result->toArray();

          $data = array(
           'va_telefono'         => $local->va_telefono,
           'va_horario'   => $local->va_horario,
           'de_latitud'            => $local->de_latitud,
           'de_longitud'         => $local->de_longitud,
           'va_rango_precio'            => $local->va_rango_precio,  
           'va_horario_opcional'  => $local->va_horario_opcional,
            'va_direccion' => $local->va_direccion,
           'ta_restaurante_in_id' => $local->ta_restaurante_in_id,
            'ta_ubigeo_in_id' => $convertir[0]['in_id'],//$local->ta_ubigeo_in_id
            'va_email'=>$local->va_email,
            'va_dia'=>$local->va_dia,
            'va_direccion_referencia'=>$local->va_direccion_referencia
        );


//          foreach($data as $index=>$valor){
//                if(empty($data[$index])){
//                    $data[$index]=1;
//                }
//            }
            

        $id = (int)$local->in_id;

        if ($id == 0) {
            
            
//            $inservicio = $this->tableGateway->getSql()->insert()->into('ta_servicio_local')
//                    ->values(array('va_nombre'=>$servicio));
//            $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($inservicio);
//            $adapter=$this->tableGateway->getAdapter();
//            $resultserv = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//            
//            $idserv=$this->tableGateway->getLastInsertValue();
           
          //   var_dump($servicio);exit;
            $this->tableGateway->insert($data);
            $idlocal=$this->tableGateway->getLastInsertValue();
             //var_dump($idlocal);exit;
            foreach($servicio as $key=>$value){
                
             $insert = $this->tableGateway->getSql()->insert()->into('ta_local_has_ta_servicio_local')
                    ->values(array('ta_local_in_id'=>$idlocal,'ta_servicio_local_in_id'=>$value));
            $selectString2 = $this->tableGateway->getSql()->getSqlStringForSqlObject($insert);
            $adapter=$this->tableGateway->getAdapter();
            $result = $adapter->query($selectString2, $adapter::QUERY_MODE_EXECUTE);
            }

            
            
            
            
            
        } else {
//            if ($this->getRestaurante($id)) {
                
                $this->tableGateway->update($data, array('in_id' => $id));
                
                
//            } else {
//                throw new \Exception('no existe el usuario');
//            }
        }
    }
    
    
        public function getLocal($id)
    {
        //$id  = (int) $id;
//        $rowset = $this->tableGateway->select(array('in_id' => $id));
//        $row = $rowset->current();
//        if (!$row) {
//            throw new \Exception("Could not find row $id");
//        }
//        return $row;
        
        
        
            $select = $this->tableGateway->getSql()->select()
             ->join(array('u'=>'ta_ubigeo'),'ta_ubigeo_in_id=u.in_id',array('in_idpais','in_iddep','in_idprov','in_iddis'))
//             ->join(array('t'=>'ta_local_has_ta_servicio_local'),'ta_local.in_id=t.ta_local_in_id',array('ta_servicio_local_in_id'))       
              ->where(array('ta_local.in_id'=>$id));//('ta_restaurante_in_id='.'1');//r.in_id
              $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
//              var_dump($selectString);exit;
            $adapter=$this->tableGateway->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            //$results->current();
//           $array=array();
//             foreach($results as $result){
//                 $array[]=$result;
//             }
//            var_dump($array);exit;
            
            return $results->current();
    }
    
          public function getServiciosId($id){
           $select = $this->tableGateway->getSql()->select()
               ->columns(array('in_id'))
            // ->join(array('u'=>'ta_ubigeo'),'ta_ubigeo_in_id=u.in_id',array('in_idpais','in_iddep','in_idprov','in_iddis'))
             ->join(array('t'=>'ta_local_has_ta_servicio_local'),'ta_local.in_id=t.ta_local_in_id',array('ta_servicio_local_in_id'))       
              ->where(array('ta_local.in_id'=>$id));
              $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($select);
//              var_dump($selectString);exit;
            $adapter=$this->tableGateway->getAdapter();
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            
//          $array=array();
//             foreach($results as $result){
//                 $array[]=$result;
//             }
////            var_dump($array);exit;
            
            return $results->toArray();
      }
      
           public function estadoRestauranteSolarAction($id,$caso=null) {
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
                    ->where(array('ta_plato.in_id' => $id ));
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
            if($plato[0]['latitud']==null or $plato[0]['longitud']==null)
            {$document->latitud = '1';
            $document->longitud = '1';} else
            {$document->latitud = $plato[0]['latitud'];
            $document->longitud = $plato[0]['longitud'];}
            $document->departamento = $plato[0]['departamento'];
            if(count($tag)>0)
            { foreach ($tag as $resultado)
            {$document->setMultiValue('tag',$resultado['tag']);}}
            $document->distrito = $plato[0]['distrito'];
            $document->va_imagen = $plato[0]['va_imagen'];
            $document->comentarios = $plato[0]['cantidad'];
            $document->restaurant_estado = $plato[0]['restaurant_estado'];
            $document->puntuacion = $plato[0]['Ta_puntaje_in_id']; 
            $solr->addDocument($document);
            $solr->commit(); 
        }
    }
    
}
