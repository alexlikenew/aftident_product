<?php

namespace Traits;
use Classes\Connection;
use Controllers\DbStatementController;

trait DbConnection{

    private $connection = null;
    private $isConnected = false;

    public function getConnection():?Connection{
        return $this->connection;
    }

    public function setConnection(){
        $this->connection = Connection::getInstance();
    }


    function query($query, $params = false, $stop = false)
    {

        if(!$this->getConnection()->connect()){
            echo 'Database error';
            die();
        }
        $statement = new DbStatementController($query, $params, $this->getConnection()->getLink(), $stop);

        $statement->execute();

        return $statement;
    }


}