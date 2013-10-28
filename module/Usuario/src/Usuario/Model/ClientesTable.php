<?php
namespace Usuario\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Usuario\Controller\ClientesController ;
use Usuario\Model\ComentariosTable ;
use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Platform;
use SanAuth\Controller\AuthController; 



class ClientesTable
{
    protected $tableGateway;
     public $in_id;
    public $va_nombre_cliente;
    public $va_email;
    public $va_contrasena;
    public $en_estado;
    public $id_facebook;
    public $va_notificacion;
    public $va_logout;
    public $va_fecha_ingreso;
    public $va_recupera_contrasena;
    public $va_fecha_exp;
    public $va_verificacion;
    

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
      
    }
    public function generarPassword($correo)
    {
        $mail = $this->getUsuarioxEmail($correo);
        $expFormat = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 3, date("Y"));
        $expDate = date("Y-m-d H:i:s", $expFormat);
        $idgenerada = sha1(uniqid($mail->in_id . substr($mail->va_nombre_cliente, 0, 8) . substr($mail->va_email, 0, 8).date("Y-m-d H:i:s"), 0));
        $data = array(
            'va_recupera_contrasena' => $idgenerada,
            'va_fecha_exp'=>$expDate
        );
        $this->tableGateway->update($data, array(
            'in_id' => $mail->in_id
        ));
        
        if (! $idgenerada) {
            throw new \Exception("No se puede generar password $idgenerada");
        }
        return $idgenerada;
    }
      public function cambiarPassword($password, $iduser) {
        $data = array(
            'va_contrasena' => sha1($password),
            'va_recupera_contrasena'=>''
        );

        $actualiza = $this->tableGateway->getSql()->update()->table('ta_cliente')
                ->set($data)
                ->where(array('in_id' => $iduser));
        $selectStringNotifca = $this->tableGateway->getSql()->getSqlStringForSqlObject($actualiza);
        $adapter1 = $this->tableGateway->getAdapter();
        $row = $adapter1->query($selectStringNotifca, $adapter1::QUERY_MODE_EXECUTE);

        if (!$row) {
            return false;
        }
        $this->eliminaPass($iduser);
        return true;
    }
     public function eliminaPass($iduser)
    {
        $data = array(
            'va_recupera_contrasena' => null
        );
        $this->tableGateway->update($data,array('in_id'=>$iduser));
    }

    public function getUsuarioxEmail($email)
    {
        $row = $this->tableGateway->select(array(
            'va_email' => $email
        ));
        $resul = $row->current();
        
        if (! $resul) {
            throw new \Exception("Could not find row $email");
        }
        return $resul;
    }
    
      public function consultarPassword($password)
    {
        $curDate = date("Y-m-d H:i:s");
        $row = $this->tableGateway->select(array(
            'va_recupera_contrasena' => $password,
//             'va_fecha_exp'=>$curDate
        ));
        $resul = $row->current();
        
//        if (! $resul) {
//            throw new \Exception("Could not find row $password");
//        }
        return $resul;
    }
      public function verificaCorreo($correo)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()->from('ta_cliente')
                ->where(array('va_email'=>$correo));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current();
    }
      public function usuario1($correo)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()->from('ta_cliente')
                ->where(array('va_email'=>$correo));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet->toArray();
    }
    
     public function cambiarestado($id)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->update('ta_cliente')
                ->set(array('va_verificacion'=>'','en_estado'=>'activo'))
                ->where(array('in_id'=>$id));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
                   $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
    
    
    
   
    
    
//       public function usuario25($correo) 
//    { 
//        $sqlSelect = $this->tableGateway->getSql() 
//                          ->select()->columns(array('in_id', 'va_nombre_cliente', 'va_email',
//                              'va_contrasena','en_estado'))
//                ->where(array('va_email'=>$correo)); 
//        
//        return $this->tableGateway->select($sqlSelect); 
//    }
    public function guardarClientes(Clientes $clientes,$valor=null)
    {  
        $data = array(
            'va_nombre_cliente' => $clientes->va_nombre_cliente,
            'va_email' => $clientes->va_email,
            'va_contrasena' => sha1($clientes->va_contrasena),
            'va_verificacion' => $valor,  
            'va_notificacion' => $clientes->va_notificacion,
            'en_estado' =>'desactivo',   
                );
        $id = (int) $clientes->in_id;
     
        foreach($data as $key=>$value){
           if(empty($value)){
               $data[$key]=0;
           }
       }
        if ($id == 0) { 
            $data['va_fecha_ingreso'] = date("Y-m-d H:i:s");
           $clientes = $this->tableGateway->insert($data);
        } else { 
            if ($this->getUsuario($id)) {
                 $this->updateCategoria($catg_ingresada, $id);
                if ($pass == '') {
                    $data['va_estado'] = 'activo';
                    $data['va_verificacion'] = '';
                    $this->tableGateway->update($data, array(
                        'in_id' => $id));
                } else {
                       $data['va_pais'] = $usuario->va_pais;
                   $data['ta_ubigeo_in_id']=$ciudad;
                    $data['va_contrasena'] = $pass;
                    $data['va_verificacion'] = '';
                    $data['va_estado'] = 'activo';

                    $this->tableGateway->update($data, array(
                        'in_id' => $id));
                }
            } else {
                throw new \Exception('no existe el usuario');
            }
        }
    }
    
    
