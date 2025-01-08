<?php



namespace Models;


use Controllers\DbStatementController;

class Comments extends Model
{
    protected $table;

    public function __construct($table = 'comments'){
        parent::__create($table);
        $this->table = $table;
    }

    public function create(array $data): int{
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "parent_id=?, ";
        $q .= "user_id=?, ";
        $q .= "title=?, ";
        $q .= "content=?, ";
        $q .= "author=?, ";
        $q .= "rate=?, ";
        $q .= "ip=?, ";
        $q .= "`group`=?, ";
        $q .= "date_add=NOW() ";

        $params = [
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, $data['user_id']],
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::STRING, $data['content']],
            [DbStatementController::STRING, $data['author']],
            [DbStatementController::STRING, $data['rate']],
            [DbStatementController::STRING, $data['ip']],
            [DbStatementController::INTEGER, $data['group']]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function update(array $data, int $id = null): bool{
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "title=?, ";
        $q .= "content=?, ";
        $q .= "author=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::STRING, $data['content']],
            [DbStatementController::STRING, $data['author']],
            [DbStatementController::INTEGER, $data['id']]
        ];
        if($this->query($q, $params)->is_success()){
            $this->saveEventInfo(json_encode($data), ''. 1, __METHOD__, $this->table);
            return true;
        }
        return false;
    }

    public function delete(int $id, $background = false): bool{
        $q = "DELETE FROM " . $this->table ." WHERE id = ?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        if($this->query($q, $params)->is_success())
        {
            $this->saveEventInfo($id, '', 2, __METHOD__, $this->table);
            return true;
        }
        return false;
    }

    public function getAllComments($group, $start, $limit){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "WHERE `group`=? ";
        $q .= "ORDER BY parent_id ASC, date_add ASC ";
        $q .= "LIMIT ?,?" ;

        $params = [
            [DbStatementController::INTEGER, $group],
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $limit]
        ];
        return $this->query($q, $params)->get_all_rows();
    }
}
