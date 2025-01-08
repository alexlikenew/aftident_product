<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\PagesController;
use Controllers\NewsController;
use Controllers\FaqController;
use Controllers\ProductController;
use Controllers\InspirationsController;
use Controllers\CatalogController;

$productController = new ProductController();
$pageController = new PagesController();
$newsController = new NewsController();
$faqController = new FaqController();
$inspirationsController = new InspirationsController();
$catalogsController = new CatalogController();


//$moduleConfiguration = $moduleController->getModuleConf('szukaj');

//$rootController->assign('article', $moduleConfiguration);

$keyword = '';

$keyword = $rootController->getRequestParameter('keyword');

$keyword = urldecode(str_replace('-', ' ', $keyword));


$count = 0;
if (!empty($keyword)) {
/*
    $siteItems = $pageController->searchItems($keyword);

    if ($siteItems) {
        $sitesCount = count($siteItems);
        $rootController->assign('sites_count', $sitesCount);
        $rootController->assign('sites', $siteItems);
    }

    $newsItems = $newsController->searchItems($keyword);
    if ($newsItems) {
        $newsCount = count($newsItems);
        $rootController->assign('news_count', $newsCount);
        $rootController->assign('news', $newsItems);
    }

    $faqItems = $faqController->searchItems($keyword);
    if ($faqItems) {
        $faqCount = count($faqItems);
        $rootController->assign('faq_count', $faqCount);
        $rootController->assign('faq', $faqItems);
    }

    $galleryItems = $galleryController->searchItems($keyword);
    if ($galleryItems) {
        $galleryCount = count($galleryItems);
        $rootController->assign('gallery_count', $galleryCount);
        $rootController->assign('galleries', $galleryItems);
    }

    $categoryItems = $categoryController->searchItems($keyword);
    if ($categoryItems) {
        $categoryCount = count($categoryItems);
        $rootController->assign('category_count', $categoryCount);
        $rootController->assign('categores', $categoryItems);
    }
*/
    $items = [];
    $productItems = $productController->searchItems($keyword);
    if($productItems)
        $items = $productItems;

    $inspirationItems = $inspirationsController->searchItems($keyword);
    if($inspirationItems)
        $items = array_merge($items, $inspirationItems);

    $newsItems = $newsController->searchItems($keyword);
    if($newsItems)
        $items = array_merge($items, $newsItems);

    $catalogItems = $catalogsController->searchItems($keyword);
    if($catalogItems)
        $items = array_merge($items, $catalogItems);

    $faqItems = $faqController->searchItems($keyword);
    if($faqItems)
        $items = array_merge($items, $faqItems);

    $pageItems = $pageController->searchItems($keyword);
    if($pageItems)
        $items = array_merge($items, $pageItems);


    if (!empty($items)) {
        $itemsCount = count($items);
       // dd($productItems);
        if ($rootController->getRequestParameter('autocomplete') === 'json') {

            $pageController->updateSearch($keyword, $itemsCount);
            $products = $productController->convertSearchResultToAutocomplete($items, false);
            $json = json_encode($products);
            header('Content-Type: application/json');
            echo $json;
            die;

        }
        $rootController->assign('count', $productItems);
        $rootController->assign('items', $productItems);
    }

    $count = /*$sitesCount + $newsCount + $faqCount + $galleryCount + $categoryCount + */ $productCount;
}
else
    redirect404();
/*
$path[0]['name'] = $moduleConfiguration['title'] ?? $GLOBALS['_PAGE_SEARCH'] ;
$path[0]['url'] = BASE_URL . '/' . $module;

$rootController->assign('path', $path);

if ($count > 0) {
    $pageController->updateSearch($keyword, $count);
    $rootController->assign('find_result', true);
    $rootController->setPageTitle(TITLE_PREFIX . $keyword . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
} else {
    $rootController->setPageTitle(TITLE_PREFIX . 'Szukaj - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
}

$rootController->assign('keyword', $keyword);
$rootController->assign('module', $moduleConfiguration);
$rootController->setPageKeywords($moduleConfiguration['page_keywords']);
$rootController->setPageDescription($moduleConfiguration['page_description']);
$rootController->displayPage('products/search.html');
*/
?>