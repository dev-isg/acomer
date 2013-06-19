<?php
namespace Restaurante\Model;
use Zend\Db\TableGateway\TableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;


class LocalTable extends TableGateway{
    public function __construct(Adapter $adapter = null, $databaseSchema = null, 
    ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('ta_local', $adapter, $databaseSchema, 
            $selectResultPrototype);
    }
    
    
    

    
}
