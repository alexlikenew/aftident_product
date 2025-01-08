<?php

namespace Models;

use Controllers\DbStatementController;
class Rooms extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='blog'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function update(array $data, int $id = null): bool{

        $q = "UPDATE ". $this->table." SET price = ? WHERE id = ? ";
        $params = [
            [DbStatementController::DOUBLE, $data['price']],
            [DbStatementController::INTEGER, $id]
        ];

        $this->query($q, $params);

        return parent::update($data, $id);
    }


}