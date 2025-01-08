<?php

namespace Models;
use Controllers\DbStatementController;

class Module extends Model{

    protected $table;
    protected $tableDescription;
    protected $class_dir;
    protected $modules_dir;
    protected $templates_dir;
    protected $limit_admin;
    protected $article_id;

    public function __construct($table = 'modules') {
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = DB_PREFIX . $table . '_description';
        $this->class_dir = ROOT_PATH . DIRECTORY_SEPARATOR . 'includes';
        $this->modules_dir = ROOT_PATH . DIRECTORY_SEPARATOR . 'modules';
        $this->templates_dir = ROOT_PATH . DIRECTORY_SEPARATOR . 'templates';
        parent::__construct($table);
    }

    // funkcja dodaje nowy artykul
    public function create(array $post): int {


        $post['title_url'] = strtolower(prepareString($post['name'], true));
        $post['table_name'] = prepareString($post['table_name'], true);
        $post['class_name'] = prepareString($post['class_name'], true);
        $post['class_file'] = prepareString($post['class_file'], true);
        $post['templates_dir'] = prepareString($post['templates_dir'], true);

        $frontend = isset($post['frontend']) ? $post['frontend'] : 0;
        $backend = isset($post['backend']) ? $post['backend'] : 0;
        $active = isset($post['active']) ? $post['active'] : 0;
        $auth = isset($post['auth']) ? $post['auth'] : 0;

        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "name=?, ";
        $q .= "file_name=?, ";
        $q .= "table_name=?, ";
        $q .= "class_name=?, ";
        $q .= "templates_dir=?, ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "frontend=?, ";
        $q .= "backend=?, ";
        $q .= "active=?, ";
        $q .= "auth=? ";

        $params = [
            [DbStatementController::STRING, $post['name']],
            [DbStatementController::STRING, $post['title_url']],
            [DbStatementController::STRING, $post['table_name']],
            [DbStatementController::STRING, $post['class_name']],
            [DbStatementController::STRING, $post['templates_dir']],
            [DbStatementController::INTEGER, $post['op_page_title']],
            [DbStatementController::INTEGER, $post['op_page_keywords']],
            [DbStatementController::INTEGER, $post['op_page_description']],
            [DbStatementController::INTEGER, $frontend],
            [DbStatementController::INTEGER, $backend],
            [DbStatementController::INTEGER, $active],
            [DbStatementController::INTEGER, $auth]
        ];
        $statement = $this->query($q, $params);
        if ($statement->is_success()) {
            $this->article_id = $statement->insert_id();

            foreach ($post['page_title'] as $i => $page_title) {
                $post['page_title'][$i] = prepareString($page_title, true);
                $post['page_keywords'][$i] = prepareString($post['page_keywords'][$i], true);
                $post['page_description'][$i] = prepareString($post['page_description'][$i], true);

                $q = "INSERT INTO " . $this->tableDescription . " SET ";
                $q .= "page_title=?, ";
                $q .= "page_keywords=?, ";
                $q .= "page_description=?, ";
                $q .= "parent_id=?, ";
                $q .= "language_id=? ";
                $params = [
                    [DbStatementController::STRING, $post['page_title'][$i]],
                    [DbStatementController::STRING, $post['page_keywords'][$i]],
                    [DbStatementController::STRING, $post['page_description'][$i]],
                    [DbStatementController::INTEGER, $this->article_id],
                    [DbStatementController::INTEGER, $i]
                ];
                $this->query($q, $params);
            }
            $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
           return $this->article_id;
        } else {
            return 0;
        }
    }


