<?php

namespace Controllers;
use Models\Catalog;
use Traits\Categorized;

class CatalogController extends Controller
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


    public function __construct($table = 'catalogs'){
        $this->model = new Catalog($table);
        $this->module = 'katalogi';

        parent::__construct($this->model, $table);

        $this->setCategoryController($table, $this->isCategorized);


    }

    public function getMainOffers(){
        $articles = $this->getByPid(0);

        $result = [];
        foreach($articles as $article){
            $article['url'] = '/'.$article['title_url'];

            $result[] = $article;
        }

        return $result;
    }

    function getByPid($pid = 0)
    {

        $data = $this->model->getByPid($pid);

        $categories = [];
        foreach($data as $cat) {

            $cat['title'] = stripslashes($cat['title']);
            $cat['photo'] = $this->getPhotoUrl($cat['photo'], $cat['id']);
            $cat['photo_list'] = $this->getPhotoUrl($cat['photo_list'], $cat['id']);
            $categories[] = $cat;
        }

        return $categories;
    }
}