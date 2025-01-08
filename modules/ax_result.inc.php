<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ModuleController;
use Controllers\PriceListController;
use Controllers\FormController;

if($action && $action == 'generate'){
    $priceListController = new PriceListController();
    $result = $priceListController->generateResult($rootController->post->getAll());
    echo json_encode($result);
    die;
}
