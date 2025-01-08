<?php

namespace Models;

use Controllers\DbStatementController;

class Inspirations extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='inspirations'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function update(array $data, int $id = null): bool
    {


        // aktualizujemy artykul
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "auth=?, ";
        $q .= "gallery_id=?, ";
        $q .= "products = ?, ";
        $q .= "show_title=?, ";
        //$q .= "active=?, ";
        $q .= "main=?, ";
        $q .= "comments=?, ";
        $q .= "date_add=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::STRING, json_encode($data['products'])],
            [DbStatementController::INTEGER, $data['show_title']],
            //[DbStatementController::INTEGER, $data['active']],
            [DbStatementController::INTEGER, $data['main'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['comments']],
            [DbStatementController::STRING, $data['date_add']],
            [DbStatementController::INTEGER, $data['id']]
        ];
        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }
}
