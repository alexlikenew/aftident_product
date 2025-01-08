<?php

namespace Controllers;
use Models\Slider;
class SliderController extends Controller{
    protected $model;

    public function __construct($table = 'slider'){
        $this->model = new Slider($table);
        parent::__construct($this->model, $table);
    }

    public function getSliders(){
        $result =  $this->model->getAll();

        $sliders = [];
        foreach($result as $row) {
            $row['photos'] = $this->getPhotos($row['id']);
            $sliders[$row['label']] = $row;
        }
        return $sliders;
    }

    public function getPhotos($slider_id, $page_keywords = ''){
        if (!empty($page_keywords)) {
            $anchor = explode(',', $page_keywords);
            $liczba = count($anchor);
        }
        $result = $this->model->getPhotos($slider_id);
        $items = [];
        foreach($result as $row) {
            if (!empty($page_keywords)) {
                $row['anchor'] = trim($anchor[rand(0, $liczba - 1)]);
            }
            $row['src'] = $this->getPhotoUrl($row['name'], $slider_id);
            if ($row['src']) {
                $items[] = $row;
            }
        }
        return $items;
    }

    public function getArticleById($id){
        $data = $this->model->getById($id);

        if ($data)
            return $data;
        else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

    public function getUrl(){
        return $this->url;
    }

    public function getThumbSize($id) {
        $thumb = [];

        $config = $this->model->getById($id);
        $thumb['width'] = $config['width'];
        $thumb['height'] = $config['height'];

        return $thumb;
    }

    public function getSliderById(int $id){

        return $this->model->getById($id);
    }

    public function loadArticle($str){

        $article = $this->model->getArticle($str);
        if($article['id'])
            $article['photos'] = $this->getPhotos($article['id']);
        return $article;
    }

    public function getItem($slug, $isActive = true) {

        $data = $this->model->getArticle($slug, $isActive);

        if($data)
            $data['photos'] = $this->getPhotos($data['id']);

        return $data;
    }


}
