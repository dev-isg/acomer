<?php
namespace Application\Model\Entity;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
class Album extends TableGateway 
{
     public $dbAdapter;
   public function __construct(Adapter $adapter = null, $databaseSchema = null, 
        ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('ta_usuario', $adapter, $databaseSchema, 
            $selectResultPrototype);
    }
public function fetchAll()
    {
        $resultSet = $this->select();
 
        return $resultSet->toArray();
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

    public function getAlbum($id,$adapter)
    {
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
        return $row;  
    }

    public function addAlbum($data = array())
    {
    $this->insert($data);
    }

    public function updateAlbum($id, $data = array())
    {
        $this->update($data, array('id' => $id));
    }

    public function deleteAlbum($id)
    {
        $this->delete(array('in_id' => $id));
       
    }
}