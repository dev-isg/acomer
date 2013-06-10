<?php
namespace Application\Model\Entity;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
class Rol extends TableGateway 
{
   public function __construct(Adapter $adapter = null, $databaseSchema = null, 
        ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('ta_rol', $adapter, $databaseSchema, 
            $selectResultPrototype);
    }
public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet->toArray();
    }

    public function getAlbum($id)
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
    $this->insert($data);
    }

    public function updateAlbum($id, $data = array())
    {
        $this->update($data, array('in_id' => $id));
    }

    public function deleteAlbum($id)
    {
        $this->delete(array('in_id' => $id));
    }
}