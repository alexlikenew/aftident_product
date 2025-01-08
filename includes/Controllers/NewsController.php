<?php

namespace Controllers;
use Models\News;
class NewsController extends Controller
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


    public function __construct($table = 'news'){
        $this->model = new News($table);
        $this->module = 'pakiety';
        parent::__construct($this->model, $table);
    }
}