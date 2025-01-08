<?php

namespace Controllers;
use Models\Module;

class ModuleController extends Controller{
    protected $model;

    public function __construct($table = 'modules'){
        $this->model = new Module($table);
        parent::__construct($this->model, $table);

    }


    public static function getFileType($module){
        $model = new Module('modules');

        return $model->getIdByName($module);
    }

    public static function getModuleUrl($id){
        $model = new Module('modules');
        $result = $model->getUrlById($id);

        if($result)
            return '/'.$result;

        return false;
    }

    public function loadNames(){
        $data = $this->model->LoadNames();
        return $data;
    }

    public static function loadModuleConfiguration($name){
        $object = new self();

        return $object->getModuleConf($name);
    }

    public function getModuleConf($module){
        $conf = $this->model->getModuleConf($module);

        if($conf){
            $conf = $this->prepare($conf);

            $conf['file_name'] = $conf['file_name'].'.inc.php';
            $conf['photo'] = $this->getPhotoUrl($conf['photo'], $conf['id']);
            return $conf;
        }

        return false;
    }

    public function getItems($pages, $page = 1, $onlyActive = true, $forAdmin = false, $category_id = false, $withModule = true, $category_path = false){
        if($forAdmin){
            $start = ($pages - $page) * $this->limit_admin;
            $articles = $this->model->loadArticles($start, $this->limit_admin, $onlyActive);
        }
        else{
            $start = ($pages - $page) * $this->limit_page;
            $articles = $this->model->loadArticles($start, $this->limit_page, $onlyActive);
        }


        foreach($articles as $key=>$article) {
            $articles[$key]['url'] = BASE_URL . $articles[$key]['title_url'];
        }
        return $articles;
    }

    public static function getClassName($name){
        $model = new Module();
        return $model->getClassName($name);
    }

    public static function getModuleUrls($url, $langs){

        $model = new Module();
        $data = $model->getModuleUrls($url, $langs);
        $result = [];

        foreach($data as $name){
            $result[$name['language_id']] = '/'.$name['title_url'];
        }
        return $result;
    }

    public static function getIdByTable(string $name){

        $model = new Module();
        return $model->getIdByTable($name);
    }

    public function getArticleById($id){
        $data = $this->model->getById($id);

        if ($data) {
            if($data['photo'])
                $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            if($data['video'])
                $data['video'] = $this->getVideoUrl($data['video'], $data['id'], $data['video_title']);
            $data['url'] = BASE_URL . '/' . $data['title_url'];
            return $data;
        } else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }


}