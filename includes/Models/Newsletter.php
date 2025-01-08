<?php

namespace Models;

use Controllers\DbStatementController;

class Newsletter extends Model
{
    protected $table;
    protected $tableDescription;
    protected $tableTemplate;
    protected $tableToSend;

    public function __construct($table='newsletter'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';
        $this->tableTemplate = $this->table . '_template';
        $this->tableToSend = $this->table . '_to_send';
        parent::__construct($table);
    }

    public function getPages($onlyActive = false)
    {
        return 1;
    }

    public function loadUsers($page, $limit, $where, $order){
        $q = "SELECT * FROM " . $this->table . " ";
        $q.= "WHERE " . $where . " ";
        $q.= "ORDER BY " . $order . " LIMIT " . ($page * $limit) . ", " . $limit;

        return $this->query($q)->get_all_rows();
    }

    public function addNewUser($email, $name){
        $q = "INSERT INTO ". $this->table ." SET ";
        $q .= "first_name=?, ";
        $q .= "email=?, ";
        $q.= "active=1, grupa=1 ";
        $params = [
            [DbStatementController::STRING, $name],
            [DbStatementController::STRING, $email]
        ];

        return $this->query($q, $params)->insert_id();
    }

    public function getAllTemplates(){
        $q = "SELECT * FROM " . $this->tableTemplate . " ";

        return $this->query($q)->get_all_rows();

    }

    public function getInQueueCount($idTemplate = false){
        $q = "SELECT count(k.id) AS ile FROM ".$this->tableToSend." k  ";
        $params = [];
        if($idTemplate)
        {
            $q.= "where k.szablon_id=? ";

            $params = [
                [DbStatementController::INTEGER, $idTemplate]
            ];
        }

        $statement = $this->query($q, $params);
        if($row = $statement->fetch_assoc())
        {
            return $row['ile'];
        }
        return 0;
    }

    public function getStats($data){
        $q = "SELECT COUNT(id) as 'all', SUM(active) as 'active' FROM " . $this->table . " ";
        $params = [];

        if (isset($data['send']) && $data['send'] == "group") {
            $q .= "where grupa=? ";
            $params    = [[DbStatementController::INTEGER, $data['grupa']],];
        }
        return $this->query($q, $params)->fetch_assoc();
    }

    public function addTemplate($data){
        $q = "INSERT INTO " . $this->tableTemplate . " SET ";
        $q .= "date_add=NOW(), ";
        $q .= "name=?, ";
        $q .= "description=? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['description']]
        ];
        return  $this->query($q, $params)->insert_id();
    }

    public function loadTemplate($id){
        $q = "SELECT * FROM " . $this->tableTemplate . " ";
        $q.= "WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function updateTemplate($data){
        $q = "UPDATE " . $this->tableTemplate . " SET ";
        $q .= "name=?, ";
        $q .= "description=?, ";
        $q .= "value=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['description']],
            [DbStatementController::STRING, $data['value']],
            [DbStatementController::INTEGER, $data['id']]
        ];
        return $this->query($q, $params)->is_success();
    }

}