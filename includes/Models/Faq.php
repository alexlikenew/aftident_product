<?php


namespace Models;


use Controllers\DbStatementController;

class Faq extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='faq'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';

        parent::__construct($table);
    }

    public function createDescription($data, $langId){

        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "title=?, ";
        $q .= "title_url=?, ";
        $q .= "content=?, ";
        $q .= "content_short=?, ";
        $q .= "tagi=?, ";
        $q .= "active=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['tagi'][$langId]],
            [DbStatementController::INTEGER, $data['lang_active'][$langId] ? 1: 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table, '');
        return $this->query($q, $params)->insert_id();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "title=?, ";
        $q .= "title_url=?, ";
        $q .= "content=?, ";
        $q .= "content_short=?, ";
        $q .= "tagi=?, ";
        $q .= "active=? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['tagi'][$langId]],
            [DbStatementController::INTEGER, isset($data['lang_active'][$langId]) ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table, '');
        return $this->query($q, $params)->is_success();
    }

    public function getAll($onlyActive = true, $toSkip = false){

        $q = "SELECT p.id, d.title, d.content, d.content_short  FROM " . $this->table ." p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON(p.id=d.parent_id)  WHERE d.language_id = ?  " ;
        $params = [
            [DbStatementController::INTEGER, _ID]
        ];
        if($onlyActive){
            $q .= " AND p.active=1 AND d.active=1 ";
        }
        $q .= "AND p.main != 1 ";

        return $this->query($q, $params)->get_all_rows();
    }
}
