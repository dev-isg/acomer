<?php
namespace Usuario\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Zend\Db\Adapter\Platform\PlatformInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

use Zend\Db\Adapter\Platform;



class UsuarioTable
{
    protected $tableGateway;
    
    private $va_nombre;
    private $va_apellidos;
     private $va_contrasenia;
    private $va_email;
    private $Ta_rol_in_id;
    private $in_id;
    

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
      
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
  
}