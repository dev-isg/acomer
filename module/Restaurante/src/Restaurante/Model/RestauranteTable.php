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
        $this->_options = new \Zend\Config\Config ( include APPLICATION_PATH . '/config/autoload/global.php' );
    }
   

    public function fetchAll()
    {

      $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('f' => 'ta_restaurante'))
                ->join(array('b' => 'ta_tipo_comida'), 'f.ta_tipo_comida_in_id=b.in_id', array('va_nombre_tipo'))//,array('va_nombre_rol'))
              ->where(array('f.ta_tipo_comida_in_id=b.in_id','f.en_estado'=>'activo'))
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
      public function getRestauranteNombre($id)
    {
         $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
               ->from(array('f' => 'ta_restaurante'))
               ->where(array('f.va_nombre'=>$id));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current();
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

     public function buscarRestaurante($datos=null,$comida=null,$estado=null){
        $adapter=$this->tableGateway->getAdapter();
           $sql = new Sql($adapter);
        
           if($comida=='' and $estado == ''){
          
             $select = $sql->select()
            ->from(array('f' => 'ta_restaurante')) 
            ->join(array('b' => 'ta_tipo_comida'),'f.ta_tipo_comida_in_id = b.in_id',array('va_nombre_tipo'))
           ->where(array('f.va_nombre LIKE ?'=>'%'.$datos.'%'));
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
            ->where(array('f.ta_tipo_comida_in_id'=>$comida,'f.en_estado'=>$estado,'f.va_nombre LIKE ?'=>'%'.$datos.'%'));
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

    
    public function ubigeototal()
     {
      $datos=$this->tableGateway->getAdapter()->query("
    SELECT `ta_ubigeo`.`ch_distrito` AS `ch_distrito`, `ta_ubigeo`.`ch_provincia` AS `ch_provincia`, `ta_ubigeo`.`ch_departamento` AS `ch_departamento` FROM `ta_ubigeo` LEFT JOIN `ta_local` ON `ta_ubigeo`.`in_id` = `ta_local`.`ta_ubigeo_in_id`
     WHERE ta_local.ta_ubigeo_in_id!=''  OR `ta_ubigeo`.`ch_distrito`='LIMA' GROUP BY `ta_ubigeo`.`ch_distrito`")->execute();
                $returnArray=array();
        foreach ($datos as $result) {
            if($result['ch_distrito']=='LIMA')
            {$returnArray[] = $result['ch_distrito'];
        }else{$returnArray[] = $result['ch_distrito'].','.ucwords(strtolower($result['ch_provincia'])).','.ucwords(strtolower($result['ch_departamento']));
        }
            }
        return  $returnArray;  
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
 INNER JOIN `ta_medio_pago` AS `b` ON `f`.`Ta_medio_pago_in_id` = `b`.`in_id` WHERE `f`.`Ta_restaurante_in_id` = $id ")->execute();
                $returnArray=array();
        foreach ($datos as $result) {
            $returnArray[] = $result;
        }
        
        return  $returnArray;
        
    }
    

    
       public function ubigeototal2($id)
     {
      $datos=$this->tableGateway->getAdapter()->query("
    SELECT `ta_ubigeo`.`ch_distrito` AS `ch_distrito` FROM `ta_ubigeo` LEFT JOIN `ta_local` ON `ta_ubigeo`.`in_id` = `ta_local`.`ta_ubigeo_in_id`
     WHERE ta_local.ta_ubigeo_in_id!='' AND `ta_ubigeo`.`ch_distrito` LIKE '%$id%' ")->execute();
         
        return  $datos;  
  }
    public function guardarMenu($menu,$imagen)
    {
         $data = array(
            'va_nombre' => $menu->va_nombre, 
            'va_url' => $menu->va_url,
               'in_orden' => $menu->in_orden,
             'va_imagen'=>$imagen,
             'en_estado'=>'activo'   
        );
              $adapter = $this->tableGateway->getAdapter();
              $sql = new Sql($adapter);
              $selecttot = $sql->insert()
                      ->into('ta_menu')
                      ->values($data);
              $selectString = $sql->getSqlStringForSqlObject($selecttot);
            $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    return $adapter;
    }
        public function guardarBanner($banner,$imagen)
    {
         $data = array(
            'va_nombre' => $banner->va_nombre, 
            'va_imagen' => $imagen,
               'in_orden' => $banner->in_orden,
             'va_url' => $banner->va_url,
             'en_estado'=>'activo'
        );
              $adapter = $this->tableGateway->getAdapter();
              $sql = new Sql($adapter);
              $selecttot = $sql->insert()
                      ->into('ta_banner')
                      ->values($data);
              $selectString = $sql->getSqlStringForSqlObject($selecttot);
            $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    return $adapter;
    }
    public function guardarTag($tag,$imagen)
    {
         $data = array(
            'va_nombre' => $tag->va_nombre, 
            'va_imagen' => $imagen,
        );
              $adapter = $this->tableGateway->getAdapter();
              $sql = new Sql($adapter);
              $selecttot = $sql->insert()
                      ->into('ta_tag')
                      ->values($data);
              $selectString = $sql->getSqlStringForSqlObject($selecttot);
            $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    return $adapter;
    }
     public function estadomenu($id,$estado){
                $data = array(
            'en_estado' => $estado );    
        $update = $this->tableGateway->getSql()->update()->table('ta_menu')
                        ->set($data)
                        ->where(array('in_id'=>$id));
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
    }
    
    
    
    
      public function eliminarmenu($id){    
          $borrar = $this->tableGateway->getSql()->delete()->from('ta_menu')
                                ->where(array('in_id'=>$id));
                        $selectStri = $this->tableGateway->getSql()->getSqlStringForSqlObject($borrar);
                        $adapter=$this->tableGateway->getAdapter();
                        $result = $adapter->query($selectStri, $adapter::QUERY_MODE_EXECUTE); 
                        return $result;
    }
    
   public function eliminarbanner($id){    
          $borrar = $this->tableGateway->getSql()->delete()->from('ta_banner')
                                ->where(array('in_id'=>$id));
                        $selectStri = $this->tableGateway->getSql()->getSqlStringForSqlObject($borrar);
                        $adapter=$this->tableGateway->getAdapter();
                        $result = $adapter->query($selectStri, $adapter::QUERY_MODE_EXECUTE); 
                        return $result;
    } 

   public function editaMenu($menu,$imagen)
    {
       $data = array(
            'va_nombre' => $menu->va_nombre, 
            'va_url' => $menu->va_url,
           'va_imagen'=>$imagen,
           'in_orden' => $menu->in_orden  );
        $update = $this->tableGateway->getSql()->update()->table('ta_menu')
                        ->set($data)
                        ->where(array('in_id'=>$menu->in_id));
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
    }
       public function buscarMenu($id)
    {
      $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);      
          $idubigeo=$sql->select()->from('ta_menu')
                 ->where(array('in_id'=>$id));
          $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);
            $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);     
            return $result;
    }
    
       public function buscarBanner($id)
    {
      $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);      
          $idubigeo=$sql->select()->from('ta_banner')
                 ->where(array('in_id'=>$id));
          $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);
            $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);     
            return $result;
    }
     
      public function editaBanner($banner,$imagen)
    {
       $data = array(
            'va_nombre' => $banner->va_nombre, 
            'va_imagen' => $imagen,
           'in_orden' => $banner->in_orden,
            'va_url' => $banner->va_url );
        $update = $this->tableGateway->getSql()->update()->table('ta_banner')
                        ->set($data)
                        ->where(array('in_id'=>$banner->in_id));//$prom[0]['in_id']
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
    }
      public function editartag($tag,$imagen)
    {
       $data = array(
            'va_nombre' => $tag->va_nombre, 
            'va_imagen' => $imagen );
        $update = $this->tableGateway->getSql()->update()->table('ta_tag')
                        ->set($data)
                        ->where(array('in_id'=>$tag->in_id));//$prom[0]['in_id']
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
    }
         public function listarmenu(){   
       $adapter=$this->tableGateway->getAdapter();
       $sql = new Sql($adapter);      
          $idubigeo=$sql->select()->from('ta_menu');
           $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);
            $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);
            return $result->buffer();
    }
       public function listarRegistro($id=null){   
       $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
               ->from(array('f' => 'ta_registro'))
                ->join(array('b' => 'ta_tipo_comida'), 'f.ta_tipo_comida_in_id=b.in_id', array('va_nombre_tipo'));
            if($id!=null)
            {$select->where(array('f.ta_tipo_comida_in_id=b.in_id','f.in_id='.$id));}
            else{$select->where(array('f.ta_tipo_comida_in_id=b.in_id'));}
              $select->order('in_id DESC');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet->buffer();
    }
     public function listarRegistroPlatos($id)
    {   
         $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);      
             $idubigeo=$sql->select()->from('ta_registroplato')
                     ->where(array('Ta_registro_in_id'=>$id));
             $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);
             $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);
             return $result;
    }
    
    public function comentariosPlatos($idplato) {
         $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()->columns(array())
                ->from(array('t' => 'ta_plato'))
                ->join(array('tc' => 'ta_comentario'), 'tc.ta_plato_in_id=t.in_id', array('tx_descripcion', 'ta_puntaje_in_id'), 'left')
                ->join(array('tcli' => 'ta_cliente'), 'tcli.in_id=tc.ta_cliente_in_id', array('va_nombre_cliente', 'va_email'), 'left')
                ->where(array('t.in_id' => $idplato,'tc.en_estado'=>'aprobado'))
                ->order('tc.in_id DESC');
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $results->toArray();
    }
      
     public function estadoRegistro($id,$estado){
            $data = array(
            'en_estado' => $estado );    
            $update = $this->tableGateway->getSql()->update()->table('ta_registro')
                        ->set($data)
                        ->where(array('in_id'=>$id));
                $statementup = $this->tableGateway->getSql()->prepareStatementForSqlObject($update);  
                $statementup->execute();
          
    }
     public function listarbanner()
    {    
            $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);      
             $idubigeo=$sql->select()->from('ta_banner');
             $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);
             $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);
             return $result->buffer();
    }
     public function listartag()
    {    
            $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);      
             $idubigeo=$sql->select()->from('ta_tag');
             $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);
             $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);
             return $result->buffer();
    }
     public function buscarTag($id)
    {
      $adapter=$this->tableGateway->getAdapter();
             $sql = new Sql($adapter);      
          $idubigeo=$sql->select()->from('ta_tag')
                 ->where(array('in_id'=>$id));
          $selectString0 = $this->tableGateway->getSql()->getSqlStringForSqlObject($idubigeo);
            $result = $adapter->query($selectString0, $adapter::QUERY_MODE_EXECUTE);     
            return $result;
    }
    
         public function guardarRestauranteRegistro($restaurante,$platos,$tipoplato)
      {
         $data = array(
            'va_nombre' => $restaurante[0]['va_nombre_restaurante'], 
            'va_razon_social' => $restaurante[0]['va_nombre_restaurante'], 
            'va_imagen' => $restaurante[0]['va_imagen'],
            'Ta_tipo_comida_in_id' => $restaurante[0]['Ta_tipo_comida_in_id'],
             'en_estado'=>'desactivo' );
              $adapter = $this->tableGateway->getAdapter();
              $sql = new Sql($adapter);
              $selecttot = $sql->insert()
                      ->into('ta_restaurante')
                      ->values($data);
                  $selectString = $sql->getSqlStringForSqlObject($selecttot);
                     $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                     $idrestaurante=$this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();    
             $original = $this->_options->upload->images . '/registro/restaurante/' . $restaurante[0]['va_imagen']; 
             $nuevo = $this->_options->upload->images . '/restaurante/principal/' . $restaurante[0]['va_imagen']; 
             $origen = $this->_options->upload->images . '/restaurante/original/' . $restaurante[0]['va_imagen'];
               copy($original, $nuevo);
               copy($original, $origen);
                            $dataubigeo = array(
                                  'va_telefono' => $restaurante[0]['va_telefono'], 
                                  'va_horario' => $restaurante[0]['va_horario'], 
                                  'va_direccion' => $restaurante[0]['va_direccion'],
                                  'Ta_restaurante_in_id' => $idrestaurante,
                                  'Ta_ubigeo_in_id' => '1246',
                                  'va_email' => $restaurante[0]['va_correo']);
                                   $adapte = $this->tableGateway->getAdapter();
                                   $sq = new Sql($adapte);
                                   $selectto = $sq->insert()
                                            ->into('ta_local')
                                            ->values($dataubigeo);
                                  $selectStrin = $sql->getSqlStringForSqlObject($selectto);
                                  $adapter->query($selectStrin, $adapte::QUERY_MODE_EXECUTE);
                                  $idlocal=$this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();                                  
           mkdir($this->_options->upload->images . '/plato/principal/'.$idrestaurante.'/' , 0777); 
           mkdir($this->_options->upload->images . '/plato/general/'.$idrestaurante.'/' , 0777);
           mkdir($this->_options->upload->images . '/plato/destacado/'.$idrestaurante.'/' , 0777);
           mkdir($this->_options->upload->images . '/plato/original/'.$idrestaurante.'/' , 0777);
              mkdir($this->_options->upload->images . '/plato/principal/'.$idrestaurante.'/'.$idlocal.'/' , 0777); 
              mkdir($this->_options->upload->images . '/plato/general/'.$idrestaurante.'/'.$idlocal.'/' , 0777);
              mkdir($this->_options->upload->images . '/plato/destacado/'.$idrestaurante.'/'.$idlocal.'/' , 0777);
              mkdir($this->_options->upload->images . '/plato/original/'.$idrestaurante.'/'.$idlocal.'/' , 0777); 
             $array=array();
             foreach($platos as $result){
                 $array[]=$result;}
            for($i=0;$i<count($platos);$i++)
            { $dataplato = array(
               'va_nombre' => $array[$i]['va_nombre_plato'], 
               'va_imagen' => $idrestaurante.'/'.$idlocal.'/'.$array[$i]['va_imagen'], 
               'en_destaque' =>2,
               'va_precio' => $array[$i]['va_precio'],
               'tx_descripcion'=>$array[$i]['va_descripcion'],
               'en_estado' =>2,    
               'Ta_tipo_plato_in_id'  =>$tipoplato,
               'Ta_puntaje_in_id'=>0,
               'Ta_usuario_in_id'=>133); 
                  $adapt = $this->tableGateway->getAdapter();
                   $s = new Sql($adapt);
                 $selectt = $s->insert()
                ->into('ta_plato')
              ->values($dataplato);
             $selectStri = $sql->getSqlStringForSqlObject($selectt);
             $adapter->query($selectStri, $adapt::QUERY_MODE_EXECUTE);
              $idplato=$this->tableGateway->getAdapter()->getDriver()->getConnection()->getLastGeneratedValue();
                $original = $this->_options->upload->images . '/registro/plato/' . $array[$i]['va_imagen'];  
                $principal = $this->_options->upload->images . '/plato/principal/'.$idrestaurante.'/'.$idlocal.'/' . $array[$i]['va_imagen'];
                $destacado = $this->_options->upload->images . '/plato/destacado/'.$idrestaurante.'/'.$idlocal.'/' . $array[$i]['va_imagen'];
                $general = $this->_options->upload->images . '/plato/general/'.$idrestaurante.'/'.$idlocal.'/' . $array[$i]['va_imagen'];
                $origen = $this->_options->upload->images .  '/plato/original/'.$idrestaurante.'/'.$idlocal.'/' . $array[$i]['va_imagen'];
               copy($original, $principal);
               copy($original, $destacado);
               copy($original, $general);
               copy($original, $origen);}     
            for($i=0;$i<count($platos);$i++)
            {$insertar = $this->tableGateway->getSql()->insert()->into('ta_plato_has_ta_local')
                                       ->values(array('Ta_local_in_id'=>$idlocal,'Ta_plato_in_id'=>($idplato-$i)));
                               $selectString3 = $this->tableGateway->getSql()->getSqlStringForSqlObject($insertar);
                               $adapter=$this->tableGateway->getAdapter();
                               $result = $adapter->query($selectString3, $adapter::QUERY_MODE_EXECUTE);
              $this->getPlatosTable()->cromSolr(($idplato-$i),'');}
                   
     }
    
    public function tipodeplato($id)
    {    
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()
               ->from(array('f' => 'ta_tipo_plato'))
                ->join(array('b' => 'ta_tipo_comida'), 'f.Ta_tipo_comida_in_id=b.in_id', array('comida'=>'in_id'))
               ->where(array('b.in_id'=>$id,'f.va_nombre'=>'otros'));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet;
    }
    
      public function eliminarRegistroTotales($id){    
          $borrar = $this->tableGateway->getSql()->delete()->from('ta_registro')
                                ->where(array('in_id'=>$id));
                        $selectStri = $this->tableGateway->getSql()->getSqlStringForSqlObject($borrar);
                        $adapter=$this->tableGateway->getAdapter();
                        $result = $adapter->query($selectStri, $adapter::QUERY_MODE_EXECUTE); 
                        return $result;
    }
    
    public function eliminarRegistroTotalPlatos($platosid){ 
                   $borrar = $this->tableGateway->getSql()->delete()->from('ta_registroplato')
                                ->where(array('in_id'=>$platosid));
                        $selectStri = $this->tableGateway->getSql()->getSqlStringForSqlObject($borrar);
                        $adapter=$this->tableGateway->getAdapter();
                        $result = $adapter->query($selectStri, $adapter::QUERY_MODE_EXECUTE); 
                        return $result; 
               }
               
               
      public function menusMovil()
       {
             $datos=$this->tableGateway->getAdapter()
                     ->query("SELECT va_nombre,va_imagen,va_url  FROM `ta_menu` WHERE in_id!=10 AND en_estado=1 ORDER BY in_orden ASC ")
                     ->execute();
             $returnArray=array();
             foreach ($datos as $result) 
              {$returnArray[] = $result;}
               return  $returnArray;
       }
    
      public function comidasMovil()
        {
          $datos=$this->tableGateway->getAdapter()
               ->query("SELECT tag.in_id AS id_tag,tag.va_imagen AS imagen,COUNT(ttag.ta_plato_in_id) AS NumeroResultados ,tag.va_nombre AS nombre_tag
                FROM ta_plato
                LEFT JOIN `ta_plato_has_ta_tag` AS `ttag` ON `ta_plato`.`in_id` = `ttag`.`ta_plato_in_id`
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id`
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                LEFT JOIN `ta_tag` AS `tag` ON `tag`.`in_id` = `ttag`.`ta_tag_in_id` WHERE  ta_plato.en_estado=1 AND tr.en_estado=1  AND tag.va_nombre IS NOT NULL  
                 GROUP BY tag.in_id ORDER BY tag.va_nombre ASC")
                 ->execute();
                 $returnArray=array();
                  foreach ($datos as $result)
                  {$returnArray[] = $result;}
                  return  $returnArray;    
         }
        
     public function platosMovil($id)
       {
          $datos=$this->tableGateway->getAdapter()
                ->query("SELECT ta_plato.*,ta_plato.Ta_tipo_plato_in_id AS Comentarios,tr.va_nombre AS restaurant_nombre ,COUNT(ta_comentario.in_id ) AS NumeroComentarios
                ,tu.ch_distrito AS Distrito,tu.ch_departamento AS Departamento,tl.va_telefono AS telefono,tl.va_direccion AS direccion
                FROM ta_plato
                LEFT JOIN  ta_comentario ON ta_plato.in_id = ta_comentario.ta_plato_in_id
                LEFT JOIN `ta_tipo_plato` ON `ta_plato`.`ta_tipo_plato_in_id`=`ta_tipo_plato`.`in_id`
                LEFT JOIN `ta_plato_has_ta_local` AS `pl` ON `pl`.`ta_plato_in_id` = `ta_plato`.`in_id` 
                LEFT JOIN `ta_local` AS `tl` ON `tl`.`in_id` = `pl`.`ta_local_in_id`
                LEFT JOIN `ta_ubigeo` AS `tu` ON `tu`.`in_id` = `tl`.`ta_ubigeo_in_id`
                LEFT JOIN `ta_restaurante` AS `tr` ON `tr`.`in_id` = `tl`.`ta_restaurante_in_id`
                WHERE  ta_plato.en_estado=1  AND tr.va_nombre IS NOT NULL  AND ta_plato.in_id=$id AND tr.en_estado=1
                GROUP BY in_id  ")
                ->execute();
                $returnArray=array();
                foreach ($datos as $result) 
                {$returnArray[] = $result; }
              return  $returnArray;
      }
    

}