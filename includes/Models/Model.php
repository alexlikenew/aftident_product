<?php
// REGISTER ACTIONS: 0 - Dodano, 1 - Zmieniono, 2 - Usunięto

namespace Models;

use Interfaces\ModelInterface;

use Traits\DbConnection;
use Controllers\DbStatementController;

abstract class Model implements ModelInterface{
    use DbConnection;
    protected $table;
    protected $tableDescription;
    protected $limit_admin;
    protected $tableRegister;
    protected $tableSearch;
    protected $tableBlock2Item;
    protected $tableBlocks;
    protected $tableBlocksDescription;
    protected $tableTypes;
    protected $tableFiles;
    protected $tableFilesDescription;
    protected $tableCategory;
    protected $tableCategoryDescription;

    public function __construct($table, $isCategorized = false){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table.'_description';
        $this->tableRegister = 'rejestr';
        $this->limit_admin = 60;
        $this->tableSearch = DB_PREFIX . 'search';
        $this->tableBlocks = DB_PREFIX . 'blocks';
        $this->tableBlocksDescription = $this->tableBlocks . '_description';
        $this->tableBlock2Item = DB_PREFIX . 'block_to_item';
        $this->tableTypes = DB_PREFIX.'block_types';
        $this->tableFiles = DB_PREFIX.'files';
        $this->tableFilesDescription = $this->tableFiles.'_description';

        if($isCategorized){
            $this->tableCategory = DB_PREFIX . $this->table.'_categories';
            $this->tableCategoryDescription = DB_PREFIX . $this->table.'_categories_description';
        }


        if(!$this->getConnection())
            $this->setConnection(base64_decode(DB_HOST), base64_decode(DB_USER), base64_decode(DB_PASS), base64_decode(DB_NAME));

    }

    function getTable() {
        return $this->table;
    }


