<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FilesController;
use Controllers\PagesController;
use Controllers\ModuleController;
use Controllers\ProductController;
use Controllers\InspirationsController;

$moduleName = 'wyprzedaz';
$filesController = new FilesController();
$pagesController = new PagesController();
$productController = new ProductController();
$moduleController = new ModuleController();
$inspirationsController = new Controllers\InspirationsController();

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}
$categories = $productController->getMainCategories(true, true);


$otherUrls = ModuleController::getModuleUrls($moduleConfiguration['title_url'], $LANG_ID);
$rootController->assign('other_urls', $otherUrls);


$onSale = $productController->getOnSaleProducts(5);
$rootController->assign('on_sale', $onSale);
$rootController->assign('categories', $categories);

$inspirations = $inspirationsController->getRandomItems(3);

$rootController->assign('inspirations', $inspirations);
$path[0]['name'] = $moduleConfiguration['title'];
$path[0]['url'] = BASE_URL.'/'.$moduleConfiguration['title_url'];

$rootController->assign('path', $path);
$rootController->assign('module', $moduleConfiguration);
if(TITLE_PREFIX)
    $rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_PRODUCTS'] . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
else
    $rootController->setPageTitle( $moduleConfiguration['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($moduleConfiguration['page_keywords'] ?? '');
$rootController->setPageDescription($moduleConfiguration['page_description'] ?? '');
$rootController->displayPage( 'products/main.html');
