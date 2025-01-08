<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

use Controllers\ModuleController;
use Controllers\SaleDepartmentController;


$module = 'kadra';
//$filesController = new FilesController();
//$pagesController = new PagesController();

$moduleController = new ModuleController();
$saleDepartmentController = new SaleDepartmentController();

$articles = $saleDepartmentController->getItems(1);

$moduleConfiguration = $moduleController->getModuleConf($module);

$strPageUrl = '';
if (is_array($rootController->params) && count($rootController->getParams()) > 0) {
    $strPageUrl = end($rootController->params);
}

$print = 0;
if ($rootController->hasParameter('print')) {
    $print = $rootController->getParameter('print');
}

$rootController->assign('menu_selected', BASE_URL . '/' . $module . '.html');


$path[0]['name'] = $moduleConfiguration['title'];

$path[0]['url'] = BASE_URL . '/' . $moduleConfiguration['title_url'] ;

$otherUrls = ModuleController::getModuleUrls($moduleConfiguration['title_url'], $LANG_ID);
$rootController->assign('other_urls', $otherUrls);

$rootController->assign('workers', $articles);
$rootController->assign('module', $moduleConfiguration);
$rootController->assign('path', $path);
if(TITLE_PREFIX)
    $rootController->setPageTitle(TITLE_PREFIX .  ' - '.$moduleConfiguration['page_title'] . TITLE_SUFFIX);
else
    $rootController->setPageTitle($moduleConfiguration['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($moduleConfiguration['page_keywords']);
$rootController->setPageDescription($moduleConfiguration['page_description']);
$rootController->displayPage( 'team/main.html');