    public function create(array $data):int
    {
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "auth=?, ";
        $q .= "gallery_id=?, ";
        $q .= "show_title=?, ";
        $q .= "active=?, ";
        $q .= "main=?, ";
        $q .= "comments=? ";

        $params = [
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::INTEGER, $data['show_title'] ?? 0],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active']))? 1 : 0],
            [DbStatementController::INTEGER, $data['main'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['comments'] ?? 0],
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function read(int $id):self
    {
        // TODO: Implement read() method.
        return $this;
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
        //$q .= "active=?, ";
        $q .= "main=?, ";
        $q .= "comments=?, ";
        $q .= "date_add=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::INTEGER, $data['show_title']],
            //[DbStatementController::INTEGER, $data['active']],
            [DbStatementController::INTEGER, $data['main'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['comments']],
            [DbStatementController::STRING, $data['date_add']],
            [DbStatementController::INTEGER, $data['id']]
        ];
        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function createDescription($data, $langId){

        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "title=?, ";
        $q .= "title_url=?, ";
        $q .= "subtitle=?, ";
        $q .= "content=?, ";
        $q .= "content_short=?, ";
        $q .= "tagi=?, ";
        $q .= "active=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['subtitle'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['tagi'][$langId]],
            [DbStatementController::INTEGER, $data['lang_active'][$langId] ? 1: 0],
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
        $q .= "title_url=?, ";
        $q .= "subtitle=?, ";
        $q .= "content=?, ";
        $q .= "content_short=?, ";
        $q .= "tagi=?, ";
        $q .= "active=? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['subtitle'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['tagi'][$langId]],
            [DbStatementController::INTEGER, isset($data['lang_active'][$langId]) ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getOtherUrls($id, $langIds){
        $q = "SELECT language_id, title_url FROM ".$this->tableDescription." ";
        $q .= "WHERE parent_id IN (SELECT id FROM ".$this->table." WHERE active = 1 AND id = ?) ";
        $q .= "AND language_id IN(".implode(',', $langIds).") ";
        $q .= "AND active=1 ";
        $q .= "AND language_id IN (SELECT id FROM languages WHERE gen_title = 1)";

        $params = [
            [DbStatementController::INTEGER, $id],

        ];


        return $this->query($q, $params)->get_all_rows();
    }

    public function delete(int $id, $backgroundd = false):bool
    {
        $this->deleteDescriptions($id);
        $q = "DELETE FROM ". $this->table ." WHERE id = ?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function deleteDescriptions($id){
        $q = "DELETE FROM ".$this->tableDescription." WHERE parent_id=?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function setActive($id, $active){
        $q = "UPDATE ". $this->table . " SET active=? WHERE id = ?";
        $params = [
            [DbStatementController::INTEGER, $active ? 1 : 0],
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function setDescriptionInactive($id){
        $q = "UPDATE ".$this->tableDescription." SET active = 0 WHERE parent_id = ? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q,$params);
    }

    public function getById($id){
        $q = "SELECT p.*, d.title, d.title_url FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function getByIds(array $ids){
        $q = "SELECT p.*, d.title, d.title_url FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id IN (".implode(',', $ids).")";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    public function loadDescriptionById($id, $langId = null){
        $q = "SELECT * FROM " . $this->tableDescription . " ";
        $q.= "WHERE parent_id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        if($langId){
            $q .= "AND language_id=? ";
            $params[] = [DbStatementController::INTEGER, $langId];
        }


        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getPhoto($id){
        $q = "SELECT photo FROM " . $this->table . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function deletePhoto($id){
        $q = "UPDATE " . $this->table . " SET photo='' WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updatePhoto($id, $filename){
        $q = "UPDATE ". $this->table . " SET photo=? WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $filename],
            [DbStatementController::INTEGER, $id]
        ];

        $this->saveEventInfo(json_encode(['id' => $id]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function loadArticles( $start, $limit, $onlyActive = true, $category_id = false, $category_path = false){

        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.active, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";

        if($onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";
        if($category_path)
            $q .= "AND p.category_id IN (SELECT id FROM ".$this->tableCategory." WHERE path_id LIKE CONCAT(?, '%')) ";
        elseif($category_id)
            $q .= "AND p.category_id = ? ";
        $q .= "ORDER BY p.date_add ASC, p.id ASC ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            ];

        if($category_path)
            $params[] = [
                DbStatementController::STRING, $category_path
            ];
        elseif($category_id)
            $params[] = [DbStatementController::INTEGER, $category_id];

        $params[] = [DbStatementController::INTEGER, $start];
        $params[]  = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getLastItems($limit){
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";
        $q .= "AND p.active=1 AND d.active = 1 ";
        $q .= "ORDER BY p.date_add DESC, p.id DESC ";
        $q .= "LIMIT ? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $limit]
        ];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getRandomItems($limit, $toSkip = false){
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.tagi  FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";
        $q .= "AND p.active=1 ";
        if($toSkip)
            $q .= "AND p.id != ? ";
        $q .= "ORDER BY RAND() ";
        $q .= "LIMIT ? ";

        $params[] = [DbStatementController::INTEGER, _ID];
        if($toSkip)
            $params[] = [DbStatementController::INTEGER, $toSkip];
        $params[] = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getMainItems($limit, $toSkip = false){
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.tagi  FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";
        $q .= "AND p.active=1 ";
        if($toSkip)
            $q .= "AND p.id != ? ";
        $q .= "AND p.main = 1 ";
        $q .= "ORDER BY RAND() ";
        $q .= "LIMIT ? ";

        $params[] = [DbStatementController::INTEGER, _ID];
        if($toSkip)
            $params[] = [DbStatementController::INTEGER, $toSkip];
        $params[] = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getPages($onlyActive = false, $category_str = null, $category_id = null, $category_path = false){

        $q = "SELECT COUNT(p.id) FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        if($category_str)
            $q .= "LEFT JOIN ".$this->tableCategoryDescription." cd ON(p.category_id = cd.parent_id) ";
        $q .= "WHERE 1 ";
        if($category_str){
            $q .= "AND cd.title_url = ? ";
        }
        elseif($category_id){
            $q .= "AND p.category_id = ?  ";
        }

        if($category_path)
            $q .= "AND p.category_id IN (SELECT id FROM ".$this->tableCategory." WHERE path_id LIKE CONCAT( ?, '%')) ";


        $q .= "AND d.language_id=? ";



        if(!$onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";


        if($category_str){
            $params[] = [
                DbStatementController::STRING, $category_str
            ];
        }
        if($category_id){
            $params[] = [
                DbStatementController::INTEGER, $category_id,
            ];
        }
        if($category_path)
            $params[] = [
                DbStatementController::STRING, $category_path
            ];

        $params[] = [
            DbStatementController::INTEGER, _ID
        ];
        //echo($q); dd($params);
        $statement = $this->query($q, $params);

        return $statement->get_result();
    }

    public function updateItemsOrder($item, $order_new){

        if ($item['order'] < $order_new) {
            $q = "UPDATE " . $this->table . " SET `order`=`order`-1 ";
            $q .= "WHERE `order`>? ";
            $q .= "AND `order`<=? ";
            $q .= "";
            $params = [
                [DbStatementController::INTEGER, $item['order']],
                [DbStatementController::INTEGER, $order_new]
            ];
            $this->query($q, $params);

        } else {
            $q = "UPDATE " . $this->table . " SET `order`=`order`+1 ";
            $q .= "WHERE `order`>=? ";
            $q .= "AND `order`<? ";
            $params = [
                [DbStatementController::INTEGER, $order_new],
                [DbStatementController::INTEGER, $item['order']]
            ];
            $this->query($q, $params);

        }
        $q = "UPDATE " . $this->table . " SET ";
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

    public function getArticle($str, $isActive = true)
    {
        $q = "SELECT p.*, d.page_title, d.page_keywords, d.page_description, d.title, d.title_url, d.subtitle, d.content, d.content_short, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND d.title_url=? ";

        if($isActive)
            $q .= "AND p.active=1 AND d.active=1 ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::STRING, $str]
        ];

        $statement = $this->query($q, $params);

        return  $statement->fetch_assoc();
    }

    //public function getOther

    public function getForSubmenu(){
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? AND p.active=1 ";
        $q .= "ORDER BY p.date_add DESC, p.id DESC ";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    public function getRegister($page){
        if ($page < 1) {
            $page = 1;
        }
        $start = ($page - 1) * $this->limit_admin;

        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "ORDER BY date_add DESC ";
        $q .= "LIMIT ?,?" ;
        $params = [
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $this->limit_admin]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function saveEventInfo($content, $url, $action, $method, $type){
        $q = "INSERT INTO " . $this->tableRegister . " SET ";
        $q .= "date_add=NOW(), ";
        $q .= "content=?, ";
        $q .= "url=?, ";
        $q .= "ip=?, ";
        $q .= "method=?, ";
        $q .= "action=?, ";
        $q .= "type=?, ";
        $q .= "login=? ";
        $params = [
            [DbStatementController::STRING, $content],
            [DbStatementController::STRING, $url],
            [DbStatementController::STRING, $_SERVER['REMOTE_ADDR']],
            [DbStatementController::STRING, $method],
            [DbStatementController::INTEGER, $action],
            [DbStatementController::STRING, $type],
            [DbStatementController::STRING, (isset($_SESSION['user']['login'])?$_SESSION['user']['login']:"SYSTEM")]
        ];
        return $this->query($q, $params)->is_success();
    }

    public function searchItems($words, $page = 1, $onlyActive = true){

        $q = "SELECT p.id, p.date_add, p.photo, p.active, d.title, d.title_url, d.subtitle, d.content_short FROM ". $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON (p.id=d.parent_id) ";
        $q .= "WHERE d.language_id=? AND (d.title LIKE CONCAT('%', ?, '%') OR d.subtitle LIKE CONCAT('%', ?, '%') OR d.content LIKE CONCAT('%', ?, '%') OR d.content_short LIKE CONCAT('%', ?, '%')) ";
        if($onlyActive)
            $q .= "AND p.active = 1 AND d.active=1";
        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::STRING, $words],
            [DbStatementController::STRING, $words],
            [DbStatementController::STRING, $words],
            [DbStatementController::STRING, $words],
        ];
        //dump($q); dd($params);
        return $this->query($q, $params)->get_all_rows();
    }

    public function getAll($onlyActive = true, $toSkip = false){

        $q = "SELECT p.id, p.photo, d.title, d.title_url, d.content, d.content_short FROM " . $this->table ." p ";
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

    public function countArticle($id){
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "`count`=`count`+1 ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->is_success();

    }

    public function keywordExists($keyword){
        $q = "SELECT id FROM " . $this->tableSearch . " ";
        $q .= "WHERE keyword=? ";
        $params = [
            [DbStatementController::STRING, $keyword]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function updateSearchResult($keyword, $quantity){
        $q = "UPDATE " . $this->tableSearch . " SET ";
        $q .= "`count`=`count`+1, ";
        $q .= "`result_quantity`=? ";
        $q .= "WHERE keyword=? ";
        $params = [
            [DbStatementController::INTEGER, $quantity],
            [DbStatementController::STRING, $keyword]
        ];
        $this->query($q, $params);
    }

    public function createSearchResult($keyword, $quantity){
        $q = "INSERT INTO " . $this->tableSearch . " SET";
        $q .= "`count`=1, ";
        $q .= "result_quantity=?, ";
        $q .= "keyword=? ";
        $params = [
            [DbStatementController::INTEGER, $quantity],
            [DbStatementController::STRING, $keyword]
        ];

        $this->query($q, $params);
    }

    /**
     * BLOCKS
     */


    public function createBlock($data){
        $q = "INSERT INTO " . $this->tableBlocks . " SET ";
        $q .= "id_product = ?, ";
        $q .= "active = ? ";
        $params = [
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active'])) ? 1 : 0]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function saveBlockDescription($data){

        $q = "INSERT INTO " . $this->tableBlocksDescription . " SET ";
        $q .= "parent_id=?, ";
        $q .= "language_id=?, ";
        $q .= "title=?, ";
        $q .= "content=?, ";
        $q .= "active=? ";

        $params = [
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, $data['language_id']],
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::STRING, $data['content']],
            [DbStatementController::INTEGER, $data['active']]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function updateBlockDescription($data){

        $q = "UPDATE " . $this->tableBlocksDescription . " SET ";
        $q .= "title=?, ";
        $q .= "content=?, ";
        $q .= "active=? ";
        $q .= "WHERE parent_id=? AND language_id=? ";

        $params = [
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::STRING, $data['content']],
            [DbStatementController::INTEGER, $data['active']],
            [DbStatementController::INTEGER, $data['parent_id']],
            [DbStatementController::INTEGER, $data['language_id']],
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getBlockById($id, $onlyActive = true){
        $q = "SELECT p.*, t.template_file, t.has_photo, t.has_video, t.has_subtitle, t.has_url, t.has_quote, t.is_parent, t.is_child, d.title FROM ".$this->tableBlocks . " p ";
        $q .= "LEFT JOIN " . $this->tableBlocksDescription . " d ON(p.id = d.parent_id) ";
        $q .= "LEFT JOIN ".$this->tableTypes . " t ON(t.id=p.type_id)";
        $q .= "WHERE p.id=? AND d.language_id=? ";

        if($onlyActive){
            $q .= "AND p.active=1 AND d.active=1 ";
        }

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }


    public function deleteBlockDescriptions($id){
        $q = "DELETE FROM ".$this->tableBlocksDescription." WHERE parent_id=? ";
        $params= [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getBlockDescription($id, $langId = false){
        $q = "SELECT * FROM ". $this->tableBlocksDescription . " WHERE parent_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        if($langId){
            $q .= "AND language_id=? ";
            $params[] = [DbStatementController::INTEGER, $langId];

            return $this->query($q, $params)->fetch_assoc();
        }
        return $this->query($q, $params)->get_all_rows();
    }

    public function setBlockActive($id, $isActive = true){
        $q = "UPDATE ".$this->tableBlocks." SET ";
        $q .= "active=? WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $isActive ? 1 : 0],
            [DbStatementController::INTEGER, $id],
        ];

        return $this->query($q, $params)->is_success();
    }

    public function deleteBlock($id){
        $q = "DELETE FROM ".$this->tableBlocks." WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->is_success();
    }

    /**
     * @param $id - item id
     * @param $typeId - module id
     * @param $onlyActive
     * @return array|false
     */
    public function getBlocksById($id, $typeId, $onlyActive = true, $onlyIds = false){

        $q = "SELECT p.id ";
        if(!$onlyIds)
            $q .= ", p.name, p.gallery_id, p.date_add, p.active, p.photo, p.video, p.parent_id, p.module_id, p.items_type, p.type_id, p.quantity_to_load, p.url_type, bt.is_agregated, d.title, d.title_url, d.h_type, d.content, d.subtitle, d.url, d.url_title, d.url2, d.url_title2, d.url_target1_id, bt.id AS type_id, bt.photo As typePhoto, bt.title as type_name, bt.template_file, bt.is_parent, bt.is_child, b2i.id as assign_id, b2i.`order` as sequence ";
        $q .= "FROM ".$this->tableBlocks . " p ";
        $q .= "LEFT JOIN ". $this->tableBlocksDescription ." d ON(d.parent_id=p.id) ";
        $q .= "LEFT JOIN ". $this->tableTypes . " bt ON (bt.id = p.type_id) ";
        $q .= "LEFT JOIN ". $this->tableBlock2Item . " b2i ON(p.id=b2i.block_id) ";
        $q .= " WHERE b2i.module_id=? AND b2i.item_id=? ";
        $q .= "AND d.language_id=? ";
        if($onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";

        $q .= "ORDER BY b2i.`order` ASC";

        $params = [
            [DbStatementController::INTEGER, $typeId],
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID],
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function assignBlock($blockId, $itemId, $moduleId, $order = 1){
        $q = "INSERT INTO ". $this->tableBlock2Item." SET ";
        $q .= "block_id=?, ";
        $q .= "module_id=?, ";
        $q .= "item_id=?, ";
        $q .= "`order`=? ";
        $order = $this->getMaxBlockOrder($itemId, $moduleId) + 1;

        $params = [
            [DbStatementController::INTEGER, $blockId],
            [DbStatementController::INTEGER, $moduleId],
            [DbStatementController::INTEGER, $itemId],
            [DbStatementController::INTEGER, $order],
        ];

        return $this->query($q, $params)->insert_id();
    }

    public function getMaxBlockOrder($itemId, $moduleId){
        $q = "SELECT MAX(`order`) FROM ".$this->tableBlock2Item." WHERE item_id=? AND module_id=? ";
        $params = [
            [DbStatementController::INTEGER, $itemId],
            [DbStatementController::INTEGER, $moduleId]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getBlockAssignedData($id){
        $q = "SELECT * FROM ". $this->tableBlock2Item." WHERe id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }

    public function moveBlockDown($item, $order){

        $q = "UPDATE " . $this->tableBlock2Item . " SET `order`=`order`-1 ";
        $q .= "WHERE `order`>=? ";
        $q .= "AND `order`<=? ";
        $q .= "AND module_id=? ";
        $q .= "AND item_id=? ";
        $params = [
            [DbStatementController::INTEGER, $item['order']],
            [DbStatementController::INTEGER, $order],
            [DbStatementController::INTEGER, $item['module_id']],
            [DbStatementController::INTEGER, $item['item_id']],
        ];

        $this->query($q, $params);
        return  $GLOBALS['_ADMIN_MOVE_DOWN_MANY'];
    }

    public function moveBlockUp($item, $order){
        $q = "UPDATE " . $this->tableBlock2Item . " SET `order`=`order`+1 ";
        $q .= "WHERE `order` >=? ";
        $q .= "AND `order`<=? ";
        $q .= "AND module_id=? ";
        $q .= "AND item_id=? ";
        $params = [
            [DbStatementController::INTEGER, $order],
            [DbStatementController::INTEGER, $item['order']],
            [DbStatementController::INTEGER, $item['module_id']],
            [DbStatementController::INTEGER, $item['item_id']],
        ];
        $this->query($q, $params);
        return $GLOBALS['_ADMIN_MOVE_UP_MANY'];
    }

    public function updateBlockOrder($id, $order){

        $q = "UPDATE " . $this->tableBlock2Item . " SET ";
        $q .= "`order`=? ";
        $q .= "WHERE id=?";
        $params = [
            [DbStatementController::INTEGER, $order],
            [DbStatementController::INTEGER, $id],
        ];

        return $this->query($q, $params)->is_success();
    }

    /**
     * END BLOCKS
     */

    public function updateVideo($name, $data){
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "video=?, ";
        $q .= "video_title=? ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $name],
            [DbStatementController::STRING, $data['video_title']],
            [DbStatementController::INTEGER, $data['id']],
        ];
        $this->query($q, $params)->is_success();
    }

    public function deleteVideo($id){
        $q = "UPDATE ".$this->table." SET ";
        $q .= "video='', ";
        $q .= "video_title='' ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function deleteBlockAssign($data){
        $q = "DELETE FROM ".$this->tableBlock2Item." WHERE ";
        $q .= "block_id=? AND ";
        $q .= "module_id=? AND ";
        $q .= "item_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $data['block_id']],
            [DbStatementController::INTEGER, $data['item_module']],
            [DbStatementController::INTEGER, $data['parent_id']]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getItemFiles($id, $moduleId = 0){
        if($moduleId == 0)
            return false;

        $q = "SELECt p.*, d.name FROM ".$this->tableFiles." p ";
        $q .= "LEFT JOIN ". $this->tableFilesDescription." d ON(p.id=d.parent_id) ";
        $q .= "WHERE p.parent_id=? AND p.parent_type=? AND d.language_id=? ";
        $q .= "ORDER BY p.`order` ASC";

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, $moduleId],
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function showOnMain($limit){
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE p.main=1 AND d.language_id=? ";
        $q .= "AND p.active=1 AND d.active = 1 ";
        $q .= "ORDER BY RAND() ";
        $q .= "LIMIT ? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $limit]
        ];

       return $this->query($q, $params)->get_all_rows();
    }

    public function getSitemapData(){

        $q = "SELECT d.title, d.title_url, p.id FROM ".$this->table." p ";
        $q .= "LEFT JOIN ". $this->tableDescription." d ON p.id = d.parent_id ";
        $q .= "WHERE p.active = 1 AND d.active=1 AND d.language_id=? ";
        $params = [
            [DbStatementController::INTEGER, _ID]
        ];
        $result = $this->query($q, $params)->get_all_rows();
        if($result)
            return $result;
        return [];
    }


    public function getTitleById($id){
        $q = "SELECT title FROM ".$this->tableDescription." ";
        $q .= "WHERE parent_id = ? AND language_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->get_result();
    }


}