//     public function usuariocorreo($idface)
//    {
//        $adapter = $this->tableGateway->getAdapter();
//        $sql = new Sql($adapter);
//        $selecttot = $sql->select()->from('ta_cliente')
//                ->where(array('id_facebook'=>$idface));
//        $selectString = $sql->getSqlStringForSqlObject($selecttot);
//        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//        return $resultSet->toArray();
//    }
    
     public function idfacebook($id,$idfacebook,$logout)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->update('ta_cliente')
                ->set(array('id_facebook'=>$idfacebook,'va_logout'=>$logout))
                ->where(array('in_id'=>$id));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
                   $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
public function idfacebook2($id,$logout)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->update('ta_cliente')
                ->set(array('va_logout'=>$logout))
                ->where(array('in_id'=>$id));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
                   $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    }
    
     public function insertarusuariofacebbok($nombre,$email,$idfacebook,$logout)
    {   
      $contrasena = sha1($idfacebook) ;
         $fecha = date("Y-m-d h:m:s");  
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->insert()
                ->into('ta_cliente')
                ->values(array('va_nombre_cliente'=>$nombre,'va_email'=>$email,'id_facebook'=>$idfacebook,
                    'en_estado'=>'activo','va_contrasena'=>$contrasena
                   ,'va_logout'=>$logout,'va_fecha_ingreso'=>$fecha,'va_notificacion'=>'si'));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
      $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
     }
      public function clientes($token)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()->from('ta_cliente')
                ->where(array('va_verificacion'=>$token,'en_estado'=>'desactivo'));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet->toArray();
    }
   
    public function compruebarUsuariox($iduser){
        
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()
        ->from('ta_cliente')
        ->where(array('in_id'=>$iduser));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet->current();
    
    }
    
    private function cargaAtributos($usuario=array())
    {
        $this->nombre=$usuario["va_apellidos"];
        $this->apellido=$usuario["va_email"];
         $this->pass=$usuario["va_contrasenia"];
        $this->correo=$usuario["va_email"];
         $this->id=$usuario["in_id"];
        $this->rol=$usuario["Ta_rol_in_id"];
        
        
    }
    public function updateUsuario($id, $data=array())
    {

    $adapter=$this->tableGateway->getAdapter();
       $sql = new Sql($adapter);
       $update = $sql->update('ta_usuario',$data, array('in_id' => $id));
            $selectString = $sql->getSqlStringForSqlObject($update);
           $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
          // var_dump($selectString);exit;
             $row = $results->current(); 
       if (!$row) {
           throw new \Exception("No existe registro con el parametro $id");
       }
        return $row;
     
    }
    public function todosUsuarios()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public function fetch(){
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    public function fetchAll()
    {
       // $resultSet = $this->tableGateway->select();
      $adapter = $this->tableGateway->getAdapter();
     
        $sql = new Sql($adapter);
        $select = $sql->select()
                ->from(array('f' => 'ta_usuario'))//,array('in_id','va_nombre','va_apellidos','va_email','en_estado')) 
                ->join(array('b' => 'ta_rol'), 'f.Ta_rol_in_id=b.in_id', array('va_nombre_rol'))//,array('va_nombre_rol'))
                ->where(array('f.Ta_rol_in_id=b.in_id'));
        
//                ->from(array('f' => 'ta_usuario'))//,array('in_id','va_nombre','va_apellidos','va_email','en_estado')) 
//                ->join(array('b' => 'ta_rol'), 'f.Ta_rol_in_id=b.in_id', array('va_nombre_rol'))//,array('va_nombre_rol'))
//                ->where(array('f.Ta_rol_in_id=b.in_id'));
        
//                ///bien
        $selectString = $sql->getSqlStringForSqlObject($select);
       
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
  
        return $resultSet;
    }
    
         public function fetchAll2() 
    { 
        $sqlSelect = $this->tableGateway->getSql() 
                          ->select()->columns(array('in_id', 'va_nombre', 'va_email',
                              'va_contraseña','en_estado')) 
                          ->join('ta_rol', 'ta_rol.in_id = ta_usuario.Ta_rol_in_id', array(), 'left'); 
        
        return $this->tableGateway->select($sqlSelect); 
    } 
    
    public function buscarUsuario($datos,$tipo){
        $adapter=$this->tableGateway->getAdapter();
           $sql = new Sql($adapter);
        
           if($tipo=='va_nombre' ){

             $select = $sql->select()
            ->from(array('f' => 'ta_usuario')) 
            ->join(array('b' => 'ta_rol'),'f.Ta_rol_in_id = b.in_id',array('va_nombre_rol'))
            ->where(array($tipo.' LIKE ?'=>'%'.$datos.'%')); //->where(array('f.in_id'=>$id));
//             $selectString = $sql->getSqlStringForSqlObject($select);
//            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//            $rowset = $results;//->ToArray();
           }else{
                $select = $sql->select()
                ->from(array('f' => 'ta_usuario')) 
                ->join(array('b' => 'ta_rol'),'f.Ta_rol_in_id=b.in_id',array('va_nombre_rol'))
                ->where(array('b.in_id'=>$tipo));
//            //$rowset = $this->tableGateway->select(array('Ta_rol_in_id'=>$tipo));               
//            $selectString = $sql->getSqlStringForSqlObject($select);
//            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
//            $rowset = $results;//->ToArray();

            }
            
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $rowset = $results;
           /* $array=array();
            foreach($rowset as $resul){
                $array[]=$resul;   
            }
             var_dump( $array);exit;*/

               if (!$rowset) {
            throw new \Exception("No hay data");
        }
       
      
        return $rowset;
    }



    public function moretablas(){

        $adapter=$this->tableGateway->getAdapter();
        $sql = new sql($adapter);
        $select = $sql->select()->from('ta_rol');
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);



                $returnArray=array();
        foreach ($results as $result) {
            $returnArray[] = $result;
        }

        var_dump($returnArray);exit;

    }
 
    public function getUsuario($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('in_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    //-----------------------------INICIO--------------------------------------------

public function getAlbum($id)
   {
        $adapter=$this->tableGateway->getAdapter();
       $sql = new Sql($adapter);
       $select = $sql->select()
           ->from(array('f' => 'ta_usuario')) 
           ->join(array('b' => 'ta_rol'),'f.Ta_rol_in_id = b.in_id')
            ->where(array('f.in_id'=>$id));
            $selectString = $sql->getSqlStringForSqlObject($select);
           $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
             $row = $results->current();
      
            
       if (!$row) {
           throw new \Exception("No existe registro con el parametro $id");
       }

       //var_dump($row);exit;
        return $row;  
   }

//----------------------------FIN---------------------------------------------------
 

//    public function getUsuario($id)
//    {
//        $id  = (int) $id;
//        $rowset = $this->tableGateway->select(array('id' => $id));
//        $row = $rowset->current();
//        if (!$row) {
//            throw new \Exception("Could not find row $id");
//        }
//        return $row;
//    }

    public function saveUsuario(Usuario $usuario)
    {
        $data = array(
            'nombre' => $usuario->nombre,
            'direccion'  => $usuario->direccion,
        );

        $id = (int)$usuario->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsuario($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

public function guardarUsuario( $usuario)
    {
        $data = array(
           'va_nombre'     => $usuario->va_nombre,
           'va_apellidos'  => $usuario->va_apellidos,
           'va_email'      => $usuario->va_email,
           'va_contrasenia'=> $usuario->va_contrasenia,
           'Ta_rol_in_id'  => $usuario->Ta_rol_in_id,  
        );
        
        $id = (int)$usuario->in_id;
     //   var_dump($id);exit;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsuario($id)) {
                $this->tableGateway->update($data, array('in_id' => $id));
            } else {
                throw new \Exception('no existe el usuario');
            }
        }
    }
    
    public function actualizaUsuario(Usuario $usuario)
    {
        $data = array(
           'va_nombre'     => $usuario["va_nombre"],
           'va_apellidos'  => $usuario["va_apellidos"],
           'va_email'      => $usuario["va_email"],
           'va_contrasenia'=> $usuario["va_pass"],
           'Ta_rol_in_id'  => $usuario["Ta_rol_in_id"],  
        );
        
        $id = (int)$usuario["in_id"];
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsuario($id)) {
                $this->tableGateway->update($data, array('in_id' => $id));
            } else {
                throw new \Exception('no existe el usuario');
            }
        }
    }
 
    public function estadoUsuario($id,$estado){
                $data = array(
                    'en_estado' => $estado,
                 );
         $this->tableGateway->update($data, array('in_id' => $id));
    }
    

    public function deleteUsuario($id)
    {
        
        $this->tableGateway->delete(array('in_id' => $id));
    }
    public function editarUsuario($id,$usuario){
                $data = array(
            'va_nombre' => $usuario->va_nombre,
            'va_apellidos'  => $usuario->va_apellidos,
            'va_email'  => $usuario->va_email,
            'va_contraseña'  => $usuario->va_contraseña,
            'en_estado'  => $usuario->en_estado,
            'Ta_rol_in_id'  => $usuario->Ta_rol_in_id,
           
        );
        $this->tableGateway->update($data, array('in_id' => $id));
    }

    public function listar(){   
        
        //obtener el adaptador x defecto defino en module
       // $lista = $this->tableGateway->getAdapter()->query("SELECT * FROM ta_usuario")->execute();//select()->from('usuario')->query()->fetchAll(); //fetchAll("SELECT * FROM USUARIO");
        
       $adapter=$this->tableGateway->getAdapter();
       $sql = new Sql($adapter);
       
       
         $select = $sql->select()
        ->from(array('f' => 'ta_usuario')) 
        ->join(array('b' => 'ta_rol'),'f.Ta_rol_in_id=b.in_id');
        //->where(array('b.in_id'=>'f.Ta_rol_in_id'));
       $selectString = $sql->getSqlStringForSqlObject($select);
        $lista= $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
    /*$select = new Select();
        $lista = $this->tableGateway->getAdapter()->select()->from('usuario',array('nombre','direccion'));

        $sql = new Sql($this->tableGateway->getAdapter());
        $lista = $sql->select()->from('foo');*/

        //necesario debido a ... es un misterio, solo lo hze xq no funciona el toArray()
        $returnArray=array();
        foreach ($lista as $result) {
            $returnArray[] = $result;
        }

       //var_dump($returnArray);exit;
        return $returnArray;
    }
    
     public function estado(){
        
        $datos=$this->tableGateway->getAdapter()->query("SELECT * FROM ta_rol")->execute();
                $returnArray=array();
        foreach ($datos as $result) {
            $returnArray[] = $result;
        }
        
        return  $returnArray;
        
    }
    

    public function listar2(){

        //con tablegetway y zend.db.sql

        /*$lista = $this->tableGateway->select(function (Select $select) {
        $select->where->like('nombre', 'kev%');
        });*/
        $adapter=$this->tableGateway->getAdapter();
        $sql = new sql($adapter);
        $select = $sql->select()->from('ta_usuario')->where(array('va_nombre' => 'kevin'));//where('nombre=kevin');//
        //$select->from('usuario'); 
        //$select->where(array('nombre' => 'kevin'));

        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);



        //$sql = new Sql($this->tableGateway->getAdapter());

      /*  $spec = function (Where $where) {
              $where->like('nombre','kev%');
        };*/

       // $lista = $sql->select()->from('usuario');//->where->like('nombre', 'kev%');
        $returnArray=array();
        foreach ($results as $result) {
            $returnArray[] = $result;
        }
         
       
        var_dump($returnArray);exit;

    }

    
    public function rolAll($adapter)
    { $sql = new Sql($adapter);
        $select = $sql->select()
                 ->from('ta_rol');
      $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);

       $row = $results->toArray();
        if (!$row) {
            throw new \Exception("No existe registro con el parametro $id");
        }
        return $row;  
    }
    
    
          public function agregarComentariomovil($coment,$id){
         
           $cliente=array(
                    'va_nombre_cliente'=>$coment['va_nombre'],
                    'va_email'=>$coment['va_email'],
                    'va_contrasena'=>sha1($coment['va_email']),
                    'en_estado'=>'activo',
               );
           $cantidad=$this->usuario1($coment['va_email']);
           if(count($cantidad)==0)
           { 
                    $insert = $this->tableGateway->getSql()->insert()->into('ta_cliente')
                    ->values($cliente);
                 $statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($insert);
                 $statement->execute();    
                 $idcliente=$this->tableGateway->getAdapter()->getDriver()->getLastGeneratedValue();  
                 $comentario = array(
                 'tx_descripcion' => $coment['tx_descripcion'],
                 'Ta_plato_in_id' => $coment['Ta_plato_in_id'],
                 'Ta_cliente_in_id' => $idcliente,
                 'Ta_puntaje_in_id' => $coment['Ta_puntaje_in_id'],
                 'da_fecha'=>  $fecha = date("Y-m-d h:m:s")
               ); 
                  
      
            //  $this->correomovill($coment['va_email'],$coment['va_nombre']);

                 
           }
           else{  
               $comentario = array(
                    'tx_descripcion' => $coment['tx_descripcion'],
                    'Ta_plato_in_id' => $coment['Ta_plato_in_id'],
                    'Ta_cliente_in_id' => $cantidad[0]['in_id'],
                    'Ta_puntaje_in_id' => $coment['Ta_puntaje_in_id'],
                   'da_fecha'=>  $fecha = date("Y-m-d h:m:s")
                ); 
             
               }

            
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
    
   
    
    

}