<?php

namespace Models;

use Controllers\DbStatementController;

class FormInput extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='form_input'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function getMaxOrder($id){

        $q = "SELECT MAX(`order`) FROM ". $this->table." ";
        $q .= "WHERE parent_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function create(array $data): int{
        $q = "INSERT INTO ". $this->table." SET ";
        $q .= "parent_id = ?, ";
        $q .= "type = ?, ";
        $q .= "name = ?, ";
        $q .= "active = 1, ";
        $q .= "`order` = ? ";

        $params = [
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, $data['type']],
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::INTEGER, $data['order']]
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function createDescription($data, $langId){
        $q = "INSERT INTO ". $this->tableDescription." SET ";
        $q .= "parent_id = ?, ";
        $q .= "language_id = ?, ";
        $q .= "config = ?, ";
        $q .= "content = ?, ";
        $q .= "title = ?, ";
        $q .= "active = ? ";

        $params = [
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, $langId],
            [DbStatementController::STRING, $data['config']],
            [DbStatementController::STRING, $data['content']],
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::INTEGER, $data['active']]
        ];

        return $this->query($q, $params)->insert_id();
    }

    public function getById($id){
        $q = "SELECT p.*, d.title, d.title_url, d.content AS lang_content, d.config as lang_config FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function update(array $data, int $id = null):bool{
        $q = "UPDATE ". $this->table . " SET ";
        $q .= "name = ?, ";
        $q .= "config = ? ";
        $q .= "WHERE id = ? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['config']],
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(array_merge($data, ['id'=> $id])), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "title=?, ";
        $q .= "config = ?, ";
        $q .= "content=?, ";
        $q .= "active = ? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::STRING, $data['config']],
            [DbStatementController::STRING, $data['content']],
            [DbStatementController::INTEGER, $data['active'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }
}