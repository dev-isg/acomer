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
    public function fetchAll($consulta=null){
        
        $sqlSelect = $this->tableGateway->getSql()->select();
        $sqlSelect->columns(array('in_id','va_nombre','va_precio','en_estado','en_destaque','Ta_puntaje_in_id'));
        $sqlSelect->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_va_nombre'=>'va_nombre'),'left');//, 'left'
        $sqlSelect->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array('ta_local_in_id'), 'left');
         $sqlSelect->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left');
         $sqlSelect->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurante_va_nombre'=>'va_nombre'), 'left');
         if($consulta!=null){
             $sqlSelect->where(array('pl.ta_local_in_id'=>$consulta));
         }
//             $selectString = $this->tableGateway->getSql()->getSqlStringForSqlObject($sqlSelect);
//             var_dump($selectString);exit;
          /*
           * con este es = q el siguiente pero en este no muestra los campos q no quieres imprimir
           * no hace uso de la estructura del tablegetway para hcer los joins
           */

//            $adapter=$this->tableGateway->getAdapter();
//            $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
           /*
            * muestra todos los campos, los q no especificaste a imprimer los imprime como null
            * para los alias es necesario ponerlos en clase de entidad sino no los imprime
            */
        $resultSet = $this->tableGateway->selectWith($sqlSelect);
        //$this->tableGateway->select($sqlSelect);
//            $array=array();
//             foreach($resultSet as $result){
//                 $array[]=$result;
//             }
//            var_dump($array);exit;
        return $resultSet;


    }
    /*
     * otra manera
     */
    /*
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
    
    */
    

    public function guardarPlato(Platos $plato,$imagen,$idlocal=null){


   
        
        $data = array(
//            'in_id' => $plato->in_id,
            'va_imagen' => $imagen['name'],//$plato->va_imagen,
            'tx_descripcion' => $plato->tx_descripcion,
            'va_nombre' => $plato->va_nombre,
            'va_precio' => $plato->va_precio,
            'en_destaque' => $plato->en_destaque,
            'en_estado' => $plato->en_estado,
            'Ta_tipo_plato_in_id' => $plato->Ta_tipo_plato_in_id,
            'Ta_puntaje_in_id' => $plato->Ta_puntaje_in_id,
            'Ta_usuario_in_id' => $plato->Ta_usuario_in_id,
        );

        foreach($data as $key=>$value){
            if(empty($value)){
                $data[$key]=1;
            }
        }
        $data['en_destaque']='si';
        $data['Ta_puntaje_in_id']=0;
//            print_r($data);exit;
        $id = (int) $plato->in_id;
          
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $idplato=$this->tableGateway->getLastInsertValue();
            
   
            $insert=$this->tableGateway->getSql()->insert()
            ->into('ta_plato_has_ta_local')
             ->values(array('Ta_plato_in_id'=>$idplato,'Ta_local_in_id'=>$idlocal));

            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insert);
            $statement->execute();
            
            
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
            ->from('ta_plato')
            ->join(array('c'=>'ta_comentario'),'c.ta_plato_in_id=ta_plato.in_id',array('cantidad' => new \Zend\Db\Sql\Expression('COUNT(*)'),'left'))
            ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_nombre'=>'va_nombre'),'left')
            ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
            ->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array('de_latitud','de_longitud','va_direccion'), 'left')
            ->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurant_nombre'=>'va_nombre'), 'left')
            ->join(array('tu'=>'ta_ubigeo'), 'tu.in_id = tl.ta_ubigeo_in_id', array('distrito'=>'ch_distrito'), 'left')
            ->where(array('ta_plato.in_id'=>$idplato)); 
   
            $selectString = $sql->getSqlStringForSqlObject($selecttot);
