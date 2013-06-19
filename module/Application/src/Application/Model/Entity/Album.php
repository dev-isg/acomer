<?php
namespace Application\Model\Entity;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
class Album extends TableGateway 
{
     public $dbAdapter;
     private $nombre;
     private $apellido;
     private $pass;
     private $email;
     private $rol;
     private $estado;
   public function __construct(Adapter $adapter = null, $databaseSchema = null, 
        ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('ta_usuario', $adapter, $databaseSchema, 
            $selectResultPrototype);
    }
    private function cargaAtributos($datos=array())
    {
        $this->nombre=$datos["nombre"];
        $this->apellido=$datos["apellido"];
        $this->pass=$datos["pass"];
        $this->email=$datos["email"];
        $this->rol=$datos["rol"];
    }
public function fetchAll()
    {
        $resultSet = $this->select();
 
        return $resultSet->toArray();
    }
    
    public function rolAll($adapter)
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

    public function getAlbum($id,$adapter)
    {
        $sql = new Sql($adapter);
        $select = $sql->select()
            ->from(array('f' => 'ta_usuario')) 
            ->join(array('b' => 'ta_rol'),'f.Ta_rol_in_id = b.in_id');
             //->where(array('f.in_id'=>$id));
             $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
       $row = $results->current();

        if (!$row) {
            throw new \Exception("No existe registro con el parametro $id");
        }
        return $row;  
    }
    
    public function obtenerUsuario($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('in_id' => $id)); 
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No existe registro con el parametro $id");
        }
        return $row;
    }

    public function addAlbum($data = array())
    {
         self::cargaAtributos($data);
         $array=array
             (  'va_nombre'=>$this->nombre,
                'va_apellidos'=>$this->apellido,
                'va_contrasenia'=>$this->pass,
                'va_email'=>$this->email,
                'Ta_rol_in_id'=>$this->rol );
               $this->insert($array);
    }

    public function updateAlbum($id, $data = array())
    {
          self::cargaAtributos($data);
         $array=array
             (  'va_nombre'=>$this->nombre,
                'va_apellidos'=>$this->apellido,
                'va_contrasenia'=>$this->pass,
                'va_email'=>$this->email,
                'Ta_rol_in_id'=>$this->rol );
        $this->update($array, array('in_id' => $id));
    }

    public function deleteAlbum($id)
    {
        $this->delete(array('in_id' => $id));
       
    }
}