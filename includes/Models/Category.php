<?php


namespace Models;


use Controllers\DbStatementController;

class Category extends Model
{
    protected $table;
    protected $tableDescription;

    public function __construct($table = 'categories'){

        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';
        parent::__construct($table);
     }

     public function getByPid($pid = 0){
         $q = "SELECT a.*, d.title, d.title_url, d.content, d.content2, d.content_short, d.tagi FROM " . $this->table . " a ";
         $q .= "LEFT JOIN " . $this->tableDescription . " d ON a.id=d.parent_id ";
         $q .= "WHERE a.parent_id=? ";
         $q .= "AND d.language_id=? ";
         $q .= "ORDER BY a.order_number ASC";

         $params = [
             [DbStatementController::INTEGER, !$pid ? false : $pid],
             [DbStatementController::INTEGER, _ID]
         ];

         return  $this->query($q, $params)->get_all_rows();
     }

     public function getSubcategoryByUrl($url){
         $q = "SELECT B.* FROM ";
         $q .= "(SELECT parent_id AS id FROM " . $this->tableDescription . " WHERE title_url=?) AS A LEFT JOIN ";
         $q .= "(SELECT a.*, d.* FROM " . $this->table . " a LEFT JOIN $this->tableDescription d ON a.id=d.parent_id WHERE d.language_id=? ORDER BY d.title ASC) AS B ON A.id=B.parent_id";

         $params = [
             [DbStatementController::STRING, addslashes($url)],
             [DbStatementController::INTEGER, _ID]
         ];

         return $this->query($q, $params)->get_all_rows();
     }

     public function getSameParent($parent){
         $q = "SELECT d.*, a.* FROM " . $this->table . " a ";
         $q .= "LEFT JOIN $this->tableDescription d ON a.id=d.parent_id ";
         $q .= "WHERE a.parent_id=? ";
         $q .= "AND d.language_id=? ";
         $q .= "and a.active='1' ";
         $q .= "ORDER BY a.order_number ASC ";

         $params = [
             [DbStatementController::INTEGER, $parent],
             [DbStatementController::INTEGER, _ID]
         ];

         return $this->query($q, $params)->get_all_rows();
     }

     public function getFirstSubcategory($pid = 0){
         $q = "SELECT a.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " a ";
         $q .= "LEFT JOIN " . $this->tableDescription . " d ON a.id=d.parent_id ";
         $q .= "WHERE a.parent_id=? ";
         $q .= "AND d.language_id=? ";
         $q .= "ORDER BY a.order_number ASC limit 1";

         $params = [
             [DbStatementController::INTEGER, $pid],
             [DbStatementController::INTEGER, _ID]
         ];

         return $this->query($q, $params)->fetch_assoc();
     }

     public function getPathById($id){
         $q = "SELECT path_id FROM " . $this->table . " WHERE id=? ";

         $params = [
             [DbStatementController::INTEGER, $id]
         ];

         return $this->query($q, $params)->get_result();

     }

     public function getPath($pathArray){
         $q = "SELECT a.id, a.parent_id, d.title_url, d.title FROM " . $this->table . " a ";
         $q .= "LEFT JOIN " . $this->tableDescription . " d ON a.id=d.parent_id ";
         $q .= "WHERE d.language_id=? AND ";

         $params = [
             [DbStatementController::INTEGER, _ID]
         ];

         foreach($pathArray as $i=>$row) {
             if ($i == 0) {
                 $q .= "(";
             }
             if ($i > 0) {
                 $q .= "OR ";
             }
             $q .= "a.id=? ";

             $params[] = [DbStatementController::STRING, $row];

             if ($i == count($pathArray) - 1) {
                 $q .= ") ";
             }
         }

         $q .= "ORDER BY path_id";

         return $this->query($q, $params)->get_all_rows();

     }

     public function getCategoryByTitleUrl($title_url = '', $parent_id = '')
     {

         $q = "SELECT d.*, a.* FROM " . $this->table . " a ";
         $q .= "LEFT JOIN $this->tableDescription d ON a.id=d.parent_id ";
         if (empty($title_url)) {
             // pobieramy pierwsza kategorie
             $q .= "WHERE a.depth=1 AND d.language_id='" . _ID . "' ORDER BY order_number ASC ";
         } else {
             $q .= "WHERE d.title_url='" . addslashes($title_url) . "' ";
             if ($parent_id != '') {
                 $q .= "AND a.parent_id='" . (int)$parent_id . "' ";
             }
             $q .= "AND d.language_id='" . _ID . "' ";
         }

         return $this->query($q)->fetch_assoc();

     }

