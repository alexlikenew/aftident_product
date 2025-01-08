<?php

namespace Models;

use Controllers\DbStatementController;

class PriceSteps extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='price_steps'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function create(array $data):int
    {

        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "from_price = ?, ";
        $q .= "to_price = ?, ";
        $q .= "active = ?";

        $params = [
            [DbStatementController::DOUBLE, $data['from']],
            [DbStatementController::DOUBLE, $data['to']],
            [DbStatementController::INTEGER, 1],
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function createDescription($data, $langId){

        $q = "INSERT INTO " . $this->tableDescription . " SET ";      ;
        $q .= "title=?, ";
        $q .= "active=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [

            [DbStatementController::STRING, 'Od: '.$data['from'].' '.' do: '.$data['to']],
            [DbStatementController::INTEGER, 1],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function update(array $data, int $id = null): bool
    {
        $q = "UPDATE ".$this->table." SET ";
        $q .= "from_price = ?, ";
        $q .= "to_price = ? ";
        $q .= "WHERE id = ? ";

        $params = [
            [DbStatementController::DOUBLE, $data['from']],
            [DbStatementController::DOUBLE, $data['to']],
            [DbStatementController::INTEGER, $id]
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);

        return $this->query($q, $params)->is_success();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE ".$this->tableDescription." SET ";
        $q .="title = ? WHERE parent_id = ? AND language_id = ? ";

        $params = [
            [DbStatementController::STRING, "Od: ".$data['from']." Do: ".$data['to']],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId],
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getAll($onlyActive = true, $toSkip = false){

        $q = "SELECT p.id, d.title, d.title_url FROM " . $this->table ." p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON(p.id=d.parent_id)  WHERE d.language_id = ?  " ;
        $params = [
            [DbStatementController::INTEGER, _ID]
        ];
        if($onlyActive){
            $q .= " AND p.active=1 AND d.active=1 ";
        }
        if($toSkip){
            $q .= " AND p.id != ? ";
            $params[] = [DbStatementController::INTEGER, $toSkip];
        }

        $q .= "ORDER BY from_price ASC ";

        return $this->query($q, $params)->get_all_rows();
    }
}