<?php

namespace Controllers;
use Models\Pages;

class PagesController extends Controller{

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

    public function __construct($table = 'pages'){
        $this->model = new Pages($table);
        $this->module = 'strony';
        parent::__construct($this->model, $table);
    }

    public function getNames($langId) {
        $data = $this->model->getNames($langId);
        $result = [];
        foreach($data as $item)
            if($item['title'])
                $result[] = $item;
        return $result;
    }

    public function getArticleById($id){
        $data = $this->model->getById($id);

        if ($data) {
            if($data['photo'])
                $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            if($data['video'])
                $data['video'] = $this->getVideoUrl($data['video'], $data['id'], $data['video_title']);
            $data['url'] = '/'.$data['title_url'];
            return $data;
        } else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

    public function getItemUrl($item, $languageId = _ID){
       return  '/' .$item['title_url'];
    }

    # sitemap-xml
    public function getSitemapData($title = '', $moduleLink = false)
    {

        $data = $this->getAll();
        $result['title'] = $GLOBALS['pages'];

        foreach ($data as $item) {
            $item['url'] = $this->getItemUrl($item);
            $result['items'][] = $item;
        }

        return $result;
    }

    public function getMainPage(){
        $article = $this->model->getMainPage();

        if ($article) {
            $article = $this->prepare($article);
            $article['url'] = $this->getItemUrl($article);

            $article['photo'] = $this->getPhotoUrl($article['photo'], $article['id']);
            $article['files'] = $this->getItemFiles($article['id']);

            if($this->module_id){
                $article['blocks'] = $this->getBlocksById($article['id'], $this->module_id);
            }

            //$article['other_urls'] = [];// $this->getOtherUrls($article['id']);

            return $article;
        } else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }
}
