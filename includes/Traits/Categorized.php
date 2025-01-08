<?php

namespace Traits;
use Controllers\CategoryAdminController;
use Controllers\FeatureCategoryController;

trait Categorized{

    protected $categoryController;
    public $isCategorized = true;

    public function setCategoryController($table, $test = false){

        $this->categoryController = new CategoryAdminController($table.'_categories', $this->module, $this->module_url, $test);

        return $this;
    }

    public function getCategoryController(){
        return $this->categoryController;
    }

    public function createCategory($data, $files){

        return $this->getCategoryController()->create($data, $files);
    }

    public function deleteCategory($id){

        if($this->isCategoryEmpty($id))
            return $this->getCategoryController()->delete($id);
        $this->setError('Category is not empty!');
        return false;
    }

    public function categoryUpdate($data){
        $this->getCategoryController()->updateDescription($data);
        return $this->getCategoryController()->update($data);
    }

    public function editCategoryPhoto($data, $file){
        return $this->getCategoryController()->editPhoto($data, $file);
    }

    public function deleteCategoryPhoto($id){
        return $this->getCategoryController()->deletePhoto($id);
    }

    public function getCategoryById($id){
        return $this->getCategoryController()->getArticleById($id);
    }

    public function createCategorySelect($title, $type = 'id', $selected = '', $onChange = '', $path_id = '', $multiple = false){
        return $this->getCategoryController()->createHtmlSelect($title, $type, $selected, $onChange, $path_id, $multiple);
    }

    public function isCategoryEmpty($id){
        return !$this->model->isCategoryEmpty($id);
    }

    public function getUrlByPid($category_id, $withModule = true){
        $category = $this->getCategoryController()->getArticleById($category_id, $withModule);
        return $category['url'];
    }

    public function getCategoryDescription($id){
        return $this->getCategoryController()->loadDescriptionById($id);
    }

    public function updateCategoryDescription($data){
        return $this->getCategoryController()->updateDescription($data);
    }

    public function loadCategory($str){
        $data = $this->getCategoryController()->loadArticle($str);
        if($data['id'])
            return $data;
        return false;
    }

    public function getMainCategories($withSubcategories = true, $countProducts = false){

        $data =  $this->getCategoryController()->getMainCategories(false, $withSubcategories);

        $productsData= $this->model->getCategoriesQuantity();
        $productsQuantity = [];

        foreach($productsData as $product){
            $productsQuantity[$product['category_id']] = $product['quantity'];
        }

        return [
            'categories'        => $data,
            'products_quantity'  => $productsQuantity
        ];

    }

    public function getItemsByCategory($catId, $onlyActive = true){
        $result =  $this->model->getItemsByCategory($catId, $onlyActive);

        return $result;
    }

    public function countItems($id){
        return $this->model->countItems($id);
    }
    public function sd(){}
}

