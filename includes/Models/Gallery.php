<?php

namespace Models;

use Controllers\DbStatementController;
use Controllers\ConfigController;

class Gallery extends Model
{
   protected $table;
   protected $tableDescription;
   protected $tablePhotos;
   protected $tablePhotosDescription;

   public function __construct($table)
   {
       $this->table = DB_PREFIX . $table;
       $this->tableDescription = $this->table . '_description';
       $this->tablePhotos = $this->table . '_photo';
       $this->tablePhotosDescription = $this->tablePhotos . '_description';
       parent::__construct($table);
   }

   public function getTableDescription(){
       return $this->tableDescription;
   }

   public function loadGaleries($only_active = true){
       $q = "SELECT p.*, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " p ";
       $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";

       $q .= "WHERE d.language_id=?  ";

       if($only_active)
           $q .= " AND p.active=1 AND d.active=1 ";

       $q .= "ORDER BY p.`order` ";

       $params = [
           [DbStatementController::INTEGER, _ID]
       ];


       $statement = $this->query($q, $params);
       return $statement->get_all_rows();
   }

   public function getGalleryById($id){
       $q = "SELECT p.* FROM " . $this->table . " p ";
       $q.= "WHERE p.id=? ";

       $params = [
           [DbStatementController::INTEGER, $id]
       ];
       $statement = $this->query($q, $params);

       return $statement->fetch_assoc();

   }

   public function getRandomPhoto($id){
       $q = "SELECT p.*, d.title, d.content FROM " . $this->tablePhotos . " p ";
       $q .= "LEFT JOIN " . $this->tablePhotosDescription . " d ON p.id=d.parent_id ";
       $q.= "WHERE gallery_id=? AND d.language_id=? ";
       $q .= "ORDER BY p.`order` ASC ";
       $q .= "LIMIT 1 ";
       $params = [
           [DbStatementController::INTEGER, $id],
           [DbStatementController::INTEGER, _ID]
       ];
       $statement = $this->query($q, $params);

       return $statement->fetch_assoc();
   }

   public function loadGallery($name = null, $id = null){
       $q = "SELECT p.*, d.page_title, d.page_keywords, d.page_description, d.title, d.title_url, d.content, d.content_short, d.tagi FROM " . $this->table . " p ";
       $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
       $q .= "WHERE d.language_id=? ";
       $params[] = [DbStatementController::INTEGER, _ID];
       if($name){
           $q .= "AND d.title_url=? ";
           $params[] = [DbStatementController::STRING, $name];
       }
       elseif($id){
           $q .= "AND p.id=? ";
           $params[] = [DbStatementController::INTEGER, $id];
       }



       $statement = $this->query($q, $params);
       return $statement->fetch_assoc();
   }

   public function loadGalleriesName($langId = null): ?array{
       $q = "SELECT p.id, d.title FROM " . $this->table . " p ";
       $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
       $q .= "WHERE p.show_page=1 AND d.language_id=? ";
       $q .= "ORDER BY d.title";

       $params = [
           [DbStatementController::INTEGER, $langId ?? _ID]
       ];
        $result =  $this->query($q, $params)->get_all_rows();
        return is_array($result) ? $result : [];
   }

   public function getMaxOrder($id = null){
       if($id)
           $q = "SELECT MAX(`order`) FROM ".$this->tablePhotos." WHERE gallery_id = ".$id;
       else
           $q = "SELECT MAX(`order`) FROM ".$this->table;
       return $this->query($q)->get_result();
   }