    public function update(array $post, int $id = null): bool {

        $q = "UPDATE " . $this->table . " SET ";
        $q .= "name=?, ";
        $q .= "file_name=?, ";
        $q .= "table_name=?, ";
        $q .= "class_name=?, ";
        $q .= "templates_dir=?, ";
        $q .= "thumbsize_list=?, ";
        $q .= "thumbsize_detail=?, ";
        $q .= "thumbsize_scale=?, ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "frontend=?, ";
        $q .= "backend=?, ";
        $q .= "active=?, ";
        $q .= "auth=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $post['name']],
            [DbStatementController::STRING, strtolower($post['title_url'])],
            [DbStatementController::STRING, $post['table_name']],
            [DbStatementController::STRING, $post['class_name']],
            [DbStatementController::STRING, $post['directory_name']],
            [DbStatementController::STRING, $post['thumbsize_list']],
            [DbStatementController::STRING, $post['thumbsize_detail']],
            [DbStatementController::STRING, $post['thumbsize_scale']],
            [DbStatementController::INTEGER, $post['op_page_title']],
            [DbStatementController::INTEGER, $post['op_page_keywords']],
            [DbStatementController::INTEGER, $post['op_page_description']],
            [DbStatementController::INTEGER, $post['frontend']],
            [DbStatementController::INTEGER, $post['backend']],
            [DbStatementController::INTEGER, $post['active']],
            [DbStatementController::INTEGER, $post['auth']],
            [DbStatementController::INTEGER, $post['id']]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $statement = $this->query($q, $params)->is_success();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "title=?, ";
        $q .= "title_url = ?, ";
        $q .= "subtitle=?, ";
        $q .= "content_short=?, ";
        $q .= "content=? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, make_url($data['title'][$langId])],
            [DbStatementController::STRING, $data['subtitle'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function createDescription($data, $langId){
        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    function setActive($id, $active) {
        $q = "UPDATE " . $this->table . " SET ";
        if ($active) {
            $q .= "active='1' ";
        } else {
            $q .= "active='0' ";
        }
        $q .= "WHERE id=? ";

        $params = array(
            array(DbStatementController::INTEGER, $id)
        );
        $statement = $this->query($q, $params);
        if ($statement->is_success()) {
            $this->setInfo($GLOBALS['_ADMIN_SET_ACTIVE_SUCCESS']);
            return true;
        } else {
            $this->setError($GLOBALS['_ADMIN_SET_ACTIVE_ERROR']);
            return false;
        }
    }

    // funkcja kasuje artykul z bazy
    function delete(int $id, $background = false): bool {
        $q = "DELETE FROM " . $this->table . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id'  => $id ]), '', 2, __METHOD__, $this->table);
        $statement = $this->query($q, $params);
        if ($statement->is_success()) {
            $q = "DELETE FROM " . $this->tableDescription . " WHERE parent_id=?";
            $this->query($q, $params);
            return true;
        }

        return false;
    }

    public function getById($id){
        return $this->getArticleById($id);
    }


    public function getArticleById($id) {
        $q = "SELECT p.*, d.title,d.title_url FROM " . $this->table . " p ";
        $q .= "LEFT JOIN ". $this->tableDescription . " d ON(p.id = d.parent_id) ";
        $q .= "WHERE p.id=? AND d.language_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID]
        ];
        return $this->query($q, $params)->fetch_assoc();

    }

    // funkcja wczytuje opis do modułu o podanym id
    function getDescriptionById($id) {
        $q = "SELECT * FROM " . $this->tableDescription . " ";
        $q.= "WHERE parent_id=? ";
        $params = array(
            array(DbStatementController::INTEGER, $id)
        );
        $statement = $this->db->query($q, $params);
        $opis = array();
        while ($row = $statement->fetch_assoc()) {
            $opis[$row['language_id']] = $row;
        }
        return $opis;
    }


    public function getModuleConf($url) {
        $q = "SELECT p.*, d.title, d.title_url, d.subtitle, d.content_short, d.content FROM " . $this->table . " p ";
        $q .= "LEFT JOIN ". $this->tableDescription." d ON (p.id = d.parent_id) ";
        $q .= "WHERE d.title_url=? AND d.language_id=?";

        $params = [
            [DbStatementController::STRING, $url],
            [DbStatementController::INTEGER, _ID]
        ];

        $statement = $this->query($q, $params);
        if ($row = $statement->fetch_assoc()) {
            return $row;
        }
        return false;
    }


    // funkcja laduje wszystkie artykuly w panelu admina
    function loadArticles($start = null, $limit = null, $onlyActive = true, $category_id = false, $category_path = false) {
        $q = "SELECT * FROM " . $this->table . " ";

        $statement = $this->query($q);
        $articles = array();
        while ($row = $statement->fetch_assoc()) {
            $articles[] = $row;
        }
        return $articles;
    }

    function LoadNames() {
        $q = "SELECT id, class_name, name FROM " . $this->table . " ";
        $q .= "ORDER BY name ASC ";

        $statement = $this->query($q);
        $articles = array();
        while ($row = $statement->fetch_assoc()) {
            $articles[] = $row;
        }
        return $articles;
    }

    function generateModule($post) {
        return false;
    }

    public function getIdByName($name){
        $q = "SELECT id FROM ". $this->table ." WHERE file_name=?";
        $params = [
            [DbStatementController::STRING, $name]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getClassName($name){
        $q = "SELECT class_name FROM ". $this->table ." WHERE file_name = ?";
        $params = [
            [DbStatementController::STRING, $name]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getIdByTable($name){
        $q = "SELECT id FROM ". $this->table ." WHERE table_name = ?";
        $params = [
            [DbStatementController::STRING, $name]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getPages($onlyActive = false, $category_str = null, $category_id = null, $category_path = false){
        $q = "SELECT COUNT(p.id) FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID]
        ];
        $statement = $this->query($q, $params);

        return $statement->get_result();
    }

    public function getUrlById($id){
        $q = "SELECT title_url FROM ".$this->tableDescription." ";
        $q .= "WHERE parent_id = ? AND language_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->get_result();
    }
    public function getModuleUrls($str, $langIds){
        $q = "SELECT language_id, title_url FROM ". $this->tableDescription." ";
        $q .= "WHERE parent_id IN (SELECT parent_id FROM ".$this->tableDescription ." WHERE title_url = ? ) ";
        $q .= "AND language_id IN (SELECT id FROM languages WHERE gen_title = 1) AND language_id != "._ID." ";
        $q .= "AND language_id IN(".implode(',', $langIds).")";

        $params = [
            [DbStatementController::STRING, $str],

        ];

        return $this->query($q, $params)->get_all_rows();
    }

}
