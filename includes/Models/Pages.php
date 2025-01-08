<?php

namespace Models;
use Controllers\DbStatementController;
class Pages extends Model{
    protected $table;
    protected $tableDescription;
    protected $url;

    public function __construct($table = 'pages'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';
        $this->url = BASE_URL . '/upload/pages';

        parent::__construct($table);
    }

    public function getArticleById($id) {
        $q = "SELECT p.*, d.title, d.title_url FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];

        $statement = $this->query($q, $params);
        if ($row = $statement->fetch_assoc()) {
            $row['photo'] = $this->getPhotoUrl($row['photo']);
            return $row;
        } else {
            //$this->messages->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

    public function getPhotoUrl($filename) {
        if (!empty($filename)) {
            $filename_l = nameThumb($filename, '_l');
            $filename_d = nameThumb($filename, '_d');
            $filename_m = nameThumb($filename, '_m');
            $row = [];
            $row['name'] = $filename;
            $row['photo'] = URL_PREFIX . $this->url . '/' . $filename;
            $row['list'] = URL_PREFIX . $this->url . '/' . $filename_l;
            $row['detail'] = URL_PREFIX . $this->url . '/' . $filename_d;
            $row['main'] = URL_PREFIX . $this->url . '/' . $filename_m;
            return $row;
        } else {
            return false;
        }
    }

    public function getNames($langId){
        $q = "SELECT p.id, d.title FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";
        $q .= "ORDER BY d.title ASC";

        $params = [
            [DbStatementController::INTEGER, $langId]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function update(array $data, int $id = null): bool
    {

        // aktualizujemy artykul
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "auth=?, ";
        $q .= "gallery_id=?, ";
        $q .= "show_title=?, ";
        $q .= "template=?, ";
        $q .= "active = ?, ";
        $q .= "comments=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::INTEGER, $data['show_title']],
            [DbStatementController::STRING, $data['template_name'] ?? ''],
            [DbStatementController::INTEGER, isset($data['lang_active']) && !empty($data['lang_active']) ? 1 : 0],
            [DbStatementController::INTEGER, $data['comments']],
            [DbStatementController::INTEGER, $data['id']]
        ];
        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function create(array $data):int
    {
        $id = parent::create($data);

        if($id){
            $q = "UPDATE ".$this->table." SET ";
            $q .= "type = ? ";
            $q .= "WHERE id = ? ";

            $params = [
                [DbStatementController::INTEGER, $data['type']],
                [DbStatementController::INTEGER, $id]
            ];

            $this->query($q, $params);
        }

        return $id;
    }

    public function getMainPage(){
        $q = "SELECT p.*, d.title, d.title_url FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.main=1 ";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function loadArticles( $start, $limit, $onlyActive = true, $category_id = false, $category_path = false){

        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.active, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND p.main=0 ";

        if($onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";
        if($category_id)
            $q .= "AND p.category_id = ? ";
        $q .= "ORDER BY p.date_add DESC, p.id DESC ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];
        if($category_id)
            $params[] = [DbStatementController::INTEGER, $category_id];

        $params[] = [DbStatementController::INTEGER, $start];
        $params[]  = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }
}
