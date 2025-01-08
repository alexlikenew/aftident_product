<?php

namespace Models;

use Controllers\DbStatementController;

class SaleDepartment extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='sale_department'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function create(array $data):int
    {

        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "active=?, ";
        $q .= "name = ?, ";
        $q .= "phone1 = ?, ";
        $q .= "phone2 = ?, ";
        $q .= "email = ?";

        $params = [
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active']))? 1 : 0],
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['phone1']],
            [DbStatementController::STRING, $data['phone2']],
            [DbStatementController::STRING, $data['email']],
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function createDescription($data, $langId){

        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "workplace=?, ";
        $q .= "active=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['workplace'][$langId]],
            [DbStatementController::INTEGER, $data['lang_active'][$langId] ? 1: 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function update(array $data, int $id = null): bool
    {

        // aktualizujemy artykul
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "name=?, ";
        $q .= "phone1=?, ";
        $q .= "phone2=?, ";
        $q .= "email=?, ";
        $q .= "date_add = ? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['phone1']],
            [DbStatementController::STRING, $data['phone2']],
            [DbStatementController::STRING, $data['email']],
            [DbStatementController::STRING, $data['date_add']],
            [DbStatementController::INTEGER, $data['id']]
        ];
        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "workplace=?, ";
        $q .= "content =?, ";
        $q .= "content_short =?, ";
        $q .= "active=? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [

            [DbStatementController::STRING, $data['workplace'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::INTEGER, isset($data['lang_active'][$langId]) ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function loadArticles( $start, $limit, $onlyActive = true, $category_id = false, $category_path = false){

        $q = "SELECT p.*, d.workplace, d.content, d.content_short FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";

        if($onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";
        if($category_id)
            $q .= "AND p.category_id = ? ";
        $q .= "ORDER BY p.date_add DESC, p.id DESC ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];

        $params[] = [DbStatementController::INTEGER, $start];
        $params[]  = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

}
