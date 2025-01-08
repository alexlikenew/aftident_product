<?php

namespace Models;

use Controllers\DbStatementController;

class MailTemplate extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='mail_template'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function loadArticles( $start, $limit, $onlyActive = true){

        $q = "SELECT p.*, d.title FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";

        if($onlyActive)
            $q .= "AND p.active=1 ";
        $q .= "ORDER BY p.date_add DESC, p.id DESC ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $limit]
        ];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function create($data): int{

        $q = "INSERT INTO ".$this->table." SET ";
        $q .= "active = ?, ";
        $q .= "name = ?, ";
        $q .= "admin = ?, ";
        $q .= "user = ?, ";
        $q .= "variables = ? ";
        $params = [
            [DbStatementController::INTEGER, !(empty($data['title'])) ? 1 : 0],
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::INTEGER, $data['send_to_admin'] ?? 0],
            [DbStatementController::INTEGER, $data['send_to_user'] ?? 0],
            [DbStatementController::STRING, $data['variables']]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function createDescription($data, $langId){

        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "title=?, ";
        $q .= "content=?, ";
        $q .= "active=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::INTEGER, $data['lang_active'][$langId] ? 1: 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function getById($id){
        $q = "SELECT * FROM ".$this->table." " ;
        $q .= "WHERE id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }

    public function update(array $data, int $id = null): bool
    {

        // aktualizujemy artykul
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "name=?, ";
        $q .= "admin=?, ";
        $q .= "user = ?, ";
        $q .= "variables=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::INTEGER, $data['send_to_admin']],
            [DbStatementController::INTEGER, $data['send_to_user']],
            [DbStatementController::STRING, $data['variables']],
            [DbStatementController::INTEGER, $data['id']],
        ];
        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "title=?, ";
        $q .= "content=?, ";
        $q .= "active=? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::INTEGER, isset($data['lang_active'][$langId]) ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getArticle($str, $isActive = true){
        $q  = "SELECT p.*, d.title, d.content FROM ".$this->table." p ";
        $q .= "LEFT JOIN ".$this->tableDescription." d ON (d.parent_id=p.id) ";
        $q .= "WHERE p.active = 1 AND d.active = 1 AND p.name=? AND d.language_id=? ";
        $params = [
            [DbStatementController::STRING, $str],
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }
}
