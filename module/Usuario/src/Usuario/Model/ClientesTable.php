<?php
namespace Usuario\Model;


use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Usuario\Model\ComentariosTable ;
use Zend\Db\Adapter\Adapter;



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
    
    
       public function usuarioface($id_face)
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->select()->from('ta_cliente')
                ->where(array('va_email'=>$id_face));
        $selectString = $sql->getSqlStringForSqlObject($selecttot);
        $resultSet = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        return $resultSet->toArray();
    }

    public function guardarClientes($clientes,$valor=null)
    {  
        $data = array(
            'va_nombre_cliente' => $clientes['va_nombre_cliente'],
            'va_email' => $clientes['va_email'],
            'va_contrasena' => sha1($clientes['va_contrasena']),
            'va_verificacion' => $valor,  
            'va_notificacion' => $clientes['va_notificacion'],
            'en_estado' =>'desactivo',
          'va_fecha_ingreso'  =>date("Y-m-d H:i:s")
                );
      //  $id = (int) $clientes->in_id;
     
//        foreach($data as $key=>$value){
//           if(empty($value)){
//               $data[$key]=0;
//           }
//       }
       // if ($id == 0) { 
           // $data['va_fecha_ingreso'] = ;
           $clientes = $this->tableGateway->insert($data);
//        } else { 
//            if ($this->getUsuario($id)) {
//                 $this->updateCategoria($catg_ingresada, $id);
//                if ($pass == '') {
//                    $data['va_estado'] = 'activo';
//                    $data['va_verificacion'] = '';
//                    $this->tableGateway->update($data, array(
//                        'in_id' => $id));
//                } else {
//                       $data['va_pais'] = $usuario->va_pais;
//                   $data['ta_ubigeo_in_id']=$ciudad;
//                    $data['va_contrasena'] = $pass;
//                    $data['va_verificacion'] = '';
//                    $data['va_estado'] = 'activo';
//
//                    $this->tableGateway->update($data, array(
//                        'in_id' => $id));
//                }
//            } else {
//                throw new \Exception('no existe el usuario');
//            }
      //  }
    }
    

    
     public function idfacebook($id,$idfacebook,$logout)
    {
         $contrasena = sha1($idfacebook) ;
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $selecttot = $sql->update('ta_cliente')
                ->set(array('id_facebook'=>$idfacebook,'va_logout'=>$logout,'va_contrasena_facebook'=>$contrasena))
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
                    'en_estado'=>'activo','va_contrasena_facebook'=>$contrasena
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
    
  

    //-----------------------------INICIO--------------------------------------------


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