   public function create(array $data) : int{
       $order = $this->getMaxOrder() + 1;
       $watermark = isset($data['watermark']) ?? 0;
       $auth = isset($data['auth']) ?? 0;
       $active = isset($data['active']) ?? 0;
       $show_list = isset($data['show_list']) ?? 0;
       $show_page = isset($data['show_page']) ?? 0;
       $show_title = isset($data['show_title']) ?? 0;
       $comments = isset($data['comments']) ?? 0;
       $q = "INSERT INTO " . $this->table . " SET ";
       $q .= "date_add=NOW(), ";
       $q .= "op_page_title=?, ";
       $q .= "op_page_keywords=?, ";
       $q .= "op_page_description=?, ";
       $q .= "width=?, ";
       $q .= "height=?, ";
       $q .= "watermark=?, ";
       $q .= "watermark_file=?, ";
       $q .= "watermark_x=?, ";
       $q .= "watermark_y=?, ";
       $q .= "watermark_position=?, ";
       $q .= "auth=?, ";
       $q .= "active=?, ";
       $q .= "show_list=?, ";
       $q .= "show_page=?, ";
       $q .= "show_title=?, ";
       $q .= "comments=?, ";
       $q .= "`order`=? ";

       $params = [
           [DbStatementController::INTEGER, $data['op_page_title']],
           [DbStatementController::INTEGER, $data['op_page_keywords']],
           [DbStatementController::INTEGER, $data['op_page_description']],
           [DbStatementController::INTEGER, $data['width']],
           [DbStatementController::INTEGER, $data['height']],
           [DbStatementController::INTEGER, $watermark],
           [DbStatementController::STRING, $data['watermark_file']],
           [DbStatementController::INTEGER, $data['watermark_x']],
           [DbStatementController::INTEGER, $data['watermark_y']],
           [DbStatementController::INTEGER, $data['watermark_position']],
           [DbStatementController::INTEGER, $auth],
           [DbStatementController::INTEGER, $active],
           [DbStatementController::INTEGER, $show_list],
           [DbStatementController::INTEGER, $show_page],
           [DbStatementController::INTEGER, $show_title],
           [DbStatementController::INTEGER, $comments],
           [DbStatementController::INTEGER, $order],
       ];

       $statement = $this->query($q, $params);
       $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
       return $statement->insert_id();
   }

   public function saveDescription($id, $data){

       foreach ($data['title'] as $i => $title) {

           $data['title'][$i] = prepareString($title, true);
           $data['page_title'][$i] = prepareString($data['page_title'][$i], true);
           $data['page_keywords'][$i] = prepareString($data['page_keywords'][$i], true);
           $data['page_description'][$i] = prepareString($data['page_description'][$i], true);

           if ($data['op_content_short'] == '1') {
               $data['content_short'][$i] = truncateWord(strip_tags($data['content'][$i]), 250, '');
           }
           $data['content_short'][$i] = prepareString($data['content_short'][$i]);

           $lang = ConfigController::loadLangById($i);
           if ($lang['gen_title']) {
               $data['title_url'][$i] = ConfigController::makeUniqueUrl(substr(make_url($data['title'][$i]), 0, 30), $this->tableDescription, "title_url", $i);
           } else {
               $data['title_url'][$i] = $data['title_url'][lang_main] . '-' . $lang['code'];
           }

           $q = "INSERT INTO " . $this->tableDescription . " SET ";
           $q .= "page_title=?, ";
           $q .= "page_keywords=?, ";
           $q .= "page_description=?, ";
           $q .= "title=?, ";
           $q .= "title_url=?, ";
           $q .= "content=?, ";
           $q .= "content_short=?, ";
           $q .= "tagi=?, ";
           $q .= "active=?, ";
           $q .= "parent_id=?, ";
           $q .= "language_id=? ";
           $params = [
               [DbStatementController::STRING, $data['page_title'][$i]],
               [DbStatementController::STRING, $data['page_keywords'][$i]],
               [DbStatementController::STRING, $data['page_description'][$i]],
               [DbStatementController::STRING, $data['title'][$i]],
               [DbStatementController::STRING, $data['title_url'][$i]],
               [DbStatementController::STRING, $data['content'][$i]],
               [DbStatementController::STRING, $data['content_short'][$i]],
               [DbStatementController::STRING, $data['tagi'][$i]],
               [DbStatementController::INTEGER, $data['lang_active'][$i] ? 1: 0],
               [DbStatementController::INTEGER, $id],
               [DbStatementController::INTEGER, $i],
           ];
           $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
           $this->query($q, $params);
       }
   }

