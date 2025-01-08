<?php

namespace Models;
use Controllers\DbStatementController;

class Catalog extends ModelCategorized
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='catalogs', $isCategorized = false){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table, $isCategorized);
    }

    public function update(array $data, int $id = null): bool{
        // aktualizujemy artykul
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "category_id = ?, ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "auth=?, ";
        $q .= "gallery_id=?, ";
        $q .= "show_title=?, ";
        //$q .= "active=?, ";
        $q .= "main=?, ";
        $q .= "on_sale = ?, ";
        $q .= "comments=?, ";
        $q .= "date_add=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $data['category_id']],
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::INTEGER, $data['show_title']],
            //[DbStatementController::INTEGER, $data['active']],
            [DbStatementController::INTEGER, $data['main'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['on_sale'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['comments']],
            [DbStatementController::STRING, $data['date_add']],
            [DbStatementController::INTEGER, $data['id']]
        ];

        $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }
    public function create(array $data):int
    {
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "category_id = ?, ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "auth=?, ";
        $q .= "gallery_id=?, ";
        $q .= "show_title=?, ";
        $q .= "active=?, ";
        $q .= "main=?, ";
        $q .= "comments=? ";

        $params = [
            [DbStatementController::INTEGER, $data['category_id']],
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::INTEGER, $data['show_title'] ?? 0],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active']))? 1 : 0],
            [DbStatementController::INTEGER, $data['main'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['comments'] ?? 0],
        ];

        $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }
}