     public function getArticleById($id){
         $q = "SELECT d.*, a.* FROM " . $this->table . " a ";
         $q .= "LEFT JOIN $this->tableDescription d ON a.id=d.parent_id ";
         $q .= "WHERE a.id=? AND d.language_id=?";

         $params = [
             [DbStatementController::INTEGER, $id],
             [DbStatementController::INTEGER, _ID]
         ];

         return  $this->query($q, $params)->fetch_assoc();
     }

     public function search($keyword){

         $q = "select c.id from " . $this->table . " c left join " . $this->tableDescription . " d on c.id=d.parent_id ";
         $q .= "where d.language_id=? and c.active=1 and d.active=1  ";
         $q .= "and (d.title LIKE CONCAT('%', ?, '%') )  ";

         $params = [
             [DbStatementController::INTEGER, _ID],
             [DbStatementController::STRING, $keyword]
         ];
         return  $this->query($q, $params)->get_all_rows();
     }

     public function getSubtree($path_id = null){
         $q = "SELECT a.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " a ";
         $q .= "LEFT JOIN " . $this->tableDescription . " d ON a.id=d.parent_id ";
         $q .= "WHERE ";
         if($path_id){
             $q .= "path_id LIKE '".$path_id."%' ";
             $q .= "AND ";
         }
         $q .= "d.language_id=? ";
         $q .= "ORDER BY depth ASC, order_number ASC";

         $params = [
             [DbStatementController::INTEGER, _ID]
         ];

         return $this->query($q, $params)->get_all_rows();

     }

     public function getCategoryByTargetId($id){
         $q = "SELECT d.*, a.* FROM " . $this->table . " a ";
         $q .= "LEFT JOIN $this->tableDescription d ON a.id=d.parent_id ";
         $q .= "WHERE a.path_id=?";

         $params = [
             [DbStatementController::STRING, $id]
         ];

         return $this->query($q, $params)->fetch_assoc();
     }

     public function loadByIds($ids){
         $q = "SELECT p.*, d.page_title, d.page_keywords, d.page_description, d.title, d.subtitle, d.title_url, d.content, d.content2, d.content_short, d.tagi FROM " . $this->table . " p ";
         $q .= "LEFT JOIN $this->tableDescription d ON p.id=d.parent_id ";
         $q .= "WHERE p.id IN (".implode(',', $ids).") ";
         $q .= "AND d.language_id=? AND p.active=1 AND d.active=1 ";
         $q .= "ORDER BY p.order_number ASC ";

         $params = [
             [DbStatementController::INTEGER, _ID]
         ];

         return  $this->query($q, $params)->get_all_rows();
     }

     public function getMaxOrder($id = null){

        $q = "SELECT MAX(order_number) FROM ". $this->table ." WHERE parent_id = ? ";
        $params = [
            [DbStatementController::INTEGER, $id ?? 0]
        ];

        return $this->query($q, $params)->get_result();
     }

     public function create(array $data): int{

        $q = "INSERT INTO " . $this->table . " SET ";
        $q.= "op_page_title=?, ";
        $q.= "op_page_keywords=?, ";
        $q.= "op_page_description=?, ";
        $q.= "gallery_id=?, ";
        $q.= "active=?, ";
        $q.= "order_number=?, ";
        $q.= "path_id=?, ";
        $q.= "parent_id=?, ";
        $q.= "depth=? ";

        $params = [
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, (isset($data['gallery_id'])?$data['gallery_id']:"") ],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active'])) ? 1 : 0],
            [DbStatementController::INTEGER, $data['order']],
            [DbStatementController::STRING, $data['path_id']],
            [DbStatementController::INTEGER, $data['category_id'] ?? 0],
            [DbStatementController::INTEGER, $data['depth']]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
     }