//            var_dump($selectString);Exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $plato=$results->toArray();
            //print_r($plato);exit;
            require './vendor/SolrPhpClient/Apache/Solr/Service.php';
                                $solr = new \Apache_Solr_Service('192.168.1.44', 8983, '/solr');  
                                           if ($solr->ping())
                                        {// echo 'entro';exit;
                                             $document = new \Apache_Solr_Document();
                                             $document->id = $plato[0]['in_id'];     
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
                                             $document->comentarios = '0';
                                             $document->puntuacion = '0';
                                             $solr->addDocument($document);
                                             $solr->commit();
                                             $solr->optimize();
                                        }

        } else {
            
            if ($this->getPlato($id)) {
//                var_dump($data);
//                echo '<br>';
//                var_dump($id);exit;
                $this->tableGateway->update($data, array('in_id' => $id));
//                $update=$this->tableGateway->getSql()->update('ta_plato_has_ta_local')
//                        ->set(array('ta_local_in_id'=>));
//                        ->where(array('ta_plato_in_id'=>$id));
//              $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);
//            $statement->execute();
            } else {
                throw new \Exception('No existe el id');
            }
        }
        
    }
    
    public function editarPlato($platos,$imagen,$idrestaurant=null){
        
//                var_dump($platos);exit;
        $data = array(
//            'in_id' => $plato->in_id,
            'va_imagen' => $imagen['name']='hola',//$plato->va_imagen,
            'tx_descripcion' => $platos["tx_descripcion"],
            'va_nombre' => $platos["va_nombre"],
            'va_precio' => $platos["va_precio"],
            'en_destaque' =>1,// $plato->en_destaque,
            'en_estado' => 1,//$plato->en_estado,
            'Ta_tipo_plato_in_id' => $platos["Ta_tipo_plato_in_id"],
            'Ta_puntaje_in_id' => 1,//$plato->Ta_puntaje_in_id,
            'Ta_usuario_in_id' => 1,//$plato->Ta_usuario_in_id,
        );
        $id=$platos["in_id"];
//        var_dump($platos["in_id"]);exit;
        $this->tableGateway->update($data, array('in_id' => $id));
        
    }
    
    
        public function eliminarPlato($id,$estado)
    {

                          $data = array(
                    'en_estado' => $estado,
                 );
         $this->tableGateway->update($data, array('in_id' => $id));
         
         //ya no se va borrar
//            $delete=$this->tableGateway->getSql()->delete()->from('ta_plato_has_ta_local')
//        ->where(array('ta_plato_in_id'=>$id));
//            $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($delete);
//            $statement->execute();
//                    
//        $this->tableGateway->delete(array('in_id' => $id));
//
//        $delete2=$this->tableGateway->getSql()->delete()->from('ta_comentario')
//        ->where(array('ta_plato_in_id'=>$id));
//        $statement2 = $this->tableGateway->getSql()->prepareStatementForSqlObject($delete2);
//        $statement2->execute();
    }
    
    /*
     * update a un unico campo el destaque
     */
        public function destaquePlato($id,$destaque){

                $data = array(
                    'en_destaque' => $destaque,
                 );
         $this->tableGateway->update($data, array('in_id' => $id));
         
//                     var_dump($id);
//            var_dump($destaque);exit;
    }

    /*
     * @return  un row de un plato
     */
         public function getPlato($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('in_id' => $id));
        $row = $rowset->current();
//        VAR_DUMP($row);EXIT;
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    /*
     *plato x restaurante 
     */
        public function getPlatoxRestaurant($idplato){
                    $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
            ->from('ta_plato')
            ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array('tipo_plato_nombre'=>'va_nombre'),'left')
            ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
            ->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array('de_latitud','de_longitud','va_direccion','va_horario','va_dia','va_telefono'), 'left')
            ->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array('restaurant_id'=>'in_id','restaurant_nombre'=>'va_nombre','restaurant_img'=>'va_imagen'), 'left')
            ->join(array('tu'=>'ta_ubigeo'), 'tu.in_id = tl.ta_ubigeo_in_id', array('pais'=>'ch_pais','departamento'=>'ch_departamento','provincia'=>'ch_provincia','distrito'=>'ch_distrito'), 'left')
//            ->join(array('tc'=>'ta_comentario'),'tc.ta_plato_in_id=ta_plato.in_id',array('TotPuntaje'=>'ROUND(AVG(tc.ta_puntaje_in_id))'),'left')
//            ->join(array('tcli'=>'ta_cliente'),'tcli.in_id=tc.ta_cliente_in_id',array('va_nombre_cliente','va_email'),'left')
            ->where(array('ta_plato.in_id'=>$idplato)); 
   
            $selectString = $sql->getSqlStringForSqlObject($selecttot);
