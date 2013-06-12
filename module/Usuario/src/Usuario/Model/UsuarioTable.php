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

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();

        return $resultSet;
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
public function guardarUsuario(Usuario $usuario)
    {
        $data = array(
            'va_nombre' => $usuario->va_nombre,
            'va_apellidos'  => $usuario->va_apellidos,
            'va_email' => $usuario->va_email,
            'va_contrasenia'  => $usuario->va_contrasenia,
            'Ta_rol_in_id'  => $usuario->Ta_rol_in_id,  
        );
        
        $id = (int)$usuario->in_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getUsuario($id)) {
                $this->tableGateway->update($data, array('in_id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    public function deleteUsuario($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }

    public function buscarUsuario($datos,$tipo){

       $rowset = $this->tableGateway->select(array($tipo => $datos));
      //$rowset=  $this->tableGateway->select();
     /* $rowset=  $this->tableGateway->select();   select(function (Select $select) {
     $select->where->like('nombre',$datos);
   
    });*/

      // $rowset = $this->tableGateway->select()->like($tipo,$datos);
        //$row = $rowset->current();
        //if (!$row) {
            //throw new \Exception("Could not find row $id");
               if (!$rowset) {
            throw new \Exception("No hay data");
        }
        //}
      
        return $rowset;
    }

    public function listar(){   
        //obtener el adaptador x defecto defino en module
        $lista = $this->tableGateway->getAdapter()->query("SELECT * FROM USUARIO")->execute();//select()->from('usuario')->query()->fetchAll(); //fetchAll("SELECT * FROM USUARIO");
        /*$select = new Select();
        $lista = $this->tableGateway->getAdapter()->select()->from('usuario',array('nombre','direccion'));

        $sql = new Sql($this->tableGateway->getAdapter());
        $lista = $sql->select()->from('foo');*/

        //necesario debido a ... es un misterio, solo lo hze xq no funciona el toArray()
        $returnArray=array();
        foreach ($lista as $result) {
            $returnArray[] = $result;
        }

       // var_dump($returnArray);exit;
        return $returnArray;
    }

    public function listar2(){

        //con tablegetway y zend.db.sql

        /*$lista = $this->tableGateway->select(function (Select $select) {
        $select->where->like('nombre', 'kev%');
        });*/
        $adapter=$this->tableGateway->getAdapter();
        $sql = new sql($adapter);
        $select = $sql->select()->from('usuario')->where(array('nombre' => 'kevin'));//where('nombre=kevin');//
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


}