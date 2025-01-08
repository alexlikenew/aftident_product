<?php

namespace Models;

use Controllers\DbStatementController;

class ModelCategorized extends Model
{
    public function countItems($id, $onlyActive = false){
        $q = "SELECT count(id) FROM ".$this->table." ";
        $q .= "WHERE category_id = ? ";
        if($onlyActive)
            $q .= "AND active = 1 ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getItemsByCategory($categoryId, $onlyActive = true){
        $q = "SELECT p.id, p.category_id, d.title, d.title_url FROM " . $this->table ." p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON(p.id=d.parent_id)  WHERE d.language_id = ? AND p.category_id = ? " ;
        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $categoryId],
        ];

        if($onlyActive){
            $q .= " AND p.active=1 AND d.active=1 ";
        }

        return $this->query($q, $params)->get_all_rows();
    }

    public function isCategoryEmpty($id){
        $q = "SELECT id FROM ".$this->table." WHERE category_id = ? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getCategoriesQuantity($onlyActive = true){
        $q = "SELECT count(id) as quantity, category_id FROM " .$this->table. " ";
        if($onlyActive)
            $q .= "WHERE active = 1 ";
        $q .= " GROUP BY category_id ";

        return $this->query($q)->get_all_rows();
    }

}