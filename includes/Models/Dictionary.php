<?php

namespace Models;
use Controllers\DbStatementController;

class Dictionary extends Model{

    protected $table;
    protected $tableDescription;
    protected $limit_admin;

    public function __construct($table = 'slownik', $limit = 20){
        $this->limit_admin = $limit;
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = DB_PREFIX . $table . '_description';
        parent::__construct($table);
    }


    function load(int $lang, array $tab) {
        if (!$lang) {
            $lang = lang_main;
        }

        $q = "SELECT p.label, d.value AS lang FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";
        $params = [
            [DbStatementController::INTEGER, $lang]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function loadArticlesAdmin($page){
        $start = ($page - 1) * $this->limit_admin;

        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "ORDER BY label ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $this->limit_admin]
        ];
        $statement = $this->query($q, $params);

        return $statement->get_all_rows();
    }

    public function loadDescriptionById($id, $langId = null){
        $q = "SELECT * FROM " . $this->tableDescription . " ";
        $q.= "WHERE parent_id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        $statement = $this->query($q, $params);

        return $statement->get_all_rows();
    }

    public function countArticlesAdmin(){
        $q = "SELECT count(*) as ile FROM " . $this->table . " ";

        $statement = $this->query($q);
        return $statement->get_result();
    }

    public function getId($parent_id, $lang_id){
        $q = "SELECT id FROM " . $this->tableDescription . " ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::INTEGER, $parent_id],
            [DbStatementController::INTEGER, $lang_id]
        ];
        $statement = $this->query($q, $params);

        return $statement->get_result();
    }

    public function update(array $params, int $id = null, string $table = ''):bool{
        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "value=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $params['value']],
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo($id, '', 1, __METHOD__, $this->table, '');
        return $this->query($q, $params)->is_success();
    }

    public function createDescription($params, $langId):int{

        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "value=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $params['value']],
            [DbStatementController::INTEGER, $params['parent_id']],
            [DbStatementController::INTEGER, $langId]
        ];
        $statement = $this->query($q, $params);
        $this->saveEventInfo($statement->insert_id(), '', 1, __METHOD__, $this->table, '');
        return $statement->insert_id();
    }

    public function create(array $data): int{
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "label=? ";
        $params = [
            [DbStatementController::STRING, $data['label']]
        ];
        $statement = $this->query($q, $params);
        $this->saveEventInfo($statement->insert_id(), '', __METHOD__, $this->table, '');
        return $statement->insert_id();
    }

    public function searchIds($keyword, $page){
        $start = ($page - 1) * $this->limit_admin;

        $q = "SELECT DISTINCT p.id AS id FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE p.label LIKE CONCAT('%', ?, '%') ";
        $q .= "OR d.value LIKE CONCAT('%', ?, '%') ";
        $q .= "ORDER BY label ASC ";
        $q .= "LIMIT " . $start . "," . $this->limit_admin;

        $params = [
            [DbStatementController::STRING, $keyword],
            [DbStatementController::STRING, $keyword]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getArticleById(int $id){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $statement = $this->query($q, $params);
        return $statement->fetch_assoc();
    }

    public function searchPages($keyword){
        $q = "SELECT COUNT (SELECT DISTINCT p.id AS id FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE p.label LIKE CONCAT('%', ?, '%') ";
        $q .= "OR d.value LIKE CONCAT('%', ?, '%') ";
        $q .= "ORDER BY label ASC) ";

        $params = [
            [DbStatementController::STRING, $keyword],
            [DbStatementController::STRING, $keyword]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_result();
    }

    public function checkLabel($label){
        $q = "SELECT p.label, d.value FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";        $q .= "WHERE p.label=? ";
        $q .= "AND d.language_id='" . lang_main . "' ";
        $params = [
            [DbStatementController::STRING, $label]
        ];
        $statement = $this->query($q, $params);

        return $statement->fetch_assoc();
    }

    public function getIdByLabel($label){
        $q = "SELECT id FROM " . $this->table . " ";
        $q .= "WHERE label=? ";

        $params = [
            [DbStatementController::STRING, trim($label)]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getAll($onlyActive = false, $toSkip = false){
        $q = "SELECT * FROM " . $this->table . " ";

        return $this->query($q)->get_all_rows();
    }

    public function getArticleDescriptions(int $id){
        $q = "SELECT * FROM " . $this->tableDescription . " ";
        $q .= "WHERE parent_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function truncate(){
        $q = "TRUNCATE " . $this->table . " ";
        $this->query($q);
        $q = "TRUNCATE " . $this->tableDescription . " ";
        $this->query($q);
    }

    public function getItem($page = 1){
        $start = ($page - 1) * $this->limit_admin;



        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "ORDER BY label ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $this->limit_admin]
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    public function search($keyword, $page){
        $start = ($page - 1) * $this->limit_admin;

        $q = "SELECT DISTINCT p.id AS id FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE p.label LIKE CONCAT('%', ?, '%') ";
        $q .= "OR d.value LIKE CONCAT('%', ?, '%') ";
        $q .= "ORDER BY label ASC ";
        //$q .= "LIMIT " . $start . "," . $this->limit_admin;

        $params = [
            [DbStatementController::STRING, $keyword],
            [DbStatementController::STRING, $keyword]
        ];

        return $this->query($q, $params)->get_all_rows();


    }
}
