<?php

namespace Controllers;
use Models\MailTemplate;

class MailTemplateController extends Controller
{
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


    public function __construct($table = 'mail_template'){
        $this->model = new MailTemplate($table);
        $this->module = $table;
        parent::__construct($this->model, $table);
    }

    public function getItems($pages, $page = 1, $onlyActive = true, $forAdmin = false, $ctaegory_id = false){

        if($forAdmin){
            $start = ($pages - $page) * $this->limit_admin;
            $articles = $this->model->loadArticles($start, $this->limit_admin, $onlyActive);
        }
        else {
            $start = ($pages - $page) * $this->limit_page;
            $articles = $this->model->loadArticles($start, $this->limit_page, $onlyActive);
        }

        foreach($articles as $key=>$article) {
            if($articles[$key]['date_add'])
                $articles[$key]['date_add'] = date('d.m.Y', strtotime($articles[$key]['date_add']));

        }
        return $articles;
    }

}