   public function getAllPhotos($id){
       $q = "SELECT id, name FROM " . $this->tablePhotos . " WHERE gallery_id=? ";
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

   public function deleteAllGalleryPhotos($id){
       $q = "DELETE FROM " . $this->tablePhotos . " WHERE gallery_id=? ";

       $params = [
           [DbStatementController::INTEGER, $id]
       ];
       $this->saveEventInfo(json_encode(['id'   => $id]), '', 2, __METHOD__, $this->table);
       return $this->query($q, $params)->is_success();
   }

   public function getGalleryOrder($id){
       $q = "SELECT `order` FROM " . $this->table . " WHERE id=? ";
       $params = [
           [DbStatementController::INTEGER, $id]
       ];

       return  $this->query($q, $params)->get_result();

   }

   public function deleteGallery($id){
       $q = "DELETE FROM " . $this->table . " WHERE id=? ";
       $params = [
           [DbStatementController::INTEGER, $id]
       ];
       $this->saveEventInfo(json_encode(['id'=>$id]), '', 2, __METHOD__, $this->table);
       if($this->query($q, $params)->is_success()){
           return $this->deleteGalleryDescriptions($id);
       }
   }

   public function deleteGalleryDescriptions($id){
       $q = "DELETE FROM " . $this->tableDescription . " WHERE parent_id=? ";
       $params = [
           [DbStatementController::INTEGER, $id]
       ];
       $this->saveEventInfo(json_encode(['id'=>$id]), '', 2, __METHOD__, $this->table);
       return $this->query($q, $params)->is_success();

   }

   public function updateOrder($order){
       $q = "UPDATE " . $this->table . " SET `order`=`order`-1 WHERE `order`>? ";
       $params = [
           [DbStatementController::INTEGER, $order]
       ];
       $this->saveEventInfo('order', '', 1, __METHOD__, $this->table);
       return $this->query($q, $params)->is_success();
   }

   public function getGalleryPhotos($id){
       $q = "SELECT * FROM " . $this->tablePhotos . " ";
       $q .= "WHERE gallery_id=? ";
       $q .= "ORDER BY `order` ";
       $params = [
           [DbStatementController::INTEGER, $id]
       ];

       return $this->query($q, $params)->get_all_rows();
   }

   public function getGalleryDescription($id){
       $q = "SELECT * FROM " . $this->tableDescription . " ";
       $q.= "WHERE parent_id=? ";
       $params = [
           [DbStatementController::INTEGER, $id]
       ];
       return $this->query($q, $params)->get_all_rows();
   }

   public function updateGallery($post){
       $watermark = isset($post['watermark']) ? $post['watermark'] : 0;
       $auth = isset($post['auth']) ? $post['auth'] : 0;
       $active = isset($post['active']) ? $post['active'] : 0;
       $show_list = isset($post['show_list']) ? $post['show_list'] : 0;
       $show_page = isset($post['show_page']) ? $post['show_page'] : 0;
       $show_title = isset($post['show_title']) ? $post['show_title'] : 0;
       $comments = isset($post['comments']) ? $post['comments'] : 0;

       // aktualizujemy artykul
       $q = "UPDATE " . $this->table . " SET ";
       $q .= "date_add=?, ";
       $q .= "op_page_title=?, ";
       $q .= "op_page_keywords=?, ";
       $q .= "op_page_description=?, ";
       $q .= "width=?, ";
       $q .= "height=?, ";
       $q .= "watermark=?, ";
       $q .= "watermark_file=?, ";
       $q .= "watermark_x=?, ";
       $q .= "watermark_y=?, ";
       $q .= "watermark_position=?, ";
       $q .= "auth=?, ";
       $q .= "active=?, ";
       $q .= "show_list=?, ";
       $q .= "show_page=?, ";
       $q .= "show_title=?, ";
       $q .= "comments=? ";
       $q .= "WHERE id=? ";

       $params = [
           [DbStatementController::STRING, $post['date_add']],
           [DbStatementController::INTEGER, $post['op_page_title']],
           [DbStatementController::INTEGER, $post['op_page_keywords']],
           [DbStatementController::INTEGER, $post['op_page_description']],
           [DbStatementController::INTEGER, $post['width']],
           [DbStatementController::INTEGER, $post['height']],
           [DbStatementController::INTEGER, $watermark],
           [DbStatementController::STRING, $post['watermark_file']],
           [DbStatementController::INTEGER, $post['watermark_x']],
           [DbStatementController::INTEGER, $post['watermark_y']],
           [DbStatementController::INTEGER, $post['watermark_position']],
           [DbStatementController::INTEGER, $auth],
           [DbStatementController::INTEGER, $active],
           [DbStatementController::INTEGER, $show_list],
           [DbStatementController::INTEGER, $show_page],
           [DbStatementController::INTEGER, $show_title],
           [DbStatementController::INTEGER, $comments],
           [DbStatementController::INTEGER, $post['id']],
       ];
       $this->saveEventInfo(json_encode($post), '', 1, __METHOD__, $this->table);
       return $this->query($q, $params)->is_success();
   }

   public function updateGalleryDescription($data, $activeLang, $langId){
       $q = "UPDATE " . $this->tableDescription . " SET ";
       $q .= "page_title=?, ";
       $q .= "page_keywords=?, ";
       $q .= "page_description=?, ";
       $q .= "title=?, ";
       $q .= "title_url=?, ";
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
           [DbStatementController::STRING, $data['content'][$langId]],
           [DbStatementController::STRING, $data['content_short'][$langId]],
           [DbStatementController::STRING, $data['tagi'][$langId]],
           [DbStatementController::INTEGER, $activeLang],
           [DbStatementController::INTEGER, $data['id']],
           [DbStatementController::INTEGER, $langId]
       ];

       $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
       return $this->query($q, $params)->is_success();
   }

