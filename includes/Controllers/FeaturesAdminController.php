<?php

namespace Controllers;

class FeaturesAdminController extends FeaturesController
{
    public function __construct($table = 'features')
    {
        parent::__construct($table);
    }

    public function create(array $data, array $files = null)
    {

        $id =  $this->model->create($data);

        if($id){
            $data['id'] = $id;
            foreach ($data['title'] as $i => $title) {
                $data['title'][$i] = prepareString($title, true);
                $this->model->createDescription($data, $i);
            }
            return $id;
        }
    }

    public function update(array $post): bool
    {
        $this->updateCategories($post);
        foreach ($post['title'] as $i => $title) {
            $post['title'][$i] = prepareString($title, true);
            $desc = $this->model->loadDescriptionById($post['id'], $i);
            if ($desc) {
                $this->model->updateDescription($post, $i);
            } else {
                $this->model->createDescription($post, $i);
            }
        }

        $this->setInfo($GLOBALS['_ADMIN_UPDATE_SUCCESS']);
        return true;
    }

    public function updateCategories($data){
        return $this->model->updateCategories($data);
    }

    public function getByFeatureId($id = null, $pages = 10, $page = 1, $forAdmin = false){
        if(!$id)
            return $this->loadArticlesAdmin($pages, $page);
        else
            return $this->getFeatureValues($id, false, $forAdmin);
    }

    public function createValue(array $data):int{

        $id = $this->model->createValue($data);

        if($id){
            $data['id'] = $id;
            foreach($data['title'] AS $langId=>$title){
                $desc = $this->model->loadValueDescription($data['id'], $langId);

                if ($desc) {
                    $this->model->updateValueDescription($data, $langId);
                } else {
                    $this->model->createValueDescription($data, $langId);
                }
            }
        }

        return $id;
    }

    public function getFeatureValue($id){
        return $this->model->getFeatureValue($id);
    }

    public function updateValue($data){

        if($this->model->update($data, $data['id']))
            foreach ($data['title'] as $i => $title) {
                $data['title'][$i] = prepareString($title, true);
                $desc = $this->model->loadValueDescription($data['id'], $i);
                if ($desc) {
                    $this->model->updateValueDescription($data, $i);
                } else {
                    $this->model->createValueDescription($data, $i);
                }
            }
        $this->setInfo($GLOBALS['_ADMIN_UPDATE_SUCCESS']);
        return true;
    }

    public function setValueActive($id, $active){
        return $this->model->setValueActive($id, $active);
    }

    public function deleteValue($id){
        $this->model->deleteValue($id);
        $this->model->deleteValueDescriptions($id);
        return true;
    }

    public function delete(int $id = null)
    {
        return $this->model->delete($id);
    }

    public function moveValue(int $id, int $order_new):bool{
        $item = $this->getFeatureValue($id);

        if($item){
            return $this->model->updateValueOrder($item, $order_new);
        }
        return false;
    }
}