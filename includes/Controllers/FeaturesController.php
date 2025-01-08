<?php

namespace Controllers;
use Models\Features;


class FeaturesController extends Controller
{
    protected $model;
    protected $module;
    protected int $scale_width;
    protected int $scale_height;
    protected int $list_height;
    protected int $list_width;
    protected int $detail_width;
    protected int $detail_height;
    protected int $main_width;
    protected int $main_height;


    public function __construct($table = 'features'){
        $this->model = new Features($table);
        $this->module = 'cechy';
        parent::__construct($this->model, $table);
    }

    public function getItems($pages, $page = 1, $onlyActive = true, $forAdmin = false,  $category_id = false, $withModule = true, $category_path = false){

        $start = ($pages - $page) * $this->limit_page;

        $articles = $this->model->loadArticles($start, $this->limit_admin, $onlyActive);


        foreach($articles as $key=>$article) {
            $articles[$key]['content_short'] = strip_tags($articles[$key]['content_short']);

            $articles[$key]['url'] = BASE_URL . '/' . $this->module . '/' . $articles[$key]['title_url'];
            if ($articles[$key]['photo'])
                $articles[$key]['photo'] = $this->getPhotoUrl($articles[$key]['photo'], $articles[$key]['id']);
        }
        return $articles;
    }

    public function getFeatureValues($id, $onlyActive = true, $forAdmin = false){
        $data = $this->model->getValuesById($id, $onlyActive);

        //if($forAdmin)
            return $data;

            /*$result = [];
        foreach($data as $item){
            if($item['custom_value'])
                $result['custom'][] = $item;
            else
                $result['only_value'][] = $item;
        }
        return $result;
            */
    }

    public function loadValueDescriptionById($id){
        $data  = $this->model->loadValueDescription($id);
        $descriptions = [];
        foreach($data as $desc) {
            $desc['title'] = str_replace('"', '&quot;', $desc['title']);
            $desc['content'] = trim(preg_replace('/\s\s+/', ' ', $desc['content']));
            $descriptions[$desc['language_id']] = $desc;
        }

        return $descriptions;
    }

    public function getAll($onlyActive = true, $toSkip = false){
        $data = $this->model->getAll($onlyActive);

        $features = [];
        foreach($data as $feature){
            $features[$feature['id']] = [
                'id'       => $feature['id'],
                'title'    => $feature['title'],
                'items'    => $this->getFeatureValues($feature['id'], true)
            ];
        }
        return $features;
    }

    public function getByCategory($id, $onlyActive = true){
        $data = $this->model->getAll($onlyActive);

        $features = [];
        foreach($data as $feature){
            $feature['categories'] = json_decode($feature['categories'], true);

            if(empty($feature['categories']) || !in_array($id, $feature['categories']))
                continue;

            $features[$feature['id']] = [
                'title'     => $feature['title'],
                'values'    => $this->getFeatureValues($feature['id'], true)
            ];
        }
        return $features;
    }

    public function getArticleById($id){
        $data = $this->model->getById($id);

        if ($data) {
            if($data['photo'])
                $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            if($data['video'])
                $data['video'] = $this->getVideoUrl($data['video'], $data['id'], $data['video_title']);
            $data['categories'] = json_decode($data['categories'], true);
            if(!$data['categories'])
                $data['categories'] = [];
            return $data;
        } else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

}