<?php


namespace Controllers;

class ProductAdminController extends ProductController
{
    public function __construct($table = 'products')
    {
        parent::__construct($table);
    }

    public function loadArticlesAdmin($pages, $page, $categoryId = null){

        return $this->getItems($pages, $page, false, true, $categoryId, false);
    }

    public function update(array $post):bool{
        $result = parent::update($post);
        $productFeaturesIds = $this->getProductFeatureIds($post['id']);

        foreach($post['feature_values'] as $featureId=>$value){
            if(isset($productFeaturesIds[$featureId])){

                $this->model->updateProductFeature($productFeaturesIds[$featureId], $value ?? NULL);
            }
            else
                $this->model->createProductFeature($post['id'], $featureId, $value ?? NULL);

        }

        return $result;
    }

    public function getProductFeatures($id)
    {
        $data = $this->model->getProductFeatures($id);

        $result = [];
        foreach ($data as $item) {
            $result[$item['id_feature_value']] = [
                'value' => $item['value']
            ];
        }
        return $result;
    }

    public function getProductFeatureIds($id){
        $data = $this->model->getProductFeatures($id);
        $result = [];

        foreach($data as $feature){
            $result[$feature['id_feature_value']] = $feature['id'];
        }

        return $result;
    }

    public function updatePriceList($data){

        foreach($data['steps'] as $stepId=>$value){
            $result = $this->model->getPriceStep($data['parent_id'], $stepId);

            if(isset($result['id']) && $result['id'])
                $this->model->updatePriceStep($result['id'], $value, $data['extra']['yes'][$stepId], $data['extra']['no'][$stepId], $data['advanced']['yes'][$stepId], $data['advanced']['no'][$stepId], $data['min_time'][12][$stepId], $data['min_time'][18][$stepId], $data['min_time'][24][$stepId], $data['min_time'][30][$stepId]);
            else
                $this->model->addPriceStep($data['parent_id'], $stepId, $value);
        }
    }
}