<?php


namespace Controllers;

use Models\Product;
use Traits\Categorized;

class ProductController extends Controller
{
    use Categorized;
    protected $model;
    protected $module;
    protected $scale_width;
    protected $scale_height;
    protected $list_height;
    protected $list_width;
    protected $detail_width;
    protected $detail_height;
    protected $main_width;
    protected $main_height;


    public function __construct($table = 'products'){

        $this->model = new Product($table, $this->isCategorized);
        $this->module = 'products';
        $this->setCategoryController($table);
        parent::__construct($this->model, $table);
    }

    public function getPages($onlyActive = false, $forAdmin = false, $category_str = null, $category_id = null, $category_path = false){

        $count = $this->model->getPages($onlyActive, $category_str, $category_id, $category_path);

        if ($count < 1) {
            $count = 1;
        }

        if($forAdmin)
            return ceil($count / $this->limit_admin);
        else
            return ceil($count / $this->limit_page);
    }

    public function getItem($slug, $isActive = true){
        $data = parent::getItem($slug, $isActive);
        if($data)
            $data['features'] = $this->getProductFeatures($data['id']);

        return $data;
    }

    public function getItemUrl($item, $languageId = _ID){

        return '/' . $item['title_url'];
    }

    public function getArticleById($id){
        $data = parent::getArticleById($id);
        $data['url'] = '/'.$data['title_url'];
        $data['features'] = $this->getProductFeatures($id);
        $data['price_step'] = $this->getPriceSteps($id);
        return $data;
    }

    public function getPriceSteps($id){
        $data = $this->model->getItemPriceSteps($id);

        $result = [];

        foreach($data as $item){
            $result[$item['step_id']] = $item;
        }

        return $result;
    }

    public function getProductFeatures($id){
        $data = $this->model->getItemFeatuturesInfo($id);
        $result = [];

        foreach($data as $item){
            if(!isset($result[$item['feature_category_id']])){
                $result[$item['feature_category_id']]['title'] = $item['category_title'];
                $result[$item['feature_category_id']]['content'] = $item['category_content'];
                $result[$item['feature_category_id']]['with_values'] = false;

            }

            $result[$item['feature_category_id']]['items'][$item['feature_id']] = [
                'title'     => $item['title'],
                'content'   => $item['content'],
                'value'     => $item['value'],
                'type'      => $item['type'],
            ];
            if($item['value'])
                $result[$item['feature_category_id']]['with_values'] = true;
        }
        return $result;
    }

    public function getOnSaleProducts($limit = null){
        $data = $this->model->getOnSaleItems($limit);
        $result = [];

        foreach($data as $id){

            $result[] = $this->getarticleById($id['id']);
        }
        return $result;
    }

    public function getRandomItems($limit, $toSkip = []){
        $data = parent::getRandomItems($limit, $toSkip);

        foreach($data as $key=>$item)
            $data[$key]['url'] = $this->getItemUrl($item);

        return $data;
    }

    public function getAll($onlyActive = true, $toSkip = false){
        $data = parent::getAll();
        $items = [];
        foreach($data as $item){
            $item['features'] = $this->getProductFeatures($item['id']);
            $items[] = $item;
        }
        return $items;
    }

}