<?php

namespace Models;
use Controllers\ConfigController;
use Controllers\DbStatementController;

class Slider extends Model{

    protected $table;
    protected $tableDescription;
    protected $tablePhotos;
    protected $tablePhotosDescription;
    protected $tableConfig;
    protected $dir;
    protected $url;
    protected $limit_home;
    protected $limit_page;
    protected $limit_admin;
    protected $limit_rss;
    protected $thumb_width;
    protected $thumb_height;
    protected $scale_width;
    protected $scale_height;
    protected $modul;

    public function __construct($table = 'slider'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . "_description";
        $this->tablePhotos = DB_PREFIX . $table . '_photo';
        $this->tablePhotosDescription = DB_PREFIX . $table . '_photo_description';
        $this->tableConfig = DB_PREFIX . 'config';
        $this->dir = ROOT_PATH . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $table;
        $this->url = BASE_URL . '/upload/' . $table;
        $this->limit_home = 10;
        $this->limit_page = 20;
        $this->limit_admin = 60;
        $this->limit_rss = 20;
        $this->scale_width = 2048;
        $this->scale_height = 1536;
        $this->modul = $table;
        parent::__construct($table);
        $this->getDefaultSliderConfig();
    }



    function getDefaultSliderConfig() {
        $this->thumb_width = $this->conf->vars['thumb_width_default'];
        $this->thumb_height = $this->conf->vars['thumb_height_default'];
        $this->watermark = 0;
    }

    /* funkcja wczytuje galerie zdjec */

    function getPhotos($slider_id = 0) {

        $q = "SELECT p.*, d.title, d.content, d.alt, d.url FROM " . $this->tablePhotos . " p ";
        $q .= "LEFT JOIN " . $this->tablePhotosDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE slider_id=? AND d.language_id=? ";
        $q .= "ORDER BY p.`order` ";
        $params = [
            [DbStatementController::STRING, $slider_id],
            [DbStatementController::INTEGER, _ID]
        ];

        return  $this->query($q, $params)->get_all_rows();

    }


    public function getPages($onlyActive = false, $category_str = null, $category_id = null, $category_path = false){
        $q = "SELECT COUNT(p.id) FROM " . $this->table . " p ";

        if(!$onlyActive)
            $q .= "WHERE p.active=1 ";

        $statement = $this->query($q);

        return $statement->get_result();
    }

    public function loadArticles( $start, $limit, $onlyActive = true, $category_id = false, $category_path = false){

        $q = "SELECT p.* FROM " . $this->table . " p ";

        if($onlyActive)
            $q .= "WHERE p.active=1 ";
        $q .= "ORDER BY p.id DESC ";
        $q .= "LIMIT ?,? ";

        $params = [
            [DbStatementController::INTEGER, $start],
            [DbStatementController::INTEGER, $limit]
        ];

        $statement = $this->query($q, $params);
        return $statement->get_all_rows();
    }

