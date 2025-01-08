<?php

namespace Models;

use Controllers\DbStatementController;

class Redirects extends Model{
    protected $table;
    protected $limit_admin;

    public function __construct($table = 'redirects')
    {
        $this -> table	= DB_PREFIX. $table;
        $this -> limit_admin = 50;
        parent::__construct($table);
    }

    function checkRedirect(string $url): ?string
    {
        $q = "SELECT dst_url from ".$this->table." where src_url=? and dst_url != '' and active='1' limit 1";
        $params = [
            [DbStatementController::STRING, $url],

        ];

        $statement = $this->query($q, $params);
        if ($row = $statement->fetch_assoc())
        {
            return $row['dst_url'];
        }

        return false;
    }

    public function search($keyword){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "WHERE dst_url like ? OR ";
        $q .= "src_url like ? ";
        $q .= "ORDER BY id ";

        $params = [
            [DbStatementController::STRING, '%' . $keyword . '%'],
            [DbStatementController::STRING, '%' . $keyword . '%'],
        ];

        return  $this->query($q, $params)->get_all_rows();
    }

    public function delete(int $id = null, $background = false): bool{
        $q = "DELETE FROM " . $this->table . " WHERE id = ? LIMIT 1";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode($data), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function create(array $data): int{
        $q = "insert into " . $this->table . " set ";
        $q .= "src_url=?, ";
        $q .= "dst_url=?, ";
        $q .= "active='1' ";
        $params = [
            [DbStatementController::STRING, $data['src_url']],
            [DbStatementController::STRING, $data['dst_url']]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function saveAll($data){
        foreach ($data['src_url'] as $key => $val) {
            $q = "UPDATE " . $this->table . " set ";
            $q .= "src_url=?, ";
            $q .= "dst_url= ";
            $q .= "where id=? LIMIT 1";

            $params = [
                [DbStatementController::STRING, $this->realEscapeString($val)],
                [DbStatementController::STRING, $data['dst_url'][$key]],
                [DbStatementController::STRING, $key]
            ];

            $this->query($q, $params);
        }
    }

    public function getAmount(){
        $q = "SELECT count(*) as amount FROM " . $this->table . " ";

        return $this->query($q)->get_result();
    }

    public function loadArticles($start, $limit, $onlyActive = false, $category_id = false, $category_path = false)
    {
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "ORDER BY id desc ";
        $q .= "LIMIT ?,?";
        $params = [
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $limit]
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    public function import($name){
        $q = "insert into " . $this->table . " set ";
        $q .= "src_url=?, ";
        $q .= "dst_url='', ";
        $q .= "active='1' ";
        $params = [
            [DbStatementController::STRING, $name]
        ];
        return $this->query($q, $params)->insert_id();
    }


}
