<?php


namespace Models;

use Controllers\DbStatementController;

class Product extends ModelCategorized
{
    protected $table;
    protected $tableDescription;
    protected $tableProductFeatures;
    protected $tableFeatures;
    protected $tableFeatureDescription;
    protected $tableFeatureCategoryDescription;

    protected $tablePriceStep;

    public function __construct($table='product', $isCategorized = false){
        $this->table = DB_PREFIX . $table;
        $this->tableDescription = $this->table . '_description';
        $this->tableProductFeatures = $this->table . '_features';
        $this->tableFeatures = DB_PREFIX . 'features_values';
        $this->tableFeatureDescription = DB_PREFIX . 'features_values_description';
        $this->tableFeatureCategoryDescription = DB_PREFIX . 'features_description';
        $this->tablePriceStep = DB_PREFIX . 'product_price_step';

        parent::__construct($table, $isCategorized);
    }


    public function create(array $data):int{
        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "category_id = ?, ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "auth=?, ";
        $q .= "gallery_id=?, ";
        $q .= "show_title=?, ";
        $q .= "active=?, ";
        $q .= "main=?, ";
        $q .= "code = ?, ";
        $q .= "comments=? ";

        $params = [
            [DbStatementController::INTEGER, $data['category_id']],
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::INTEGER, $data['show_title'] ?? 0],
            [DbStatementController::INTEGER, (isset($data['lang_active']) && !empty($data['lang_active']))? 1 : 0],
            [DbStatementController::INTEGER, $data['main'] ? 1 : 0],
            [DbStatementController::STRING, $data['code']],
            [DbStatementController::INTEGER, $data['comments'] ?? 0],
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        $id = $this->query($q, $params)->insert_id();
        return $id;
    }

    public function update(array $data, int $id = null): bool{

        // aktualizujemy artykul
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "category_id = ?, ";
        $q .= "op_page_title=?, ";
        $q .= "op_page_keywords=?, ";
        $q .= "op_page_description=?, ";
        $q .= "auth=?, ";
        $q .= "gallery_id=?, ";
        $q .= "show_title=?, ";
        //$q .= "active=?, ";
        $q .= "main=?, ";
        $q .= "code = ?, ";
        $q .= "on_sale = ?, ";
        $q .= "comments=?, ";
        $q .= "date_add=? ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $data['category_id']],
            [DbStatementController::INTEGER, $data['op_page_title']],
            [DbStatementController::INTEGER, $data['op_page_keywords']],
            [DbStatementController::INTEGER, $data['op_page_description']],
            [DbStatementController::INTEGER, $data['auth']],
            [DbStatementController::INTEGER, $data['gallery_id']],
            [DbStatementController::INTEGER, $data['show_title']],
            //[DbStatementController::INTEGER, $data['active']],
            [DbStatementController::INTEGER, $data['main'] ? 1 : 0],
            [DbStatementController::STRING, $data['code']],
            [DbStatementController::INTEGER, $data['on_sale'] ? 1 : 0],
            [DbStatementController::INTEGER, $data['comments']],
            [DbStatementController::STRING, $data['date_add']],
            [DbStatementController::INTEGER, $data['id']]
        ];

        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function getProductFeatures($id){
        $q = "SELECT p.id, p.id_feature_value, p.`value` FROM ".$this->tableProductFeatures . " p  ";

        $q .= "WHERE id_product = ? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function createProductFeature($productId, $featureId, $value = null){
        $q = "INSERT INTO ". $this->tableProductFeatures." SET ";
        $q .= "id_product = ?, ";
        $q .= "id_feature_value = ?, ";
        $q .= "value = ? ";

        $params = [
            [DbStatementController::INTEGER, $productId],
            [DbStatementController::INTEGER, $featureId],
            [DbStatementController::STRING, $value]
        ];

        return $this->query($q, $params)->insert_id();
    }

