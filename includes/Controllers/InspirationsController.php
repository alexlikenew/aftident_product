<?php

namespace Controllers;
use Models\Inspirations;

class InspirationsController extends Controller
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


    public function __construct($table = 'inspirations'){
        $this->model = new Inspirations($table);
        $this->module = 'inspiracje';
        parent::__construct($this->model, $table);
    }
}