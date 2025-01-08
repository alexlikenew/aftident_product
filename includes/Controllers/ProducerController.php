<?php


namespace Controllers;
use Models\Producer;

class ProducerController extends Controller
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


    public function __construct($table = 'producers'){
        $this->model = new Producer($table);
        $this->module = 'producenci';
        parent::__construct($this->model, $table);
    }
}