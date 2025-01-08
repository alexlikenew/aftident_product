<?php

namespace Models;

use Controllers\DbStatementController;

class Features extends Model
{
    protected $table;
    protected $tableDescription;
    protected $tableValues;
    protected $tableValuesDescription;
    public function __construct($table='features'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';
        $this->tableValues = $this->table . '_values';
        $this->tableValuesDescription = $this->tableValues.'_description';

        parent::__construct($table);
    }


    public function create(array $data):int{
        $order = $this->getMaxOrder() + 1;
        $q = "INSERT INTO ".$this->table." SET ";
        $q .= "active = ?, ";
        $q .= "`order` = ?, ";
        $q .= "categories=? ";

        $params = [
            [DbStatementController::INTEGER, (isset($data['lang_active']) && $data['lang_active']) ? 1 : 0],
            [DbStatementController::INTEGER, $order],
            [DbStatementController::STRING, json_encode($data['categories'])]
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

    public function getMaxOrder(){
        $q = "SELECT MAX(`order`) FROM ".$this->table;
        return $this->query($q)->get_result();
    }

    public function update(array $data, int $id = null): bool{
        $q = "UPDATE ". $this->tableValues ." SET ";
        $q .= "custom_value = ? ";
        $q .= "WHERE id = ? ";

        $params = [
            [DbStatementController::INTEGER, $data['custom_value'] ?? 0],
            [DbStatementController::INTEGER, $id]
        ];

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
    public function updateCategories($data){
        $q = "UPDATE ". $this->table ." SET ";
        $q .= "categories=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, json_encode($data['categories'])],
            [DbStatementController::INTEGER, $data['id']]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function loadArticles( $start, $limit, $onlyActive = true, $category_id = false, $category_path = false){

        $q = "SELECT p.*, d.title, d.content FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";

        if($onlyActive)
            $q .= "AND p.active=1 ";
        $q .= "ORDER BY p.`order` ASC, p.date_add DESC, p.id DESC ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $limit]
        ];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getById($id){
        $q = "SELECT p.*, d.title, d.content FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }

    public function getValuesById($id, $onlyActive = true){
        $q = "SELECT v.id, v.active, v.parent_id, v.type, vd.title, v.custom_value  FROM ".$this->tableValues." v ";
        $q .= "LEFT JOIN ".$this->tableValuesDescription . " vd ON (v.id = vd.parent_id) ";
        $q .= "WHERE v.parent_id = ? AND vd.language_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID]
        ];
        if($onlyActive){
            $q .= "AND v.active = ? ";
            $params[] = [DbStatementController::INTEGER, 1];
        }
        $q .= "ORDER BY v.`order` ASC ";

        return $this->query($q, $params)->get_all_rows();
    }

    public function createValue($data){

        $order = $this->getMaxValueOrder($data['feature_id']) + 1;
        $q = "INSERT INTO ".$this->tableValues." SET ";
        $q .= "parent_id=?, ";
        $q .= "type=?, ";
        $q .= "custom_value = ?, ";
        $q .= "active=?, ";
        $q .= "`order`=?";

        $params = [
            [DbStatementController::INTEGER, $data['feature_id']],
            [DbStatementController::INTEGER, $data['type_id']],
            [DbStatementController::INTEGER, $data['custom_value'] ?? 0],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && $data['lang_active']) ? 1 : 0 ],
            [DbStatementController::INTEGER, $order]
        ];

        return $this->query($q, $params)->insert_id();
    }

    public function getMaxValueOrder($id){
        $q = "SELECT MAX(`order`) FROM ".$this->tableValues . " ";
        $q .= "WHERE parent_id=?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->get_result();
    }

    public function loadValueDescription($id, $langId = null){
        $q = "SELECT * FROM " . $this->tableValuesDescription . " ";
        $q.= "WHERE parent_id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        if($langId){
            $q .= "AND language_id=? ";
            $params[] = [DbStatementController::INTEGER, $langId];
        }

        $statement = $this->query($q, $params);

        if($langId)
            return $statement->fetch_assoc();

        return $statement->get_all_rows();
    }

    public function updateValueDescription($data, $langId){

        $q = "UPDATE " . $this->tableValuesDescription . " SET ";
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

    public function createValueDescription($data, $langId){

        $q = "INSERT INTO " . $this->tableValuesDescription . " SET ";
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

    public function getFeatureValue($id){
        $q = "SELECT * FROM ".$this->tableValues." WHERE id=?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }

    public function setValueActive($id, $active){
        $q = "UPDATE ". $this->tableValues . " SET active=? WHERE id = ?";
        $params = [
            [DbStatementController::INTEGER, $active ? 1 : 0],
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function deleteValue($id){
        $q = "DELETE FROM ".$this->tableValues." WHERE id=?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function deleteValueDescriptions($id){
        $q = "DELETE FROM ".$this->tableValuesDescription." WHERE parent_id = ?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getAll($onlyActive = true, $toSkip = false){

        $q = "SELECT p.id, p.categories, d.title  FROM " . $this->table ." p ";
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

        return $this->query($q, $params)->get_all_rows();
    }

    public function updateValueOrder($item, $order_new){

        if ($item['order'] < $order_new) {
            $q = "UPDATE " . $this->tableValues . " SET `order`=`order`-1 ";
            $q .= "WHERE `order`>? ";
            $q .= "AND parent_id=? ";
            $q .= "AND `order`<=? ";
            $q .= "";
            $params = [
                [DbStatementController::INTEGER, $item['order']],
                [DbStatementController::INTEGER, $item['parent_id']],
                [DbStatementController::INTEGER, $order_new]
            ];
            $this->query($q, $params);

        } else {
            $q = "UPDATE " . $this->tableValues . " SET `order`=`order`+1 ";
            $q .= "WHERE `order`>=? ";
            $q .= "AND parent_id=? ";
            $q .= "AND `order`<? ";
            $params = [
                [DbStatementController::INTEGER, $order_new],
                [DbStatementController::INTEGER, $item['parent_id']],
                [DbStatementController::INTEGER, $item['order']]
            ];
            $this->query($q, $params);

        }
        $q = "UPDATE " . $this->tableValues . " SET ";
        $q .= "`order`=? ";
        $q .= "WHERE id=?";
        $params = [
            [DbStatementController::INTEGER, $order_new],
            [DbStatementController::INTEGER, $item['id']]
        ];
        $statement = $this->query($q, $params);
        if ($statement->is_success()) {
            return true;
        }
        return false;
    }


}
