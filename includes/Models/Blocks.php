<?php


namespace Models;


use Controllers\DbStatementController;

class Blocks extends Model
{
    const TYPE_URL = 1;
    const TYPE_PAGE = 2;
    const TYPE_MODULE = 3;
    const TYPE_OFFER = 4;
    protected $table;
    protected $tableDescription;

    public function __construct($table='blocks'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';


        parent::__construct($table);
    }


    public function create(array $data):int
    {
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "type_id=?, ";
        $q .= "active = ?, ";
        $q .= "name=? ";

        $params = [
            [DbStatementController::INTEGER, $data['type_id']],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active'])) ? 1 : 0],
            [DbStatementController::STRING, $data['name']]
        ];

        if(isset($data['is_child']) && $data['is_child']){
            $q .= ", parent_id=? ";
            $params[] = [DbStatementController::INTEGER, $data['parent_id']];
        }

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function createDescription($data, $langId){

        $q = "INSERT INTO " . $this->tableDescription . " SET ";
        $q .= "title=?, ";
        $q .= "title_url=?, ";
        $q .= "subtitle=?, ";
        $q .= "content=?, ";
        $q .= "url=?, ";
        $q .= "active=?, ";
        $q .= "parent_id=?, ";
        $q .= "language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['subtitle'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['url'][$langId]],
            [DbStatementController::INTEGER, $data['lang_active'][$langId] ? 1: 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "title=?, ";
        $q .= "h_type = ?,";
        $q .= "title_url=?, ";
        $q .= "subtitle=?, ";
        $q .= "content=?, ";
        $q .= "quote = ?, ";
        $q .= "url_target1_id = ? ,";
        $q .= "url=?, ";
        $q .= "url2=?, ";
        $q .= "url_title=?, ";
        $q .= "url_title2=?, ";
        $q .= "active=? ";
        $q .= " WHERE parent_id=? ";
        $q .= " AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['h_type']],
            [DbStatementController::STRING, $data['title_url'][$langId]],
            [DbStatementController::STRING, $data['subtitle'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['quote'][$langId]],
            [DbStatementController::INTEGER, $data['target_id'][$langId] ?? 0],
            [DbStatementController::STRING, $data['url'][$langId]],
            [DbStatementController::STRING, $data['url2'][$langId]],
            [DbStatementController::STRING, $data['url_title'][$langId]],
            [DbStatementController::STRING, $data['url_title2'][$langId]],
            [DbStatementController::INTEGER, $data['lang_active'][$langId] ? 1: 0],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);

        return $this->query($q, $params)->is_success();
    }

    public function getById($id){
        $q = "SELECT p.*, d.title, d.title_url, t.template_file, t.is_parent, t.has_text, t.has_photo, t.has_video, t.has_subtitle, t.has_url, t.has_quote, t.has_gallery, t.has_module FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "LEFT JOIN ".$this->tableTypes . " t ON(t.id=p.type_id)";
        $q.= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function getPages($onlyActive = false, $category_str = null, $category_id = null, $category_path = false){
        $q = "SELECT COUNT(p.id) FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";
        $q .= "AND p.id NOT IN (SELECT block_id FROM ".$this->tableBlock2Item." ) ";

        if(!$onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";

        $params = [
            [DbStatementController::INTEGER, _ID]
        ];

        $statement = $this->query($q, $params);

        return $statement->get_result();
    }

    public function loadArticles( $start, $limit, $onlyActive = true, $onlyUniversal = false, $typeId = 0, $onlyParentOrAlone = false){

        $q = "SELECT p.*, d.title, d.title_url, d.content, d.category, d.subtitle, d.url, d.url2, d.url_title, d.url_title2, bt.title_url, bt.is_parent FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON (p.id=d.parent_id) ";
        $q .= "LEFT JOIN " . $this->tableTypes . " bt ON (p.type_id = bt.id) " ;
        $q .= "WHERE d.language_id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
        ];

        if($typeId){
            $q .= "AND p.type_id=? ";
            $params[] = [DbStatementController::INTEGER, $typeId];
        }


        if($onlyActive)
            $q .= "AND p.active=1 ";
        if($onlyParentOrAlone)
            $q .= "AND p.parent_id IS NULL ";
        $q .= "ORDER BY ";
        $q .= "p.id DESC ";
        $q .= "LIMIT ?,? ";

        $params[] = [DbStatementController::INTEGER, $start];
        $params[] = [DbStatementController::INTEGER, $limit];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function update(array $data, int $id = null): bool
    {

        $q = "UPDATE " . $this->table . " SET ";
        $q .= "name=?, ";
        $q .= "gallery_id=?, ";
        $q .= "url_type = ?, ";
        $q .= "module_id = ?, ";
        $q .= "items_type = ?, ";
        $q .= "quantity_to_load = ? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::INTEGER, isset($data['gallery_id']) ? $data['gallery_id'] : 0],
            [DbStatementController::INTEGER, isset($data['type']) ? $data['type'] : null],
            [DbStatementController::INTEGER, isset($data['module']) ? $data['module'] : null],
            [DbStatementController::INTEGER, isset($data['items_type']) ? $data['items_type'] : null],
            [DbStatementController::INTEGER, isset($data['module_quantity']) ? $data['module_quantity'] : null],
            [DbStatementController::INTEGER, $data['id']]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();

    }


    public function updateItemsOrder($item, $order_new){

        if ($item['order'] < $order_new) {
            $q = "UPDATE " . $this->table . " SET `order`=`order`-1 ";
            $q .= "WHERE `order`>? ";
            $q .= "AND `order`<=? ";
            $q .= "AND id IN (SELECT parent_id FROM ".$this->tableDescription." WHERE page_id = ?)";
            $params = [
                [DbStatementController::INTEGER, $item['order']],
                [DbStatementController::INTEGER, $order_new],
                [DbStatementController::INTEGER, $item['page_id']],
            ];
            $this->query($q, $params);

        } else {
            $q = "UPDATE " . $this->table . " SET `order`=`order`+1 ";
            $q .= "WHERE `order`>=? ";
            $q .= "AND `order`<? ";
            $q .= "AND id IN (SELECT parent_id FROM ".$this->tableDescription." WHERE page_id = ?)";
            $params = [
                [DbStatementController::INTEGER, $order_new],
                [DbStatementController::INTEGER, $item['order']],
                [DbStatementController::INTEGER, $item['page_id']],
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

    public function getAllTypes($custom_type = null){
        $q = "SELECT * FROM ". $this->tableTypes. " ";
        if($custom_type){
            $q .= "WHERE module_id IN(0, ?)";
            $params = [
                [DbStatementController::INTEGER, $custom_type]
            ];

            $result = $this->query($q, $params)->get_all_rows();
        }
        else{
            $result = $this->query($q)->get_all_rows();
        }
        return $result;
    }

    public function getQuantityByType(){
        $q = "SELECT COUNT(id) AS quantity, type_id  FROM ".$this->table." ";
        $q .= "GROUP BY type_id";

        return $this->query($q)->get_all_rows();
    }

    public function createType(array $data){

        $q = "INSERT INTO ".$this->tableTypes." SET ";
        $q .= "title=?, ";
        $q .= "title_url=?, ";
        $q .= "template_file=?, ";
        $q .= "has_text = ?, ";
        $q .= "has_photo=?, ";
        $q .= "has_video=?, ";
        $q .= "has_subtitle=?, ";
        $q .= "has_url=?, ";
        $q .= "has_quote=?, ";
        $q .= "has_gallery=?, ";
        $q .= "module_id = ?, ";
        $q .= "is_parent = ?, ";
        $q .= "is_child = ?";

        $params = [
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::STRING, make_url($data['title'])],
            [DbStatementController::STRING, $data['template_name']],
            [DbStatementController::INTEGER, $data['text'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['photo'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['video'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['subtitle'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['url'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['quote'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['gallery'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['module'] ?? 0],
            [DbStatementController::INTEGER, $data['is_parent'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['is_child'] ? 1 : 0]

        ];

        return  $this->query($q, $params)->insert_id();

    }

    public function updateType(array $data){

        $q = "UPDATE  ".$this->tableTypes." SET ";
        $q .= "title=?, ";
        $q .= "title_url=?, ";
        $q .= "template_file=?, ";
        $q .= "has_text = ?, ";
        $q .= "has_photo=?, ";
        $q .= "has_video=?, ";
        $q .= "has_subtitle=?, ";
        $q .= "has_url=?, ";
        $q .= "has_quote=?, ";
        $q .= "has_gallery=?, ";
        $q .= "has_module = ?, ";
        $q .= "is_agregated = ? ";

        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::STRING, $data['title']],
            [DbStatementController::STRING, make_url($data['title'])],
            [DbStatementController::STRING, $data['template_name']],
            [DbStatementController::INTEGER, $data['text'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['photo'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['video'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['subtitle'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['url'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['quote'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['gallery'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['module'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['is_agregated'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['id']]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function deleteType(int $id){

        $q = "DELETE FROM ".$this->tableTypes." ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function typeHasItems($id){
        $q = "SELECT COUNT(id) FROM ".$this->tableBlocks." WHERE type_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getTypeById($id){
        $q = "SELECT * FROM ".$this->tableTypes." WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }

    public function changeType($id, $typeId){
        $q = "UPDATE ".$this->table." SET type_id=? WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $typeId],
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->is_success();
    }

    public function updateTypePhoto($id, $filename){
        $q = "UPDATE ". $this->tableTypes . " SET photo=? WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $filename],
            [DbStatementController::INTEGER, $id]
        ];

        $this->saveEventInfo(json_encode(['id' => $id]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getTypePhoto($id){
        $q = "SELECT photo FROM " . $this->tableTypes . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function getAll($onlyActive = true, $toSkip = false,  $onlyUniversal = false){

        $q = "SELECT p.id, p.name, d.title  FROM " . $this->table ." p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON(p.id=d.parent_id)  WHERE d.language_id = ?  " ;
        $params = [
            [DbStatementController::INTEGER, _ID]
        ];
        if($onlyActive){
            $q .= " AND p.active=1 AND d.active=1 ";
        }

        if(!empty($toSkip)){
            $idsString = implode(',', $toSkip);
            if($idsString)
                $q .= " AND p.id NOT IN ( ".implode(',', $toSkip)." ) ";

        }


        return $this->query($q, $params)->get_all_rows();
    }

    public function deleteBlockAssigns($id){
        $q = "DELETE FROM ". $this->tableBlock2Item . " WHERE block_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getUniversalBlocks($onlyActive, $toSkip = []){
        $q = "SELECT p.*, d.title, d.title_url, d.content, d.category, d.subtitle, d.url, d.url_target1_id, d.url2, d.url_title, d.url_title2, bt.title_url, bt.template_file, bt.is_parent, bt.title_url AS template_name FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON (p.id=d.parent_id) ";
        $q .= "LEFT JOIN " . $this->tableTypes . " bt ON (p.type_id = bt.id) " ;
        $q .= "WHERE d.language_id=? ";
        if($onlyActive)
            $q .= "AND p.active=1 AND d.active=1 ";

        if($toSkip && !empty($toSkip)){
            $q .= "AND p.id NOT IN (".implode(',', $toSkip).") ";
        }

        $params = [
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function getItemParentsBlocks($moduleId, $itemId = null){
        $q = "SELECT id FROM ".$this->table." ";
        $q .= "WHERE type_id IN (SELECT id FROM ".$this->tableTypes." WHERE is_parent=1 ";
        $q .= " ) AND id IN (SELECT block_id FROM ".$this->tableBlock2Item." WHERE module_id = ? AND item_id = ? )";
        $params = [
            [DbStatementController::INTEGER, $moduleId],
            [DbStatementController::INTEGER, $itemId]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function getChildrensById($id){
        $q = "SELECT id FROM ".$this->table." ";
        $q .= "WHERE parent_id = ? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_all_rows();
    }
    public function deleteTypePhoto($id){
        $q = "UPDATE " . $this->tableTypes . " SET photo='' WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

}
