<?php

namespace Controllers;

use Models\Attractions;
class AttractionsController extends Controller
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


    public function __construct($table = 'attractions'){
        $this->model = new Attractions($table);
        $this->module = $table;
        $this->module = 'atrakcje';
        parent::__construct($this->model, $table);
    }

    public function getItems($pages, $page = 1, $onlyActive = true, $forAdmin = false, $category_id = false, $withModule = true, $category_path = false){
        $data = parent::getItems($pages, $page, $onlyActive, $forAdmin, $category_id, $withModule, $category_path);

        if($forAdmin)
            return $data;

        $result =[];

        foreach($data as $item){
            if($item['hotel_attraction'])
                $result['hotel_attractions'][]  = $item;
            else
                $result['other_attractions'][] = $item;
        }
        return $result;
    }
}