//            var_dump($selectString);Exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
          
            return $results;//->toArray();
            
        }
        public function getPagoxPlato($idplato){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $selecttot = $sql->select()
            ->from('ta_plato')
            ->columns(array())
            ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
            ->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left')
            ->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array(), 'left')
            ->join(array('rp'=>'ta_restaurante_has_ta_medio_pago'), 'rp.ta_restaurante_in_id= tr.in_id', array(), 'left')
            ->join(array('mp'=>'ta_medio_pago'), 'rp.ta_medio_pago_in_id= mp.in_id', array('id_pago'=>'in_id','nom_pago'=>'va_nombre'), 'left')
           ->where(array('ta_plato.in_id'=>$idplato)); 
            $selectString = $sql->getSqlStringForSqlObject($selecttot);
          
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
                    
         }
         
            public function getServicioxPlato($idplato){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $selecttot = $sql->select()
            ->from('ta_plato')
            ->columns(array('in_id'))
            ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
            ->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left')
            ->join(array('ls'=>'ta_local_has_ta_servicio_local'), 'tl.in_id= ls.ta_local_in_id', array(), 'left')
            ->join(array('s'=>'ta_servicio_local'), 'ls.ta_servicio_local_in_id= s.in_id', array('id_servicio'=>'in_id','nom_servicio'=>'va_nombre'), 'left')
           ->where(array('ta_plato.in_id'=>$idplato)); 
            $selectString = $sql->getSqlStringForSqlObject($selecttot);
//            var_dump($selectString);Exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
                    
         }
         
         public function getLocalesxRestaurante($idrest){
           $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $selecttot = $sql->select()
            ->from('ta_local')
            ->columns(array('in_id','va_telefono','de_latitud','de_longitud','va_direccion'))
            ->join(array('tu'=>'ta_ubigeo'), 'ta_local.ta_ubigeo_in_id = tu.in_id', array('distrito'=>'ch_distrito'), 'left')
            ->where(array('ta_restaurante_in_id'=>$idrest)); 
            $selectString = $sql->getSqlStringForSqlObject($selecttot);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            return $results;
         }
        
        public function getComentariosxPlatos($idplato){
                                $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
            ->from('ta_plato')
//            ->columns(array('num' => new \Zend\Db\Sql\Expression('COUNT(*)')))
//            ->join('ta_tipo_plato', 'ta_plato.ta_tipo_plato_in_id=ta_tipo_plato.in_id ', array(),'left')
//            ->join(array('pl'=>'ta_plato_has_ta_local'), 'pl.ta_plato_in_id = ta_plato.in_id', array(), 'left')
//            ->join(array('tl'=>'ta_local'), 'tl.in_id = pl.ta_local_in_id', array(), 'left')
//            ->join(array('tr'=>'ta_restaurante'), 'tr.in_id = tl.ta_restaurante_in_id', array(), 'left')
//            ->join(array('tu'=>'ta_ubigeo'), 'tu.in_id = tl.ta_ubigeo_in_id', array(), 'left')
            ->join(array('tc'=>'ta_comentario'),'tc.ta_plato_in_id=ta_plato.in_id',array('tx_descripcion','ta_puntaje_in_id'),'left')
            ->join(array('tcli'=>'ta_cliente'),'tcli.in_id=tc.ta_cliente_in_id',array('va_nombre_cliente','va_email'),'left')

            ->where(array('ta_plato.in_id'=>$idplato)); 
   
            $selectString = $sql->getSqlStringForSqlObject($selecttot);
//            var_dump($selectString);Exit;
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
          
            return $results;
        }
    
        
        public function comentarios($dest=1,$lim,$val){
      if($val==1){
          $res='is not null';//'is not null or ta_comentario.ta_puntaje_in_id!=0'; 
          } else if ($val==2){
              $res='is null';//'is null or ta_comentario.ta_puntaje_in_id!=0';
              }
              else if ($val==3){
              $res='is null';//'is null or ta_comentario.ta_puntaje_in_id=0';    
              }

        $adapter=$this->tableGateway->getAdapter();
        $primer=$this->tableGateway->getAdapter()
                ->query('SELECT ta_plato.*,tr.va_nombre AS restaurant_nombre,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
        ta_comentario.ta_puntaje_in_id AS Puntaje,ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS Promedio
        FROM ta_plato
        LEFT JOIN  ta_comentario
        ON ta_plato.in_id = ta_comentario.ta_plato_in_id
        LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id` 
        LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
        LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id` 
        LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
        where ta_plato.en_destaque='.$dest.' and ta_plato.en_estado=1 and tr.va_nombre is not null and (ta_comentario.ta_puntaje_in_id '.$res.')
        GROUP BY va_nombre,in_id
        order by MAX(ta_comentario.ta_puntaje_in_id) DESC
        LIMIT '.$lim, $adapter::QUERY_MODE_EXECUTE);

       return $primer;
        
    }
    public function cantComentxPlato($dest=1,$lim,$val){
      if($val==1){
          $res='is not null';//'is not null or ta_comentario.ta_puntaje_in_id!=0'; 
          } else if ($val==2){
              $res='is null';//'is null or ta_comentario.ta_puntaje_in_id!=0';
              }
              else if ($val==3){
              $res='is null';//'is null or ta_comentario.ta_puntaje_in_id=0';    
              }
        
//               $select=$this->tableGateway->getSql()->select()
//               ->columns(array('va_nombre','in_id'))
//             ->join('ta_comentario','ta_plato.in_id = ta_comentario.ta_plato_in_id',array('num_comenta' => new \Zend\Db\Sql\Expression('COUNT(ta_comentario.in_id)'),'ta_puntaje_in_id'))
//                       //
//                ->group(array('va_nombre','in_id')); 
//            $selectString = $this->getSql()->getSqlStringForSqlObject($select);
//            $adapter=$this->getAdapter();
//            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//
//       return $results->toArray();
        $adapter=$this->tableGateway->getAdapter();
        $primer=$this->tableGateway->getAdapter()
                ->query('SELECT ta_plato.*,tr.va_nombre AS restaurant_nombre,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
ta_comentario.ta_puntaje_in_id AS Puntaje,ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS Promedio
FROM ta_plato
LEFT JOIN  ta_comentario
ON ta_plato.in_id = ta_comentario.ta_plato_in_id
LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id` 
LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id` 
LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
where ta_plato.en_destaque='.$dest.' and ta_plato.en_estado=1 and tr.va_nombre is not null and (ta_comentario.ta_puntaje_in_id '.$res.')
GROUP BY va_nombre,in_id
order by MAX(ta_comentario.ta_puntaje_in_id) DESC
LIMIT '.$lim, $adapter::QUERY_MODE_EXECUTE);
        
//        print_r($primer->toArray());Exit;
//        $aux=array();
//        foreach($primer as $value){
//            $aux[]=$value;
//        }
//        var_dump($aux);exit;
       return $primer;//->toArray();//$data;// $aux;//select()->from('usuario')->query()->fetchAll();
        
    }
    
     public function cantComentarios($dest=1,$lim){
 
        $adapter=$this->tableGateway->getAdapter();
        $primer=$this->tableGateway->getAdapter()
                ->query('SELECT ta_plato.*,tr.va_nombre AS restaurant_nombre,COUNT(ta_comentario.in_id ) AS NumeroComentarios,
                ta_comentario.ta_puntaje_in_id AS Puntaje,ROUND(AVG(ta_comentario.ta_puntaje_in_id)) AS Promedio
                FROM ta_plato
                LEFT JOIN  ta_comentario
                ON ta_plato.in_id = ta_comentario.ta_plato_in_id
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id` 
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id` 
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                where ta_plato.en_destaque='.$dest.' and ta_plato.en_estado=1 and tr.va_nombre is not null 
                GROUP BY va_nombre,in_id
                order by MAX(ta_comentario.ta_puntaje_in_id) DESC
                LIMIT '.$lim, $adapter::QUERY_MODE_EXECUTE);
       return $primer;
        
    }
    
     public function distritosPlato(){

        $adapter=$this->tableGateway->getAdapter();
        $primer=$this->tableGateway->getAdapter()
                ->query("SELECT`ch_distrito` FROM ta_ubigeo WHERE ch_provincia ='lima'", $adapter::QUERY_MODE_EXECUTE);
       return $primer;
    }
    
}

?>


