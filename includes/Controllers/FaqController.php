<?php


namespace Controllers;
use Models\Faq;


class FaqController extends Controller
{
    /** @var Faq */
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


    public function __construct($table = 'faq'){
        $this->model = new Faq($table);
        $this->module = $table;
        parent::__construct($this->model, $table);
    }

}