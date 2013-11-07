<?php

namespace Platos\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Platos\Model\Platos;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Cache\Storage\StorageInterface;
class PlatosTable {

    protected $tableGateway;
    protected $cache;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
        $this->resultSetPrototype = new HydratingResultSet();
         $this->resultSetPrototype->setObjectPrototype(new Platos());
    }
 public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
    } 
    
       
     public function platoslistadelsabor($limit){
            if($limit == 1)
            {$limite=9;
           $constante= '';}else{$constante=',9';
            $limite= ($limit-1)*9; }
         $adapter = $this->tableGateway->getAdapter();  
       if( ($resultadosHome = $this->cache->getItem('platoscache'.$limit)) == FALSE) {
         $resultadosHome = $this->tableGateway->getAdapter()
                ->query('SELECT ta_plato.in_id AS id ,ta_plato.va_nombre,ta_plato.Ta_puntaje_in_id,ta_plato.va_imagen,ta_plato.en_destaque,tr.va_nombre AS restaurant_nombre ,COUNT(ta_comentario.in_id ) AS NumeroComentarios
                ,tu.ch_distrito AS Distrito,tu.ch_departamento AS Departamento,tl.va_telefono AS telefono,tl.va_direccion AS direccion
                FROM ta_plato
                LEFT JOIN  ta_comentario ON ta_plato.in_id = ta_comentario.ta_plato_in_id
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id`
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_ubigeo` AS `tu` ON `tu`.`in_id` = `tl`.`ta_ubigeo_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                WHERE    ta_plato.en_estado=1 AND tr.en_estado=1 AND tr.va_nombre IS NOT NULL  
                GROUP BY id,ta_plato.en_destaque
                ORDER BY ta_plato.en_destaque ASC,ta_plato.Ta_puntaje_in_id DESC,NumeroComentarios DESC  LIMIT '.$limite.''.'' . $constante.'' , $adapter::QUERY_MODE_EXECUTE);
                    $resultadosHome = $resultadosHome->toArray();
                  $this->cache->setItem('platoscache'.$limit,  $resultadosHome ); 
          }
       return $resultadosHome;
    }

    public function fetchAll($consulta = null,$consulta2 = null) {

        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('in_id', 'va_nombre', 'va_precio', 'en_estado', 'en_destaque', 'Ta_puntaje_in_id'));
        $sqlSelect->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_va_nombre' => 'va_nombre'), 'left'); //, 'left'
        $sqlSelect->join(array('pl' => 'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array('ta_local_in_id'), 'left');
        $sqlSelect->join(array('tl' => 'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left');
        $sqlSelect->join(array('tr' => 'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurante_va_nombre' => 'va_nombre'), 'left');
        $sqlSelect->join(array('c' => 'ta_comentario'), 'c.ta_plato_in_id=ta_plato.in_id', array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(c.in_id)')), 'left');
        if ($consulta != null) {
            $sqlSelect->where(array('pl.ta_local_in_id'=>$consulta))->where->and->like('ta_plato.va_nombre', '%'.$consulta2.'%');//where(array('pl.ta_local_in_id' => $consulta));
        }
        $sqlSelect->group('ta_plato.in_id')->order('ta_plato.in_id desc');

        $resultSet = $this->tableGateway->selectWith($sqlSelect);

        return $resultSet;
    }

    
    public function guardarPlato(Platos $platos, $imagen, $idlocal = null,$otro =null,$promocion=null) {

        $data = array(
//            'in_id' => $plato->in_id,
            
            'va_imagen' => $imagen, //$plato->va_imagen,
            'tx_descripcion' => $platos->tx_descripcion,
            'va_nombre' => $platos->va_nombre,
            'va_precio' => $platos->va_precio,
            'en_destaque' => $platos->en_destaque,
            'en_estado' => (!empty($platos->en_estado)) ? $platos->en_estado : 2, //$plato->en_estado,
            'Ta_tipo_plato_in_id' => $platos->Ta_tipo_plato_in_id,
            'Ta_puntaje_in_id' => (!empty($platos->Ta_puntaje_in_id)) ? $platos->Ta_puntaje_in_id : 0,
            //'Ta_usuario_in_id' => (!empty($plato->Ta_usuario_in_id)) ? $plato->Ta_usuario_in_id : 1//$plato->Ta_usuario_in_id,
           'Ta_usuario_in_id' => 133,//$plato->Ta_usuario_in_id,
        );
//       var_dump($platos->ta_tipo_plato_in_id);
//       var_dump($otro);exit;
        if($otro!='')
          {
           $adaptado = $this->tableGateway->getAdapter();
           $sq = new Sql($adaptado);
           $seleccionar = $sq->select()->from('ta_local')
           ->join(array('tr' => 'ta_restaurante'), 'tr.in_id = ta_local.ta_restaurante_in_id', array('restaurant_nombre' => 'va_nombre', 'restaurant_estado' => 'en_estado','Ta_tipo_comida_in_id'), 'left')
           ->where(array('ta_local.in_id'=>$idlocal));
            $selectStrin = $sq->getSqlStringForSqlObject($seleccionar);
            $resultados = $adaptado->query($selectStrin, $adaptado::QUERY_MODE_EXECUTE);
            $resultado=$resultados->toArray();
            $resultado[0]['Ta_tipo_comida_in_id'];
            $insertar = $this->tableGateway->getSql()->insert()
                    ->into('ta_tipo_plato')
                    ->values(array('va_nombre' =>$otro, 'Ta_tipo_comida_in_id' => $resultado[0]['Ta_tipo_comida_in_id']));
            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insertar);
            $statement->execute();
            $idtipoplato=$this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
            }else{}

        $data['en_destaque'] = 'si';
        $id = (int) $platos->in_id;
        
           $adapterc = $this->tableGateway->getAdapter();
           $sql = new Sql($adapterc);
           $cantidad = $sql->select()->from('ta_plato_has_ta_local')
                        ->columns(array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(*)')))
                       ->where(array('ta_local_in_id'=>$idlocal));
            $selectStringC = $sql->getSqlStringForSqlObject($cantidad);
            $results = $adapterc->query($selectStringC, $adapterc::QUERY_MODE_EXECUTE);
            $cant=$results->toArray();


        if ($id == 0) {
     if(!empty($idtipoplato))
           {$data2 = array(
            'va_imagen' => $imagen, 
            'tx_descripcion' => $platos->tx_descripcion,
            'va_nombre' => $platos->va_nombre,
            'va_precio' => $platos->va_precio,
            'en_destaque' => $platos->en_destaque,
            'en_estado' => (!empty($platos->en_estado)) ? $platos->en_estado : 2, 
            'Ta_tipo_plato_in_id' => $idtipoplato,
            'Ta_puntaje_in_id' => (!empty($platos->Ta_puntaje_in_id)) ? $platos->Ta_puntaje_in_id : 0,
            'Ta_usuario_in_id' => 133,
                     );
                      $this->tableGateway->insert($data2);
                }else{  $this->tableGateway->insert($data);}
             $idplato = $this->tableGateway->getLastInsertValue();
            $insert = $this->tableGateway->getSql()->insert()
                    ->into('ta_plato_has_ta_local')
                    ->values(array('Ta_plato_in_id' => $idplato, 'Ta_local_in_id' => $idlocal));
            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insert);
            $statement->execute();
            ////////////promociones////////////////////
            if($promocion!=null){
            
                    foreach($promocion as $value){
                    $promo = $this->tableGateway->getSql()->insert()
                            ->into('ta_plato_has_ta_tag')
                            ->values(array('ta_plato_in_id' => $idplato, 'ta_tag_in_id' => $value));
                    $statementProm = $this->tableGateway->getSql()->prepareStatementForSqlObject($promo);
                    $statementProm->execute();
                    }
            
            }
            //////////////fin///////////////

           $this->cromSolr($idplato,1);
         // }
         } else {// echo 'hola';exit;

            if ($this->getPlato($id)){
                 if(!empty($idtipoplato))
                    {
                                  //        var_dump($idtipoplato);exit;
                     $data['ta_tipo_plato_in_id'] = $idtipoplato;}
                $this->tableGateway->update($data, array('in_id' => $id));
//                if($this->promocionxPlato($id)->toArray()>0){
//                    $var=1;
//                }else{$var=0;}
//                var_dump($var);exit;
                    if($this->promocionxPlato($id)->toArray()>0){

                        if ($promocion != null) {
                        foreach ($promocion as $value) {
                            $delete = $this->tableGateway->getSql()
                                    ->delete()
                                    ->from('ta_plato_has_ta_tag')
                                    ->where(array('ta_plato_in_id' => $id));
                            $selectStringDelete = $this->tableGateway->getSql()->getSqlStringForSqlObject($delete);
                            $adapter1 = $this->tableGateway->getAdapter();
                            $adapter1->query($selectStringDelete, $adapter1::QUERY_MODE_EXECUTE);
                        }
                        foreach ($promocion as $value) {
                            $promo2 = $this->tableGateway->getSql()->insert()
                                    ->into('ta_plato_has_ta_tag')
                                    ->values(array('ta_plato_in_id' => $id, 'ta_tag_in_id' => $value));
                            $selectStringUpdate = $this->tableGateway->getSql()->getSqlStringForSqlObject($promo2);
                            $adapter2 = $this->tableGateway->getAdapter();
                            $adapter2->query($selectStringUpdate, $adapter2::QUERY_MODE_EXECUTE);
                        }
                    }else{
                           $delete = $this->tableGateway->getSql()
                                    ->delete()
                                    ->from('ta_plato_has_ta_tag')
                                    ->where(array('ta_plato_in_id' => $id));
                            $selectStringDelete = $this->tableGateway->getSql()->getSqlStringForSqlObject($delete);
                            $adapter1 = $this->tableGateway->getAdapter();
                            $adapter1->query($selectStringDelete, $adapter1::QUERY_MODE_EXECUTE);
                            
                        
                    }

                    }else{
                      foreach($promocion as $value){
                        $update = $this->tableGateway->getSql()->insert()
                               ->into('ta_plato_has_ta_tag')
                               ->values(array('ta_plato_in_id' => $id, 'ta_tag_in_id' => $value));
                   $selectStringUpdate2 = $this->tableGateway->getSql()->getSqlStringForSqlObject($update);
                $adapter3 = $this->tableGateway->getAdapter();
                $adapter3->query($selectStringUpdate2, $adapter2::QUERY_MODE_EXECUTE);
                         }
                         
                    }

                $this->cromSolr($id,'');

               
            } else {
                throw new \Exception('No existe el id');
            }
        }
    }

       public function cromSolr($id,$caso=null) {
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

    public function editarPlato($platos, $imagen, $idrestaurant = null) {

//                var_dump($platos);exit;
        $data = array(
//            'in_id' => $plato->in_id,
            'va_imagen' => $imagen['name'] = 'hola', //$plato->va_imagen,
            'tx_descripcion' => $platos["tx_descripcion"],
            'va_nombre' => $platos["va_nombre"],
            'va_precio' => $platos["va_precio"],
            'en_destaque' => 1, // $plato->en_destaque,
            'en_estado' => 1, //$plato->en_estado,
            'Ta_tipo_plato_in_id' => $platos["ta_tipo_plato_in_id"],
            'Ta_puntaje_in_id' => 1, //$plato->Ta_puntaje_in_id,
            'Ta_usuario_in_id' => 1, //$plato->Ta_usuario_in_id,
        );
        $id = $platos["in_id"];
//        var_dump($platos["in_id"]);exit;
        $this->tableGateway->update($data, array('in_id' => $id));
        $this->cromSolr($id,'');
    }

    public function eliminarPlato($id, $estado) {

        $data = array(
            'en_estado' => $estado,
        );
        $this->tableGateway->update($data, array('in_id' => $id));
        if($estado=='activo'){ $this->cromSolr($id,'');}
       else{ $this->eliminaprevia2($id);}
    }

     public function eliminaprevia($id,$estado)
      {
         if($estado=='activo'){ $this->cromSolr($id,'');}
       else{ $this->eliminaprevia2($id);}
 
      }
    
     public function eliminaprevia2($id)
      {$solr = \Classes\Solr::getInstance()->getSolr();
        if ($solr->ping())
            $solr->deleteByQuery('id:' . $id);
            $solr->commit();
            $solr->optimize();}

    public function destaquePlato($id, $destaque) {

        $data = array(
            'en_destaque' => $destaque,
        );
        $this->tableGateway->update($data, array('in_id' => $id));
        $this->cromSolr($id,'');
    }

    /*
     * @return  un row de un plato
     */

    public function getPlato($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('in_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    /*
     * plato x restaurante 
     */

    public function getPlatoxRestaurant($idplato) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_plato')
                ->columns(array('*', 'num' => new \Zend\Db\Sql\Expression('COUNT(tc.in_id)')))
                ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_nombre' => 'va_nombre'), 'left')
                ->join(array('pl' => 'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
                ->join(array('tl' => 'ta_local'), 'tl.in_id = pl.ta_local_in_id', array('va_horario_opcional','de_latitud','va_email', 'de_longitud', 'va_direccion', 'va_horario', 'va_dia', 'va_telefono','va_direccion_referencia'), 'left')
                ->join(array('tr' => 'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurant_id' => 'in_id', 'restaurant_nombre' => 'va_nombre', 'restaurant_img' => 'va_imagen','web'=>'va_web'), 'left')
                ->join(array('ttc' => 'ta_tipo_comida'), 'ttc.in_id=tr.ta_tipo_comida_in_id', array('tipo_comida'=>'va_nombre_tipo'), 'left')           
                ->join(array('tu' => 'ta_ubigeo'), 'tu.in_id = tl.ta_ubigeo_in_id', array('pais' => 'ch_pais', 'departamento' => 'ch_departamento', 'provincia' => 'ch_provincia', 'distrito' => 'ch_distrito'), 'left')
                ->join(array('tc' => 'ta_comentario'), 'tc.ta_plato_in_id=ta_plato.in_id', array('estado_comen'=>'en_estado'), 'left')
//            ->join(array('tcli'=>'ta_cliente'),'tcli.in_id=tc.ta_cliente_in_id',array('va_nombre_cliente','va_email'),'left')
                ->where(array('ta_plato.in_id' => $idplato,'ta_plato.en_estado' =>'activo','tr.en_estado'=>'activo'));//'tc.en_estado'=>'aprobado'
        $selecttot->group('ta_plato.in_id');

        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

        return $results; //->toArray();
    }

    public function getPagoxPlato($idplato) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_plato')
                ->columns(array())
                ->join(array('pl' => 'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
                ->join(array('tl' => 'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left')
                ->join(array('tr' => 'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array(), 'left')
                ->join(array('rp' => 'ta_restaurante_has_ta_medio_pago'), 'rp.ta_restaurante_in_id= tr.in_id', array(), 'left')
                ->join(array('mp' => 'ta_medio_pago'), 'rp.ta_medio_pago_in_id= mp.in_id', array('id_pago' => 'in_id', 'nom_pago' => 'va_nombre'), 'left')
                ->where(array('ta_plato.in_id' => $idplato));

        $selectString = $sql->getSqlStringForSqlObject($selecttot);

        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function getServicioxPlato($idplato) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_plato')
                ->columns(array('in_id'))
                ->join(array('pl' => 'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
                ->join(array('tl' => 'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left')
                ->join(array('ls' => 'ta_local_has_ta_servicio_local'), 'tl.in_id= ls.ta_local_in_id', array(), 'left')
                ->join(array('s' => 'ta_servicio_local'), 'ls.ta_servicio_local_in_id= s.in_id', array('id_servicio' => 'in_id', 'nom_servicio' => 'va_nombre'), 'left')
                ->where(array('ta_plato.in_id' => $idplato));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

     public function guardarregistro($dataregistro,$imagen) {
         $data = array(
            'va_nombre_contacto' => $dataregistro->va_nombre_contacto, 
            'va_correo' => $dataregistro->va_correo,
             'va_telefono' => $dataregistro->va_telefono,
            'va_nombre_restaurante' => $dataregistro->va_nombre_restaurante,
            'va_imagen' =>$imagen,
            'va_direccion' => $dataregistro->va_direccion,
            'va_horario' => $dataregistro->va_horario,
            'Ta_tipo_comida_in_id' => $dataregistro->Ta_tipo_comida_in_id,
             'en_estado'=>'desactivo'
        );
              $adapter = $this->tableGateway->getAdapter();
              $sql = new Sql($adapter);
              $selecttot = $sql->insert()
                      ->into('ta_registro')
                      ->values($data);
              $selectString = $sql->getSqlStringForSqlObject($selecttot);
            $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
     $idrestaurante=$this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
     return $idrestaurante;

    }
       
public function guardarplatoregistro($dataregistro) {
         
         $va = count($dataregistro)/4;
         for($i=1;$i<=$va;$i++)
           { 
             $Ta_registro_in_id = 'Ta_registro_in_id'.$i;
             $va_descripcion = 'va_descripcion'.$i;
             $va_nombre_plato = 'va_nombre_plato'.$i;
             $va_precio = 'va_precio'.$i;
              $data = array(
                 'va_imagen' =>'plato'.$i.'-'.$dataregistro->$Ta_registro_in_id.'.jpg', 
                   'Ta_registro_in_id' => $dataregistro->$Ta_registro_in_id,            
                  'va_nombre_plato' => $dataregistro->$va_nombre_plato,     
                  'va_descripcion' => $dataregistro->$va_descripcion, 
                   'va_precio' => $dataregistro->$va_precio,) ;
             $insertar = $this->tableGateway->getSql()->insert()
                      ->into('ta_registroplato')
                      ->values($data);
            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insertar);
            $statement->execute();
           }
    }
    
    
    public function getLocalesxRestaurante($idrest) {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_local')
                ->columns(array('in_id', 'va_telefono', 'de_latitud', 'de_longitud', 'va_direccion'))
                ->join(array('tu' => 'ta_ubigeo'), 'ta_local.ta_ubigeo_in_id = tu.in_id', array('distrito' => 'ch_distrito','depa' => 'ch_departamento'), 'left')
                ->where(array('ta_restaurante_in_id' => $idrest));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function getComentariosxPlatos($idplato) {
         $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_plato')
                ->join(array('tc' => 'ta_comentario'), 'tc.ta_plato_in_id=ta_plato.in_id', array('tx_descripcion', 'ta_puntaje_in_id','en_estado'), 'left')
                ->join(array('tcli' => 'ta_cliente'), 'tcli.in_id=tc.ta_cliente_in_id', array('va_nombre_cliente', 'va_email'), 'left')
                ->where(array('ta_plato.in_id' => $idplato,'tc.Ta_puntaje_in_id>?'=>0,'tc.en_estado'=>'aprobado'))
                ->order('tc.in_id DESC');
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results->buffer();
        return $results;
    }

    public function comentarios($dest = 1, $lim, $val) {
        if ($val == 1) {
            $res = 'is not null'; //'is not null or ta_comentario.ta_puntaje_in_id!=0'; 
        } else if ($val == 2) {
            $res = 'is null'; //'is null or ta_comentario.ta_puntaje_in_id!=0';
        } else if ($val == 3) {
            $res = 'is null'; //'is null or ta_comentario.ta_puntaje_in_id=0';    
        }

        $adapter = $this->tableGateway->getAdapter();
        $primer = $this->tableGateway->getAdapter()
                ->query('SELECT ta_plato.*,tr.va_nombre AS restaurant_nombre,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
        ta_comentario.ta_puntaje_in_id AS Puntaje,ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS Promedio
        FROM ta_plato
        LEFT JOIN  ta_comentario
        ON ta_plato.in_id = ta_comentario.ta_plato_in_id
        LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id` 
        LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
        LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id` 
        LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
        where ta_plato.en_destaque=' . $dest . ' and ta_plato.en_estado=1 and tr.va_nombre is not null and (ta_comentario.ta_puntaje_in_id ' . $res . ')
        GROUP BY va_nombre,in_id
        order by MAX(ta_comentario.ta_puntaje_in_id) DESC
        LIMIT ' . $lim, $adapter::QUERY_MODE_EXECUTE);

        return $primer;
    }

    public function cantComentxPlato() {
        
        $adapter = $this->tableGateway->getAdapter();
        $primer = $this->tableGateway->getAdapter()
                ->query('SELECT ta_plato.*,tr.va_nombre AS restaurant_nombre ,COUNT(ta_comentario.in_id ) AS NumeroComentarios
                     ,tu.ch_distrito AS Distrito,tu.ch_departamento AS Departamento,tl.va_telefono AS telefono,tl.va_direccion AS direccion
                FROM ta_plato
                LEFT JOIN  ta_comentario ON ta_plato.in_id = ta_comentario.ta_plato_in_id
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id`
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_ubigeo` AS `tu` ON `tu`.`in_id` = `tl`.`ta_ubigeo_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                where ta_plato.en_destaque=1 and ta_plato.en_estado=1  and tr.va_nombre is not null 
                 GROUP BY in_id 
               ORDER BY RAND() LIMIT 3' , $adapter::QUERY_MODE_EXECUTE);
 
        return $primer; 
       
    }
    
      
    
     public function cantidad(){      
      $adapter = $this->tableGateway->getAdapter();
           $query='SELECT COUNT(DISTINCT(ta_plato.in_id )) AS NumeroResultados
                FROM ta_plato
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id`
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id`
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
               WHERE    ta_plato.en_estado=1 AND tr.en_estado=1 AND tr.va_nombre IS NOT NULL ';
           $cantidad = $this->tableGateway->getAdapter()
            ->query($query, $adapter::QUERY_MODE_EXECUTE); 
            return $cantidad->toArray();
      }
      
       public function minimomaximo(){      
      $adapter = $this->tableGateway->getAdapter();
           $query='SELECT MIN(DISTINCT(ta_plato.in_id )) AS minimo,MAX(DISTINCT(ta_plato.in_id )) AS maximo
                FROM ta_plato
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id`
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id`
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                WHERE  ta_plato.en_destaque=1 
                 AND ta_plato.en_estado=1  AND tr.en_estado=1';
           $cantidad = $this->tableGateway->getAdapter()
            ->query($query, $adapter::QUERY_MODE_EXECUTE); 
            return $cantidad->toArray();
      }
    public function platosParticipantes($destaque=1,$estado=1){
        
          $cantidad=$this->aleatorios($destaque, $estado, $puntaje='>=0',1);
          $total=$cantidad[0]['NumeroResultados'];
            if($total<=3) { 
                $aleatorio=0;
            }
            else {
                $aleatorio=rand(0,$total-3);
            }
        $limit=' LIMIT '.$aleatorio.',3';
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_plato')
                ->columns(array('*', 'NumeroComentarios' => new \Zend\Db\Sql\Expression('COUNT(ta_comentario.in_id)')))
                ->join('ta_comentario', 'ta_plato.in_id = ta_comentario.ta_plato_in_id', array(), 'left')
                ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id', array(), 'left')
                ->join('ta_plato_has_ta_local', 'ta_plato_has_ta_local.ta_plato_in_id = ta_plato.in_id', array(), 'left')
                ->join('ta_local', 'ta_local.in_id = ta_plato_has_ta_local.ta_local_in_id', array(), 'left')
                ->join('ta_ubigeo', 'ta_ubigeo.in_id = ta_local.ta_ubigeo_in_id', array('Distrito' => 'ch_distrito'), 'left')
                ->join('ta_restaurante', 'ta_local.ta_restaurante_in_id= ta_restaurante.in_id', array('restaurant_nombre'=>'va_nombre'), 'left')
                ->join('ta_plato_has_ta_tag', 'ta_plato.in_id = ta_plato_has_ta_tag.ta_plato_in_id', array(), 'left')
                ->join('ta_tag', 'ta_tag.in_id = ta_plato_has_ta_tag.ta_tag_in_id', array('tag'=>'va_nombre'), 'left')
                ->where('ta_tag.in_id=1 and ta_plato.en_estado=1 and (ta_comentario.en_estado=1 or ta_comentario.en_estado is null)')->group('ta_plato.in_id')->order('ta_plato.in_id DESC');//limit($aleatorio)->offset(3)                
//array('ta_tag.in_id'=>'1','ta_plato.en_estado'=>'activo','ta_comentario.en_estado'=>'aprobado')
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString.$limit, $adapter::QUERY_MODE_EXECUTE);
        return $results;
     
    }
    
    
     
    
    public function aleatorios($destaque,$estado,$puntaje,$promocion=null){      
           $adapter = $this->tableGateway->getAdapter();
           $query='SELECT COUNT(DISTINCT(ta_plato.in_id )) AS NumeroResultados
                FROM ta_plato
                LEFT JOIN  ta_comentario ON ta_plato.in_id = ta_comentario.ta_plato_in_id
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id`
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id`
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_ubigeo` AS `tu` ON `tu`.`in_id` = `tl`.`ta_ubigeo_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                LEFT JOIN `ta_plato_has_ta_tag` AS `ttag` ON `ta_plato`.`in_id` = `ttag`.`ta_plato_in_id`
                LEFT JOIN `ta_tag` AS `tag` ON `tag`.`in_id` = `ttag`.`ta_tag_in_id` where';
           if($promocion==1){
             $consulta=$query.' tag.in_id=1 and ta_plato.en_destaque=' . $destaque . ' and ta_plato.en_estado=' . $estado.' and (ta_comentario.en_estado=1 or ta_comentario.en_estado is null)';
          }else{
              $consulta=$query.' ta_plato.en_destaque=' . $destaque . ' and ta_plato.en_estado=' . $estado . '  and tr.va_nombre is not null and ta_plato.ta_puntaje_in_id ' . $puntaje.' and (ta_comentario.en_estado=1 or ta_comentario.en_estado is null)';
          } 
           $cantidad = $this->tableGateway->getAdapter()
            ->query($consulta, $adapter::QUERY_MODE_EXECUTE);
            
            return $cantidad->toArray();
    }
    
   public function promocion($id=null){
               $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_tag')->order('ta_tag.va_nombre asc');
       if($id!=null){
            $selecttot ->where(array('ta_tag.in_id='=>$id))->order('ta_tag.va_nombre DESC');       
       }
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }
    
    public function promocionxPlato($id){
    
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
                ->from('ta_plato_has_ta_tag')
                ->join('ta_tag', 'ta_tag.in_id = ta_plato_has_ta_tag.ta_tag_in_id', array(), 'left');
            $selecttot ->where(array('ta_plato_has_ta_tag.ta_plato_in_id'=>$id));       
   
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results;
    }

    public function cantComentarios($dest = 1, $lim) {

        $adapter = $this->tableGateway->getAdapter();
        $primer = $this->tableGateway->getAdapter()
                ->query('SELECT ta_plato.*,tr.va_nombre AS restaurant_nombre,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
                ta_comentario.ta_puntaje_in_id AS Puntaje,ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS Promedio
                FROM ta_plato
                LEFT JOIN  ta_comentario
                ON ta_plato.in_id = ta_comentario.ta_plato_in_id
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id` 
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id` 
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                where ta_plato.en_destaque=' . $dest . ' and ta_plato.en_estado=1 and tr.va_nombre is not null 
                GROUP BY va_nombre,in_id
                order by MAX(ta_comentario.ta_puntaje_in_id) DESC
                LIMIT ' . $lim, $adapter::QUERY_MODE_EXECUTE);
        return $primer;
    }

    public function distritosPlato() {

        $adapter = $this->tableGateway->getAdapter();
        $primer = $this->tableGateway->getAdapter()
                ->query("SELECT`ch_distrito` FROM ta_ubigeo WHERE ch_provincia ='lima'", $adapter::QUERY_MODE_EXECUTE);
        return $primer;
    }
 public function comen ($id)
         {
      $adapter2=$this->tableGateway->getAdapter();
        $promselect=$this->tableGateway->getAdapter()
                ->query('SELECT SUM(ta_puntaje_in_id)AS SumaPuntaje ,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
                    ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS TotPuntaje
                    FROM ta_comentario
                    where  ta_comentario.ta_plato_in_id='.$id, $adapter2::QUERY_MODE_EXECUTE);
                        $prom=$promselect->toArray();
              $update = $this->tableGateway->getSql()->update()->table('ta_plato')
                        ->set(array('Ta_puntaje_in_id'=>$prom[0]['TotPuntaje']))
                        ->where(array('in_id'=>$id));
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
           }
}
?>


