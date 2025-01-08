<?php

namespace Models;
use Controllers\DbStatementController;

class Contact extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='contact_messages'){
        $this->table = DB_PREFIX . $table;

        parent::__construct($table);
    }

    public function create(array $data): int{

        $q = "INSERT INTO ".$this->table." SET ";
        $q .= "name = ?, ";
        $q .= "email = ?, ";
        $q .= "content = ?, ";
        $q .= "type = ?, ";
        $q .= "position = ? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['email']],
            [DbStatementController::STRING, $data['message']],
            [DbStatementController::INTEGER, $data['type']],
            [DbStatementController::INTEGER, $data['position'] ?? 0]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function updateFileName($id, $name){
        $q = "UPDATE ".$this->table." SET ";
        $q .= "file = ? ";
        $q .= "WHERE id = ? ";
        $params = [
            [DbStatementController::STRING, $name],
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }
}