   public function createGalleryDescription($data, $activeLang, $langId){

       $q = "INSERT INTO " . $this->tableDescription . " SET ";
       $q .= "page_title=?, ";
       $q .= "page_keywords=?, ";
       $q .= "page_description=?, ";
       $q .= "title=?, ";
       $q .= "title_url=?, ";
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
           [DbStatementController::STRING, $data['content'][$langId]],
           [DbStatementController::STRING, $data['content_short'][$langId]],
           [DbStatementController::STRING, $data['tagi'][$langId]],
           [DbStatementController::INTEGER, $activeLang],
           [DbStatementController::INTEGER, $data['id']],
           [DbStatementController::INTEGER, $langId]
       ];
       $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
       return $this->query($q, $params)->is_success();

   }

   public function checkDescriptionExist($id, $langId){
       $q = "SELECT * FROM " . $this->tableDescription . " ";
       $q.= "WHERE parent_id=? AND language_id=? ";
       $params = [
           [DbStatementController::INTEGER, $id],
           [DbStatementController::INTEGER, $langId]
       ];
       return  $this->query($q, $params)->num_rows();
   }

   public function getGalleryConfig($id){
       $q = "SELECT ";
       $q .= "width, ";
       $q .= "height, ";
       $q .= "watermark, ";
       $q .= "watermark_file, ";
       $q .= "watermark_x, ";
       $q .= "watermark_y, ";
       $q .= "watermark_position ";
       $q .= "FROM " . $this->table . " ";
       $q .= "WHERE id=? ";
       $params = [
           [DbStatementController::INTEGER, $id]
       ];
       $statement = $this->query($q, $params);
       return $statement->fetch_assoc();
   }

   public function addPhotoToGallery($id, $files, $key ){
       $order = $this->getMaxOrder($id) + 1;
       $q = "INSERT INTO " . $this->tablePhotos . " SET ";
       $q .= "gallery_id=?, ";
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

   public function getPhotoData($id){
       $q = "SELECT * FROM " . $this->tablePhotos . " WHERE id=? ";
       $params = [
           [DbStatementController::INTEGER, $id]
       ];
       $statement = $this->query($q, $params);
       return $statement->fetch_assoc();
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

   public function updatePhotoDescription($data, $id){
       $q = "UPDATE " . $this->tablePhotosDescription . " SET ";
       $q .= "title=?, ";
       $q .= "content=?, ";
       $q .= "alt=?, ";
       $q .= "url = ? ";
       $q .= "WHERE parent_id=? ";
       $q .= "AND language_id=? ";
       $params = [
           [DbStatementController::STRING, $data['title'][$id]],
           [DbStatementController::STRING, $data['content'][$id]],
           [DbStatementController::STRING, $data['alt'][$id]],
           [DbStatementController::STRING, $data['url'][$id]],
           [DbStatementController::INTEGER, $data['foto_id']],
           [DbStatementController::INTEGER, $id]
       ];

       $this->saveEventInfo(json_encode(['id'=>$id]), '', 1, __METHOD__, $this->table);
       return $this->query($q, $params);
   }

   public function updateGalleryPhotosOrder($galleryId, $order){
       $q = "UPDATE " . $this->tablePhotos . " SET `order`=`order`-1 WHERE `order`>? AND gallery_id=? ";

       $params = [
           [DbStatementController::INTEGER, $order],
           [DbStatementController::INTEGER, $galleryId]
       ];

       return $this->query($q, $params);
   }

   public function createPhotoDescription($data, $id){
       $q = "INSERT INTO " . $this->tablePhotosDescription . " SET ";
       $q .= "title=?, ";
       $q .= "content=?, ";
       $q .= "parent_id=?, ";
       $q .= "language_id=? ";
       $params = [
           [DbStatementController::STRING, $data['title'][$id]],
           [DbStatementController::STRING, $data['content'][$id]],
           [DbStatementController::INTEGER, $data['foto_id']],
           [DbStatementController::INTEGER, $id]
       ];
       $this->saveEventInfo(json_encode(['id'=>$id]), '', 0, __METHOD__, $this->table);
       return $this->query($q, $params);
   }

   public function updatePhotoOrder($photo, $order_new){

       if ($photo['order'] < $order_new) {
           $q = "UPDATE " . $this->tablePhotos . " SET `order`=`order`-1 ";
           $q .= "WHERE gallery_id=? ";
           $q .= "AND `order`>? ";
           $q .= "AND `order`<=? ";
           $params = [
               [DbStatementController::INTEGER, $photo['gallery_id']],
               [DbStatementController::INTEGER, $photo['order']],
               [DbStatementController::INTEGER, $order_new]
           ];

           $this->query($q, $params);

       } else {
           $q = "UPDATE " . $this->tablePhotos . " SET `order`=`order`+1 ";
           $q .= "WHERE gallery_id=? ";
           $q .= "AND `order`>=? ";
           $q .= "AND `order`<?";
           $params = [
               [DbStatementController::INTEGER, $photo['gallery_id']],
               [DbStatementController::INTEGER, $order_new],
               [DbStatementController::INTEGER, $photo['order']]
           ];
           $this->query($q, $params);

       }
       $q = "UPDATE " . $this->tablePhotos . " SET `order`=? WHERE id=? ";
       $params = [
           [DbStatementController::INTEGER, $order_new],
           [DbStatementController::INTEGER, $photo['id']]
       ];

       $statement = $this->query($q, $params);

       if ($statement->is_success())
           return true;

       return false;
   }

   public function getPhotos($gallery_id){
       $q = "SELECT p.*, d.title, d.content, d.alt, d.url FROM " . $this->tablePhotos . " p ";
       $q .= "LEFT JOIN " . $this->tablePhotosDescription . " d ON p.id=d.parent_id ";
       $q .= "WHERE gallery_id=? AND d.language_id=? ";
       $q .= "ORDER BY p.`order` ";
       $params = array(
           array('i', $gallery_id),
           array('i', _ID)
       );
       return  $this->query($q, $params)->get_all_rows();
   }

    public function searchItems($words, $page = 1, $onlyActive = true){

        $q = "SELECT p.id, p.date_add, p.active, d.title, d.title_url, d.content_short FROM ". $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON (p.id=d.parent_id) ";
        $q .= "WHERE d.language_id=? AND (d.title LIKE ? OR d.subtitle LIKE ? OR d.content LIKE ? OR d.content_short LIKE ?)";
        if($onlyActive)
            $q .= "AND p.active = 1 AND d.active=1";
        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::STRING, '%'.$words.'%'],
            [DbStatementController::STRING, '%'.$words.'%'],
            [DbStatementController::STRING, '%'.$words.'%'],
            [DbStatementController::STRING, '%'.$words.'%'],
        ];

        return $this->query($q, $params)->get_all_rows();
    }


}
