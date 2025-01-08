<?php

namespace Models;

use Controllers\DbStatementController;

class Form extends Model
{
    protected $table;
    protected $tableDescription;
    protected $tableInput;
    protected $tableInputDescription;


    public function __construct($table='form'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';
        $this->tableInput = $this->table.'_input';
        $this->tableInputDescription = $this->tableInput.'_description';

        parent::__construct($table);
    }

    public function getInputsInfo($id){
        $q = "SELECT p.id, p.type, p.active, d.title FROM " .$this->tableInput ." p ";
        $q .= "LEFT JOIN ". $this->tableInputDescription." d ON(p.id=d.parent_id) ";
        $q .= "WHERE p.parent_id = ? AND d.language_id = ? ";
        $q .= "ORDER BY `order` ";

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID],
        ];

        return $this->query($q, $params)->get_all_rows();

    }

    public function getById($id){
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.subtitle FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

}