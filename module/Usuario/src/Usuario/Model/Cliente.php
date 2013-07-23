<?php
namespace Usuario\Model;
use Zend\Db\TableGateway\TableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\AbstractResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
//namespace Application\Modelo\Entity;
use Zend\Db\Adapter\Platform\PlatformInterface;

class Cliente extends TableGateway{
    protected $tableGateway;
    
    public function __construct(Adapter $adapter = null, $databaseSchema = null, 
    ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('ta_cliente', $adapter, $databaseSchema, 
            $selectResultPrototype);
    }
    
        public function getCliente($consulta=null){
            $datos = $this->select('(in_id LIKE "%'.$consulta.'%") OR (va_nombre_cliente LIKE "%'.$consulta.'%") 
                        OR (va_email LIKE "%'.$consulta.'%")');
//                    ->where('(in_id LIKE "%'.$consulta.'%") OR (va_nombre LIKE "%'.$consulta.'%") 
//                        OR (va_email LIKE "%'.$consulta.'%")');

            $res=$datos->buffer();
            return $res;//->toArray();     
    }
    
         public function getClientePorId($id)
     {
        $id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        
        if (!$row) {
            throw new \Exception("No hay registros asociados al valor $id");
        }
        
        return $row;
     }
      public function addCliente($data=array())
        {
         
           $cliente=array(
                    'va_nombre_cliente'=>$data['va_nombre'],
                    'va_email'=>$data['va_email'],
                    
                );
          
        $id = (int) $data['in_id'];
            if ($id == 0) {
//                 var_dump($cliente);exit;
                $this->insert($cliente);
            }
        }

    public function updateCliente($id, $data=array())
    {
        
        $this->update($data, array('id' => $id));
    }

    public function deleteCliente($id)
    {
        $this->delete(array('id' => $id));
    }
    
}