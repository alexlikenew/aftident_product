<?php


namespace Models;
use Controllers\DbStatementController;

class Register extends Model
{
    protected $table;

    public function __construct($table = 'rejestr'){
        $this->table = $table;
        parent::__construct($table);
    }

    public function getPages($onlyActive = false, $category_str = null, $category_id = null, $category_path = false){
        $q = "SELECT COUNT(p.id) FROM " . $this->table . " p ";

        $statement = $this->query($q);

        return $statement->get_result();
    }

    public function loadArticles($start, $limit, $onlyActive = true, $category_id = false, $category_path = false){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "ORDER BY date_add DESC LIMIT  ?,? ";
        $params = [
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $limit]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function delete(int $id, $backgroundd = false): bool{
        $q = "DELETE FROM " . $this->table ." WHERE id=?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->is_success();
    }
}
