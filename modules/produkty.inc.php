<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FilesController;
use Controllers\PagesController;
use Controllers\ModuleController;
use Controllers\ProductController;
use Controllers\InspirationsController;

$moduleName = 'produkty';
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
$randomProducts = $productController->getRandomItems(10);
$rootController->assign('random_products', $randomProducts);

$otherUrls = ModuleController::getModuleUrls($moduleConfiguration['title_url'], $LANG_ID);
$rootController->assign('other_urls', $otherUrls);


$onSale = $productController->getOnSaleProducts(5);
$rootController->assign('on_sale', $onSale);
$rootController->assign('categories', $categories);

if (!$pages = $cache->getVariable('pages')) {
    $pages = $productController->getPages();
    $cache->saveVariable($pages, "pages");
}

$page = 1;
if ($rootController->hasParameter('page')) {
    $page = $rootController->getRequestParameter('page');
}

if (!$products = $cache->getVariable('articles')) {
    $products = $productController->getItems($pages, $page);
    $cache->saveVariable($products, "products");
}

$inspirations = $inspirationsController->getRandomItems(3);

$rootController->assign('inspirations', $inspirations);
$path[0]['name'] = $moduleConfiguration['title'];
$path[0]['url'] = BASE_URL.'/'.$moduleConfiguration['title_url'];

$rootController->assign('products', $products);
$rootController->assign('path', $path);
$rootController->assign('module', $moduleConfiguration);
$rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_PRODUCTS'] . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($moduleConfiguration['page_keywords'] ?? '');
$rootController->setPageDescription($moduleConfiguration['page_description'] ?? '');
$rootController->displayPage( 'products/main.html');
?>
