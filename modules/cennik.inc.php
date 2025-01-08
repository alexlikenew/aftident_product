<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//use Controllers\FilesController;
//use Controllers\FilesController;
//use Controllers\PagesController;
use Controllers\ModuleController;
use Controllers\PriceListController;
use Controllers\FormController;

$module = 'cennik';
//$filesController = new FilesController();
//$pagesController = new PagesController();

$moduleController = new ModuleController();
$formController = new FormController();

$form = $formController->getArticleById(1);
/*
foreach($form['inputs'] as $input){
    dump($input->getContent());
}
die;
*/
$action = '';

if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}


//$moduleConfiguration = $moduleController->getModuleConf($module);

$strPageUrl = '';
if (is_array($rootController->params) && count($rootController->getParams()) > 0) {
    $strPageUrl = $rootController->params[1];
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
$rootController->assign('form', $form);
$rootController->assign('module', $moduleConfiguration);
$rootController->assign('path', $path);
if(TITLE_PREFIX)
    $rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_BLOG'] . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
else
    $rootController->setPageTitle($moduleConfiguration['page_title'] . TITLE_SUFFIX);

$rootController->setPageKeywords($moduleConfiguration['page_keywords']);
$rootController->setPageDescription($moduleConfiguration['page_description']);
$rootController->displayPage( 'price-list/main.html');



?>