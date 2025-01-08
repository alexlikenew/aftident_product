<?php

namespace Controllers;
use Models\Menu;

class MenuAdminController extends MenuController{
    protected $model;

    public function __construct($table = 'menu'){
        parent::__construct($table);
    }


    public function loadPath($pid, $group = -1, $menu_selected = '')
    {
        return $this->model->getPath($pid, $group, $menu_selected);
    }

    public function loadPathTitle($pid)
    {
        return $this->model->getPathTitle($pid);
    }

    public function getItemDescription($id)
    {
        return $this->model->getItemDescription($id);
    }

    public function getPid($id)
    {
        return $this->model->_getPIDbyID($id);
    }

    public function create(array $post, ?array $files = null)
    {

        return $this->model->createMenuItem($post);
    }

    public function update($post): bool
    {
        /*
        if($post['deleteImage'] != 0){
            $this->removeOldImage($post['id']);
        }

        if($file['menu_image']['name']){
            $fileName =  $this->saveFile($post['id'], $file);

            if(!$fileName){
                $this -> messages -> setError('Wystąpił błąd podczas zapisywania pliku!');
                return false;
            }

            $has_image = 1;
            $image = $fileName;
        }
        */

        return $this->model->update($post);


    }

    public function removeOldImage($id){
        $data = $this->model->getImageName($id);

        if($data){
            if(isset($data['image']) && $data['image']){
                $filePath = $this->dir . DIRECTORY_SEPARATOR . $data['image'];
                unlink($filePath);
            }
        }
    }

    public function deleteItem($id)
    {
        return $this->model->deleteItem($id);
    }


    public function getMap() {
        $map = $this->model->loadMenu(0);
        for ($j = 0; $j < count($map); $j++) {
            $map[$j]['submenu'] = $this->model->_loadMenu($map[$j]['id'], '%');
        }
        return $map;
    }

    public function loadTree($mm) {
        for ($i = 0; $i < count($mm); $i++) {
            $map = $this->model->loadMenu(0, $mm[$i]['group']);
            for ($j = 0; $j < count($map); $j++) {
                $map[$j]['submenu'] = $this->model->loadMenu($map[$j]['id']);
                $maps[$i] = $map;
            }
        }
        return $maps;
    }

    public function createPhotoName($article, $files, $key){
        return isset($article['id'])
            ? changeFilename($article['id'] . "-" . rand(0, 1000), '', $files['photo']['name'])
            : '';

    }

    public function move($id, $order_new):bool{
        $item = $this->getArticleById($id);
        if($item)
           return  $this->model->updateItemsOrder($item, $order_new);

        return false;
    }
}
