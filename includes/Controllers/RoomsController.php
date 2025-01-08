<?php

namespace Controllers;

use Models\Rooms;

class RoomsController extends Controller
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


    public function __construct($table = 'rooms'){
        $this->model = new Rooms($table);
        $this->module = $table;
        $this->module = 'pokoje';
        parent::__construct($this->model, $table);
    }

    public function getRandomItems($limit, $toSkip = null){
        $data = parent::getRandomItems($limit, $toSkip);
        $result = [];
        foreach($data as $item){
            $item['price'] = number_format($item['price'], 2, '.', '');
            $result[] = $item;
        }

        return $result;
    }
}