    public function updateProductFeature($id, $value = null){
        $q = "UPDATE ". $this->tableProductFeatures." SET ";
        $q .= "value = ? ";
        $q .= "WHERE id = ? ";

        $params = [
            [DbStatementController::STRING, $value],
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function deleteProductFeature($id){
        $q = "DELETE FROM ". $this->tableProductFeatures." ";
        $q .= "WHERE id = ? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getItemFeatuturesInfo($id){
        $q = "SELECT p.id, p.value, f.id as feature_id, f.type, fd.title, fd.content, fcd.parent_id AS feature_category_id, fcd.title AS category_title, fcd.content AS category_content FROM ". $this->tableProductFeatures. " p ";
        $q .= "LEFT JOIN ".$this->tableFeatures." f ON (f.id = p.id_feature_value)";
        $q .= "LEFT JOIN ".$this->tableFeatureDescription . " fd ON (f.id = fd.parent_id) ";
        $q .= "LEFT JOIN ". $this->tableFeatureCategoryDescription. " fcd ON(fcd.parent_id = f.parent_id) ";
        $q .= "WHERE p.id_product = ? ";
        $q .= "AND fd.language_id = ? ";
        $q .= "AND fcd.language_id = ? ";
        $q .= "AND fd.active = 1 AND f.active = 1 AND fcd.active = 1 ";

        $params = [
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, _ID],
            [DbStatementController::INTEGER, _ID]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function getOnSaleItems($limit){
        $q = "SELECT id FROM ".$this->table." WHERE active = 1 AND on_sale = 1 ";
        if($limit)
            $q .= "LIMIT ?";
        $params = [
            [DbStatementController::INTEGER, $limit]
        ];

        return $this->query($q, $params)->get_all_rows();
    }

    public function getPriceStep($parentId, $stepId){
        $q = "SELECT * FROM ".$this->tablePriceStep." ";
        $q .= "WHERE parent_id = ? AND step_id = ? ";

        $params = [
            [DbStatementController::INTEGER, $parentId],
            [DbStatementController::INTEGER, $stepId]
        ];

        return $this->query($q, $params)->fetch_assoc();

    }

    public function updatePriceStep($id, $value, $extra_yes, $extra_no, $advanced_yes, $advanced_no, $min_time_12, $min_time_18, $min_time_24, $min_time_30){
        $q = "UPDATE ". $this->tablePriceStep. " SET ";
        $q .= "value = ?, ";
        $q .= "extra_yes = ?, ";
        $q .= "extra_no = ?, ";
        $q .= "advanced_yes = ?, ";
        $q .= "advanced_no  = ?, ";
        $q .= "min_time_12 = ?, ";
        $q .= "min_time_18 = ?, ";
        $q .= "min_time_24 = ?, ";
        $q .= "min_time_30 = ? ";
        $q .= "WHERE id = ? ";

        $params = [
            [DbStatementController::DOUBLE, $value],
            [DbStatementController::DOUBLE, $extra_yes],
            [DbStatementController::DOUBLE, $extra_no],
            [DbStatementController::DOUBLE, $advanced_yes],
            [DbStatementController::DOUBLE, $advanced_no],
            [DbStatementController::DOUBLE, $min_time_12],
            [DbStatementController::DOUBLE, $min_time_18],
            [DbStatementController::DOUBLE, $min_time_24],
            [DbStatementController::DOUBLE, $min_time_30],
            [DbStatementController::INTEGER, $id]
        ];

        $this->saveEventInfo(json_encode(['id' => $id, 'value'=>$value ]), '', 1, __METHOD__, $this->table);

        return $this->query($q, $params)->is_success();
    }

    public function addPriceStep($parentId, $stepId, $value){
        $q = "INSERT INTO ". $this->tablePriceStep. " SET ";
        $q .= "parent_id = ?, ";
        $q .= "step_id = ?, ";
        $q .= "value = ? ";

        $params = [
            [DbStatementController::INTEGER, $parentId],
            [DbStatementController::INTEGER, $stepId],
            [DbStatementController::DOUBLE, $value]
        ];

        $this->saveEventInfo(json_encode(['id' => $parentId, 'step_id'  => $stepId, 'value'=>$value ]), '', 1, __METHOD__, $this->table);

        return $this->query($q, $params)->insert_id();
    }

    public function getItemPriceSteps($id){
        $q = "SELECT p.*, s.from_price, s.to_price FROM ".$this->tablePriceStep."  p ";
        $q .= "LEFT JOIN price_steps s ON(p.step_id = s.id) ";
        $q .= "WHERE p.parent_id = ?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->get_all_rows();
    }
}
