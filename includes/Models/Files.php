<?php

namespace Models;
use Controllers\DbStatementController;
class Files extends Model{
    protected $table;

    public function __construct($table){
        $this->table = DB_PREFIX . $table;
        parent::__construct($table);
    }

    public function getFilesAdmin($parentId, $type){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "WHERE parent_id=? AND parent_type=? ";
        $q .= "ORDER BY `order` ASC ";

        $params = [
            [DbStatementController::INTEGER, $parentId],
            [DbStatementController::INTEGER, $type],
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    public function getMaxOrder($type, $id ){
        $q = "SELECT MAX(`order`) FROM ".$this->table." WHERE parent_type = ? AND parent_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $type],
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function add($data, $order){
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "parent_id=?, ";
        $q .= "parent_type=?, ";
        $q .= "`order`=? ";

        $params = [
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, $data['parent_type']],
            [DbStatementController::INTEGER, $order]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function updateFileName($id, $name){
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "filename=? ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $name],
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id, 'name' => $name ]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function addFileDescription($id, $langId, $name, $active){
        $name = prepareString($name, true);
        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "parent_id=?, ";
        $q .= "language_id=?, ";
        $q .= "name=?, ";
        $q .= "active=? ";

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, $langId],
            [DbStatementController::STRING, $name],
            [DbStatementController::INTEGER, $active]
        ];
        $this->saveEventInfo(json_encode(['id' => $id, 'name' => $name ]), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function updateFileDescription(array $data, int $langId){

        $q = "Update " . $this->tableDescription . " SET ";
        $q .= "name=?, ";
        $q .= "active=? ";
        $q .= "WHERE parent_id=? AND language_id=?";

        $params = [
            [DbStatementController::STRING, $data['name'][$langId]],
            [DbStatementController::STRING, $data['lang_active'][$langId] ? 1: 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];


        return $this->query($q, $params)->is_success();
    }

    public function getById($id){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return  $this->query($q, $params)->fetch_assoc();
    }

    public function getDescriptionsById($id){
        $q = "SELECT * FROM " . $this->tableDescription . " ";
        $q.= "WHERE parent_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id],
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    public function move(array $file, int $order_new): bool{
        if ($file['order'] < $order_new) {
            $q = "UPDATE " . $this->table . " SET `order`=`order`-1 ";
            $q .= "WHERE parent_id=? ";
            $q .= "AND parent_type=? ";
            $q .= "AND `order`>? ";
            $q .= "AND `order`<=? ";
            $params = [
                [DbStatementController::INTEGER, $file['parent_id']],
                [DbStatementController::INTEGER, $file['parent_type']],
                [DbStatementController::INTEGER, $file['order']],
                [DbStatementController::INTEGER, $order_new]
            ];

            $this->query($q, $params);

        } else {
            $q = "UPDATE " . $this->table . " SET `order`=`order`+1 ";
            $q .= "WHERE parent_id=? ";
            $q .= "AND parent_type=? ";
            $q .= "AND `order`>=? ";
            $q .= "AND `order`<?";
            $params = [
                [DbStatementController::INTEGER, $file['parent_id']],
                [DbStatementController::INTEGER, $file['parent_type']],
                [DbStatementController::INTEGER, $order_new],
                [DbStatementController::INTEGER, $file['order']]
            ];
            $this->query($q, $params);

        }
        $q = "UPDATE " . $this->table . " SET `order`=? WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $order_new],
            [DbStatementController::INTEGER, $file['id']]
        ];

        $statement = $this->query($q, $params);

        if ($statement->is_success())
            return true;

        return false;
    }
}
