<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

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
        return $resultSet->toArray();
    }

    public function getAlbum($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('in_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    
    
    public function saveAlbum(Album $album)
    {
        $data = array(
            'nombre' => $album->va_nombre,
           'apellido'=> $album->va_apellido,
            'pass' => $album->va_contrasenia,
           'email'=> $album->va_email,
            'rol' => $album->Ta_rol_in_id,
        );

        $id = (int)$album->in_id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlbum($id)) {
                $this->tableGateway->update($data, array('in_id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteAlbum($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
}