<?php

namespace Models;

use Controllers\DbStatementController;
class Attractions extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='attractions'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function update(array $post, int $id = null): bool {

        $result = parent::update($post, $id);

        $q = "UPDATE ". $this->table." SET hotel_attraction = ? WHERE id = ? ";
        $params = [
            [DbStatementController::INTEGER, $post['atraction_type'] ?? 0],
            [DbStatementController::INTEGER, $id]
        ];

        $this->query($q, $params);
        return $result;
    }
}