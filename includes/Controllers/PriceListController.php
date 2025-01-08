<?php

namespace Controllers;
use Models\PriceList;

class PriceListController extends Controller
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


    public function __construct($table = 'price_list'){
        $this->model = new PriceList($table);
        $this->module = $table;
        parent::__construct($this->model, $table);
    }

    public function generateResult($data){

        $packagesController = new ProductController();
        $packages = $packagesController->getAll();
        $result = [];

        foreach($packages as $package){
            $item = $packagesController->getArticleById($package['id']);
            foreach($item['price_step'] as $step){
                $percent = 0;
                if($data['project_value'] >= $step['from_price'] && $data['project_value'] <= $step['to_price']){
                    $value = $data['project_value'] * ($step['value'] / 100);
                    $percent = $step['value'];

                    $value = $value * (1 + ($step['min_time_'.$data['min_time']] / 100));
                    $percent += $step['min_time_'.$data['min_time']];

                    if($data['extra_value'] && ($percent + $step['extra_yes'] <= 10)){
                        $percent += $step['extra_yes'];
                        $value = $value * (1 + ($step['extra_yes']/100));
                    }
                    if($data['advanced_value'] && ($percent + $step['advanced_yes'] <= 10)){
                        $percent += $step['advanced_yes'] ;
                        $value = $value * (1 + ($step['advanced_yes'] / 100));
                    }

                    $result[$item['id']] = [
                        'title'     => $item['title'],
                        'value'     => round($value, 2),
                        'percent'   => $percent,
                    ];
                    break;
                }
            }
        }
        return $result;
    }

}