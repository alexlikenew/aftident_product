<?php

namespace Controllers;
use Models\PriceSteps;
class PriceStepsController extends Controller
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


    public function __construct($table = 'price_steps'){
        $this->model = new PriceSteps($table);
        $this->module = $table;
        parent::__construct($this->model, $table);
    }

}