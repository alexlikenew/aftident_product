<?php
namespace Models;

use Controllers\DbStatementController;

class Client extends Model
{
    protected $table;
    protected $tableDescription;
    protected $tableClient2Offer;

    public function __construct($table='clients'){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';
        $this->tableClient2Offer = DB_PREFIX . 'client2offers';

        parent::__construct($table);
    }

    public function create(array $data):int{
        $id = parent::create($data);
        $this->updateClientOffers($id, $data['offer_id']);
        return $id;
    }

    public function update(array $data, int $id = null): bool{
        $result = parent::update($data, $id);
        $this->updateClientOffers($data['id'], $data['offer_id']);

        return $result;
    }

    public function getLogo($id){
        $q = "SELECT logo FROM " . $this->table . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_result();
    }

    public function deleteLogo($id){
        $q = "UPDATE " . $this->table . " SET logo='' WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateLogo($id, $filename){
        $q = "UPDATE ". $this->table . " SET logo=? WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $filename],
            [DbStatementController::INTEGER, $id]
        ];

        $this->saveEventInfo(json_encode(['id' => $id]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getById($id){
        $q = "SELECT p.*, d.title, d.title_url, d.content_short, d.content FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q.= "WHERE d.language_id=? AND p.id=? ";

        $params = [
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->fetch_assoc();
    }

    public function updateDescription($data, $langId){

        $q = "UPDATE " . $this->tableDescription . " SET ";
        $q .= "page_title=?, ";
        $q .= "page_keywords=?, ";
        $q .= "page_description=?, ";
        $q .= "title=?, ";
        $q .= "subtitle=?, ";
        $q .= "content_short=?, ";
        $q .= "content=?, ";
        $q .= "range_of_activities = ?, ";
        $q .= "main_content = ?, ";
        $q .= "list_content = ?, ";
        $q .= "offer_content = ? ";
        $q .= "WHERE parent_id=? ";
        $q .= "AND language_id=? ";
        $params = [
            [DbStatementController::STRING, $data['page_title'][$langId]],
            [DbStatementController::STRING, $data['page_keywords'][$langId]],
            [DbStatementController::STRING, $data['page_description'][$langId]],
            [DbStatementController::STRING, $data['title'][$langId]],
            [DbStatementController::STRING, $data['subtitle'][$langId]],
            [DbStatementController::STRING, $data['content_short'][$langId]],
            [DbStatementController::STRING, $data['content'][$langId]],
            [DbStatementController::STRING, $data['range_of_activities'][$langId]],
            [DbStatementController::STRING, $data['mian_content'][$langId]],
            [DbStatementController::STRING, $data['list_content'][$langId]],
            [DbStatementController::STRING, $data['offer_content'][$langId]],
            [DbStatementController::INTEGER, $data['id']],
            [DbStatementController::INTEGER, $langId]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getArticle($str, $isActive = true)
    {
        $q = "SELECT p.*, d.page_title, d.page_keywords, d.page_description, d.title, d.title_url, d.subtitle, d.content, d.range_of_activities, d.main_content, d.list_content, d.offer_content, d.content_short, d.tagi FROM " . $this->table . " p ";
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

    public function loadArticles( $start, $limit, $onlyActive = true, $category_id = false){

        $q = "SELECT p.*, d.title, d.title_url, d.content, d.range_of_activities, d.main_content, d.list_content, d.offer_content, d.content_short, d.active, d.tagi FROM " . $this->table . " p ";
        $q .= "LEFT JOIN " . $this->tableDescription . " d ON p.id=d.parent_id ";
        $q .= "WHERE d.language_id=? ";

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

    public function getClientOffers($id){
        $q = "SELECT offer_id FROM ". $this->tableClient2Offer." WHERE client_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function updateClientOffers($id, $offerIds){
        $this->deleteFromOffers($id);
        foreach($offerIds as $offerId)
            $this->addClient2Offer($id, $offerId);
        return true;
    }

    public function deleteFromOffers($id){
        $q = "DELETE FROM ".$this->tableClient2Offer." WHERE client_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function addClient2Offer($clientId, $offerId){
        $q = "INSERT INTO ".$this->tableClient2Offer." SET ";
        $q .= "client_id = ?, ";
        $q .= "offer_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $clientId],
            [DbStatementController::INTEGER, $offerId]
        ];

        return $this->query($q, $params)->insert_id();
    }
}