    public function getById($id){
        $q = "SELECT * FROM " . $this->table . " ";
        $q.= "WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function addPhotoToSlider($id, $files, $key ){
        $order = $this->getMaxOrder($id) + 1;
        $q = "INSERT INTO " . $this->tablePhotos . " SET ";
        $q .= "slider_id=?, ";
        $q .= "name=?, ";
        $q .= "`order`=? ";
        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::STRING, $files['photo']['name'][$key]],
            [DbStatementController::INTEGER, $order]
        ];

        $statement = $this->query($q, $params);
        if ($statement->is_success()) {
            $photoId = $statement->insert_id();
            $langs = ConfigController::getAllLangs();
            foreach ($langs as $lang) {
                $q = "INSERT INTO " . $this->tablePhotosDescription . " SET ";
                $q .= "parent_id=?, ";
                $q .= "language_id=? ";
                $params = [
                    [DbStatementController::INTEGER, $photoId],
                    [DbStatementController::INTEGER,$lang['id'] ]
                ];
                $this->query($q, $params);
            }
            $this->saveEventInfo(json_encode(['id'=>$id]), '', 0, __METHOD__, $this->table);
            return true;
        }
        return false;
    }

    public function getMaxOrder($id = null){
        if($id)
            $q = "SELECT MAX(`order`) FROM ".$this->tablePhotos." WHERE slider_id = ".$id;
        else
            $q = "SELECT MAX(`order`) FROM ".$this->table;
        return $this->query($q)->get_result();
    }

    function getAll($onlyActive = true, $toSkip = false):array {
        $q = "SELECT * FROM " . $this->table . " ";
        $q.= "WHERE active=1 ";
        return $this->query($q)->get_all_rows();

    }

    public function getAllPhotos($id){
        $q = "SELECT id, name FROM " . $this->tablePhotos . " WHERE slider_id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->get_all_rows();
    }

    public function deletePhotoDescription($id){
        $q = "DELETE FROM " . $this->tablePhotosDescription . " WHERE parent_id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        $this->query($q, $params);
    }

    public function deleteAllSliderPhotos($id){
        $q = "DELETE FROM " . $this->tablePhotos . " WHERE slider_id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id'   => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }


    public function deleteSlider($id){
        $q = "DELETE FROM " . $this->table . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        $this->saveEventInfo(json_encode(['id'=>$id]), '', 2, __METHOD__, $this->table);

        return $this->query($q, $params)->is_success();

    }

    public function create(array $data): int{
        $q = "INSERT INTO ". $this->table ." SET ";
        $q .= "label=?, ";
        $q .= "width=?, ";
        $q .= "height=?, ";
        $q .= "watermark=?, ";
        $q .= "watermark_file=?, ";
        $q .= "watermark_x=?, ";
        $q .= "watermark_y=?, ";
        $q .= "watermark_position=?, ";
        $q .= "active = ?";

        $params =[
            [DbStatementController::STRING, $data['title'][_ID]],
            [DbStatementController::INTEGER, $data['width']],
            [DbStatementController::INTEGER, $data['height']],
            [DbStatementController::INTEGER, $data['watermark'] ?? 0],
            [DbStatementController::STRING, $data['watermark_file']],
            [DbStatementController::INTEGER, $data['watermark_x'] ?? 0],
            [DbStatementController::INTEGER, $data['watermark_y'] ?? 0],
            [DbStatementController::INTEGER, $data['watermark_position'] ?? 1],
            [DbStatementController::INTEGER, $data['active'] ?? 0]
        ];

        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);

        return $this->query($q, $params)->insert_id();
    }

    public function update(array $data, int $id = null): bool
    {

        // aktualizujemy artykul
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "label=?, ";
        $q .= "width=?, ";
        $q .= "height=?, ";
        $q .= "watermark=?, ";
        $q .= "watermark_file=?, ";
        $q .= "watermark_x=?, ";
        $q .= "watermark_y=?, ";
        $q .= "watermark_position=? ";
        $q .= "WHERE id=?";


        $params =[
            [DbStatementController::STRING, $data['label']],
            [DbStatementController::INTEGER, $data['width']],
            [DbStatementController::INTEGER, $data['height']],
            [DbStatementController::INTEGER, $data['watermark'] ?? 0],
            [DbStatementController::STRING, $data['watermark_file']],
            [DbStatementController::INTEGER, $data['watermark_x'] ?? 0],
            [DbStatementController::INTEGER, $data['watermark_y'] ?? 0],
            [DbStatementController::INTEGER, $data['watermark_position'] ?? 1],
            [DbStatementController::INTEGER, $data['id']]
        ];
        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getPhotoData($id){
        $q = "SELECT * FROM " . $this->tablePhotos . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $statement = $this->query($q, $params);
        return $statement->fetch_assoc();
    }

    public function getPhotoDescription($id, $langId = null){
        $q = "SELECT * FROM " . $this->tablePhotosDescription . " ";
        $q.= "WHERE parent_id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        if($langId){
            $q .= "AND language_id = ? ";
            $params[] = [DbStatementController::INTEGER, $langId];
        }

        $statement = $this->query($q, $params);
        if($langId)
            return $statement->fetch_assoc();
        return $statement->get_all_rows();

    }

    public function deletePhoto($id, $background = false){
        $q = "DELETE FROM " . $this->tablePhotos . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        $statement = $this->query($q, $params);
        $this->saveEventInfo(json_encode(['id'=>$id]), '', 2, __METHOD__, $this->table);
        return $statement->is_success();
    }

    public function updatePhotoDescription($data, $langId){

        $q = "UPDATE ".$this->tablePhotosDescription." SET ";
        $q .= "active=?, ";
        $q .= "title=?, ";
        $q .= "url=?, ";
        $q .= "content=?, ";
        $q .= "alt=? ";
        $q .= "WHERE parent_id=? AND language_id=?";

        $params = [
            [DbStatementController::INTEGER, (isset($data['lang_active'][$langId]) && $data['lang_active'][$langId]) ? 1 : 0],
            [DbStatementController::STRING, prepareString($data['title'][$langId], true)],
            [DbStatementController::STRING, prepareString($data['url'][$langId], true, true)],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['alt'][$langId]],
            [DbStatementController::INTEGER, $data['foto_id']],
            [DbStatementController::INTEGER, $langId]
        ];

        return $this->query($q, $params)->is_success();
    }


    public function createPhotoDescription($data, $langId){

        $q = "INSERT INTO  ".$this->tablePhotosDescription." SET ";
        $q .= "language_id=?, ";
        $q .= "parent_id=?, ";
        $q .= "active=?, ";
        $q .= "title=?, ";
        $q .= "url=?, ";
        $q .= "content=? ";


        $params = [
            [DbStatementController::INTEGER, $langId],
            [DbStatementController::INTEGER, $data['foto_id']],
            [DbStatementController::INTEGER, (isset($data['lang_active'][$langId]) && $data['lang-active'][$langId]) ? 1 : 0],
            [DbStatementController::STRING, prepareString($data['title'][$langId], true)],
            [DbStatementController::STRING, prepareString($data['url'][$langId], true, true)],
            [DbStatementController::STRING, $data['content'][$langId]],
        ];

        return $this->query($q, $params)->insert_id();
    }

    public function getArticle($str, $onlyActive = true)
    {
        $q = "SELECT p.* FROM " . $this->table . " p ";
        $q .= "WHERE p.label=?";

        $params = [
            [DbStatementController::STRING, $str],
        ];

        $statement = $this->query($q, $params);

        return  $statement->fetch_assoc();
    }
}