     public function createDescription($data, $langId)
     {
         $q = "INSERT INTO " . $this->tableDescription . " SET ";
         $q .= "page_title=?, ";
         $q .= "page_keywords=?, ";
         $q .= "page_description=?, ";
         $q .= "title=?, ";
         $q .= "subtitle=?, ";
         $q .= "title_url=?, ";
         $q .= "content=?, ";
         $q .= "content2=?, ";
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
             [DbStatementController::STRING, $data['subtitle'][$langId]],
             [DbStatementController::STRING, $data['title_url'][$langId]],
             [DbStatementController::STRING, $data['content'][$langId]],
             [DbStatementController::STRING, $data['content2'][$langId]],
             [DbStatementController::STRING, $data['content_short'][$langId]],
             [DbStatementController::STRING, $data['tagi'][$langId]],
             [DbStatementController::INTEGER, isset($data['lang_active'][$langId]) && $data['lang_active'][$langId] == 'on' ? 1 : 0],
             [DbStatementController::INTEGER, $data['id']],
             [DbStatementController::INTEGER, $langId]
         ];
         $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table);
         return $this->query($q, $params)->is_success();
     }

     public function updatePath($id){
         $q = "UPDATE " . $this->table . " SET path_id=CONCAT(path_id,?) WHERE id=? ";
            $params = [
                [DbStatementController::STRING, $id.'.'],
                [DbStatementController::INTEGER, $id]
            ];
         return $this->query($q, $params)->is_success();
     }

     public function update(array $data, int $id = null):bool{
        $q = "UPDATE " . $this->table . " SET ";
        $q.= "op_page_title=?, ";
        $q.= "op_page_keywords=?, ";
        $q.= "op_page_description=?, ";
        $q.= "gallery_id=?, ";
        $q.= "active=?, ";
        $q.= "show_subcategories=? ";
        $q.= "WHERE id=?";

        $params = [
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, (isset($data['gallery_id'])?$data['gallery_id']:"") ],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active'])) ? 1 : 0],
            [DbStatementController::INTEGER, (isset($data['show_subcategories']) && !empty($data['show_subcategories'])) ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']]
        ];
         $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
     }

    public function updateDescription($data, $langId)
    {

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "title=?, ";
        $q .= "subtitle=?, ";
        $q .= "title_url=?, ";
        $q .= "content=?, ";
        $q .= "content2=?, ";
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
            [DbStatementController::STRING, $data['subtitle'][$langId]],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['content2'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['tagi'][$langId]],
            [DbStatementController::INTEGER, isset($data['lang_active'][$langId]) && $data['lang_active'][$langId] ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];
        $this->saveEventInfo(json_encode($data), '', __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getParentById($id){
        $q = "SELECT parent_id FROM " . $this->table . " WHERE id=? LIMIT 1";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->get_result();

    }

    public function getMainCategories($limit = null){
        $q = "SELECT id FROM " . $this->table ." WHERE parent_id = 0 AND active=1 ORDER BY RAND()";

        if($limit){
            $q .= " LIMIT  ?";
            $params = [
                [DbStatementController::INTEGER, $limit]
            ];
            return $this->query($q, $params)->get_all_rows();
        }
        return $this->query($q)->get_all_rows();
    }

    public function getSubcategories($id, $depth, $limit = null){
            $q = "SELECT id FROM " . $this->table ." WHERE ";
            $q .= "path_id LIKE CONCAT( ?, '%') ";
            $q .= "AND depth = ? ORDER BY RAND()";
            $params = [
                [DbStatementController::STRING, $id.'.'],
                [DbStatementController::INTEGER, $depth + 1],
            ];

            if($limit){
                $q .= " LIMIT ?";
                $params[] = [DbStatementController::INTEGER, $limit];
            }
        return $this->query($q, $params)->get_all_rows();

        }

        public function getSubcategoriesIds($id){
            $q = "SELECT id FROM " . $this->table ." WHERE ";
            $q .= "path_id LIKE CONCAT('%', ?, '%') ";
            $q .= "ORDER BY RAND()";
            $params = [
                [DbStatementController::STRING, $id.'.'],
            ];

            return $this->query($q, $params)->get_all_rows();
        }

        public function updateContent($id, $langId, $content, $active = false){
            $q = "UPDATE ".$this->tableDescription." SET ";
            $q.= "content = ? ,";
            $q .= "active = ? ";
            $q .= "WHERE parent_id = ? ";
            $q .= "AND language_id = ? ";

            $params = [
                [DbStatementController::STRING, $content],
                [DbStatementController::INTEGER, $active],
                [DbStatementController::INTEGER, $id],
                [DbStatementController::INTEGER, $langId]
            ];

            return $this->query($q, $params)->is_success();
        }

    public function searchItems($words, $page = 1, $onlyActive = true){

        $q = "SELECT p.id, p.path_id, p.date_add, p.photo, p.active, d.title, d.title_url, d.subtitle, d.content_short FROM ". $this->table . " p ";
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

    public function getOtherUrls($path, $langIds){

        if(substr($path, -1) == '.')
            $path = substr($path, 0, strlen($path) - 1);

        $path = explode('.', $path);

        $q = "SELECT language_id, title_url FROM ".$this->tableDescription." ";
        $q .= "WHERE parent_id IN (SELECT id FROM ".$this->table." WHERE active = 1 AND id IN(".implode(',', $path).")) ";
        $q .= "AND language_id IN(".implode(',', $langIds).") AND language_id != "._ID." ";
        $q .= "AND active=1 ";
        $q .= "AND language_id IN (SELECT id FROM languages WHERE gen_title = 1)";


        return $this->query($q)->get_all_rows();
    }
}
