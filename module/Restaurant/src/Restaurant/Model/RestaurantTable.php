<?php
namespace Restaurant\Model;

use Zend\Db\TableGateway\TableGateway;

class RestaurantTable
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

    }