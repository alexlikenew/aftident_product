<?php

namespace Models;

use Controllers\DbStatementController;

class Offer extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table='offers'){
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
        $q .= "subtitle=?, ";
        $q .= "title_url=?, ";
        $q .= "content=?, ";
        $q .= "content_short=?, ";
        $q .= "content_2=?, ";
        $q .= "content_3=?, ";
        $q .= "content_documents=?, ";
        $q .= "content_price=?, ";
        $q .= "tagi=?, ";
        $q .= "active=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['subtitle'][$langId] ?? ''],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['content_2'][$langId] ?? ''],
            [DbStatementController::STRING, $data['content_3'][$langId] ?? ''],
            [DbStatementController::STRING, $data['content_documents'][$langId] ?? ''],
            [DbStatementController::STRING, $data['content_price'][$langId] ?? ''],
            [DbStatementController::STRING, $data['tagi'][$langId]],
            [DbStatementController::INTEGER, $data['lang_active'] ? 1: 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "title=?, ";
        $q .= "subtitle=?, ";
        $q .= "title_url=?, ";
        $q .= "content=?, ";
        $q .= "content_short=?, ";
        $q .= "content_2=?, ";
        $q .= "content_3=?, ";
        $q .= "content_documents=?, ";
        $q .= "content_price=?, ";
        $q .= "tagi=?, ";
        $q .= "active=? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['subtitle'][$langId] ?? ''],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['content_2'][$langId] ?? ''],
            [DbStatementController::STRING, $data['content_3'][$langId] ?? ''],
            [DbStatementController::STRING, $data['content_documents'][$langId] ?? ''],
            [DbStatementController::STRING, $data['content_price'][$langId] ?? ''],
            [DbStatementController::STRING, $data['tagi'][$langId]],
            [DbStatementController::INTEGER, isset($data['lang_active'][$langId]) ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];
        $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getOffer($str)
    {
        $q = "SELECT p.*, d.page_title, d.page_keywords, d.page_description, d.title, d.title_url, d.content, d.content_2, d.content_3, d.content_documents, d.content_price, d.content_short, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND d.title_url=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::STRING, $str]
        ];

        $statement = $this->query($q, $params);

        return  $statement->fetch_assoc();
    }

    public function getByPid($pid = null){
        $q = "SELECT a.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " a ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON a.id=d.parent_id ";
        $q .= "WHERE a.parent_id=? ";
        $q .= "AND d.language_id=? ";
        $q .= "ORDER BY a.id ASC";

        $params = [
            [DbStatementController::INTEGER, $pid],
            [DbStatementController::INTEGER, _ID]
        ];

        return  $this->query($q, $params)->get_all_rows();
    }


    public function getSubtree($path_id = null){
        $q = "SELECT a.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " a ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON a.id=d.parent_id ";
        $q .= "WHERE ";
        if($path_id){
            $q .= "path_id LIKE '".$path_id."%' ";
            $q .= "AND ";
        }
        $q .= "d.language_id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->get_all_rows();

    }


    public function getPathById($id){
        $q = "SELECT path_id FROM " . $this->table . " WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();

    }

    public function getPath($pathArray){
        $q = "SELECT a.id, a.parent_id, d.title_url, d.title FROM " . $this->table . " a ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON a.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND ";

        $params = [
            [DbStatementController::INTEGER, _ID]
        ];

        foreach($pathArray as $i=>$row) {
            if ($i == 0) {
                $q .= "(";
            }
            if ($i > 0) {
                $q .= "OR ";
            }
            $q .= "a.id=? ";

            $params[] = [DbStatementController::STRING, $row];

            if ($i == count($pathArray) - 1) {
                $q .= ") ";
            }
        }

        $q .= "ORDER BY path_id";

        return $this->query($q, $params)->get_all_rows();

    }

    public function getParentById($id){
        $q = "SELECT parent_id FROM " . $this->table . " WHERE id=? LIMIT 1";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->get_result();

    }

    public function updatePath($id, $parent_id){
        $q = "UPDATE " . $this->table . " SET path_id=CONCAT(path_id,?) WHERE id=? ";

        $params = [
            [DbStatementController::STRING,  $id.'.'],
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getMaxOrder($id = null){

        $q = "SELECT MAX(order_number) FROM ". $this->table ." WHERE parent_id = ? ";
        $params = [
            [DbStatementController::INTEGER, $id ?? 0]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function create(array $data): int{

        $q = "INSERT INTO " . $this->table . " SET ";
        $q.= "op_page_title=?, ";
        $q.= "op_page_keywords=?, ";
        $q.= "op_page_description=?, ";
        $q.= "gallery_id=?, ";
        $q.= "active=?, ";
        $q.= "order_number=?, ";
        $q.= "path_id=?, ";
        $q.= "parent_id=? ";

        $params = [
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, (isset($data['gallery_id'])?$data['gallery_id']:"") ],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active'])) ? 1 : 0],
            [DbStatementController::INTEGER, $data['order']],
            [DbStatementController::STRING, $data['parent_id'] ? $data['parent_id'].'.' : ''],
            [DbStatementController::INTEGER, $data['parent_id'] ?? 0],
        ];
        $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function getById($id){
        $q = "SELECT p.*, d.title, d.title_url, d.content_short, d.content FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function getByIds(array $ids){
        $q = "SELECT p.*, d.title, d.title_url, d.content_short, d.content FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id IN (".implode(',', $ids).")";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];
        return $this->query($q, $params)->get_all_rows();
    }

}
