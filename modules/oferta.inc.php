<?php


if (!defined('SCRIPT_CHECK')) {
    die('No-Hack here buddy.. ;)');
}

use Controllers\OfferController;
use Controllers\ModuleController;
use Controllers\GalleryController;
use Controllers\RealisationsController;


$moduleController = new ModuleController();
$offerController = new OfferController();
$realisationController = new RealisationsController();

$module = 'oferta';
$moduleConfiguration = $moduleController->getModuleConf($module);


if (!$moduleConfiguration['active']) {
    if (!BASE_URL)
        redirect301('/');
    else
        redirect301(BASE_URL);
    die;
}

$strPageUrl = '';
if (is_array($rootController->params) && count($rootController->getParams()) > 0) {

    $strPageUrl = end($rootController->params);
    redirect301('/'.$strPageUrl);
}

if($strPageUrl === ''){
    $path[0]['name'] = 'Oferta';
    $path[0]['url'] = '';
    $mainOffers = $cache->getVariable('mainOffers');
    if(!$mainOffer) {
        $mainOffers = $offerController->getMainOffers();
        $cache->saveVariable($articles, "articles");
    }

    $rootController->assign('znacznik_kategoria', true);
    $rootController->assign('path', $path);
    foreach ($mainOffers as $key=>$offer) {
        unset($mainOffers[$key]);
        $mainOffers[$offer['title_url']] = $offer;
        $mainOffers[$offer['title_url']]['sub_offer'] = $offerController->getByPid($offer['id']);
    }
    $rootController->assign('mainOffers', $mainOffers);

    $realisations = $realisationController->getRandomItems(3);
    $rootController->assign('realizations', $realisations);
    $rootController->setPageTitle($moduleConfiguration['page_title'] ?? '');
    $rootController->setPageKeywords($moduleConfiguration['page_keywords'] ?? '');
    $rootController->setPageDescription($moduleConfiguration['page_description'] ?? '');
    $rootController->displayPage('offer/main.html');
}
else{

    if (!$category = $cache->getVariable('category') && !empty($strPageUrl)) {

        $category = $categoryController->getCategoryByTitleUrl($strPageUrl);

        $cache->saveVariable($category, "category");
    }

    if($category['gallery_id']){
        $galleryController = new GalleryController();
        $gallery = $galleryController->getArticleById($category['gallery_id']);
        if($gallery['active']){
            $category['gallery'] = $galleryController->getArticleById($category['gallery_id']);
            $category['gallery']['photos'] = $galleryController->getPhotos($category['gallery_id']);
        }
        else
            unset($gallery);

    }

    if (!$menuCategories = $cache->getVariable('menuCategories')) {
        $menuCategories = $categoryController->getMenu();
        $cache->saveVariable($menuCategories, "menuCategories");
    }

    $rootController->assign('menu_selected', BASE_URL . '/kategoria');

    if (!$category) {
        redirect301(BASE_URL . '/');
        die;
    }

    if ($category['active'] != 1) {
        $rootController->displayError($GLOBALS['_PAGE_UPDATING']);
        die;
    }

    if (!$pages = $cache->getVariable('pages')) {
        $pages = $productController->getPages($category['id'],  10);

        $cache->saveVariable($pages, "pages");
    }

    $page = 0;
    if ($rootController->hasParameter('page')) {
        $page = $rootController->getRequestParameter('page');
    } else {
        $page = 1;
    }

    if ($page > 1) {
        $google_prev_url = 'https://' . $_SERVER['HTTP_HOST'] . (str_replace(".html",
                    "",
                    $category['url']) . '?page=' . ($page - 1));
        $rootController->assign('google_prev_url', $google_prev_url);
    }
    if ($page < $pages['pages']) {
        $google_next_url = 'https://' . $_SERVER['HTTP_HOST'] . (str_replace(".html",
                    "",
                    $category['url']) . '?page=' . ($page + 1));
        $rootController->assign('google_next_url', $google_next_url);
    }

    if (!$products= $cache->getVariable('produkty')) {
        $products = $productController->getItems($page, $category['id'], 20);
        $cache->saveVariable($products, "produkty");
    }

    $data = [];

    foreach($products as $product){

        if(!isset($data[$product['id_category']])){
            $data[$product['id_category']]['category'] = $categoryController->getArticleById($product['id_category']);
        }
        $data[$product['id_category']]['products'][] = $product;
    }

    if (!$productsCount = $cache->getVariable('productsCount')) {

        $productsCount = $productController->countArticles($category['id'], true);
        $cache->saveVariable($productsCount, "productsCount");
    }

    $rootController->assign('header', "<span>" . $GLOBALS['PRODUKTY'] . " : </span>" . $category['title']);

    if (!$path = $cache->getVariable('path')) {
        $path = $categoryController->getPath($category['path_id']);
        $cache->saveVariable($path, "path");
    }
    /*
    if (!$path_urls = $cache->getVariable('path_urls')) {
        $path_urls = $categoryController->loadPathUrlsByPath($kategoria['path_id']);
        $cache->saveVariable($path_urls, "path_urls");
    }
    */

    foreach($other as $key=>$cat){

        if($cat['id'] == $category['id'])
            unset($other[$key]);
    }

    $features = $featureController->getByCategory($category['id']);
    $rootController->assign('features', $features);
    $rootController->assign('other', $other);
    //$rootController->assign('path_urls', $path_urls);
    $rootController->assign('default_count', $productController->getPageLimit());
    $rootController->assign('productsCount', $productsCount);
    $rootController->assign('znacznik_kategoria', true);
    $rootController->assign('products', $data);
    $rootController->assign('pages', $pages['pages']);
    $rootController->assign('page', $page);
    $rootController->assign('path', $path);
    $rootController->assign('article', $category);
    $rootController->assign('menuCategories', $menuCategories);
    $rootController->setPageTitle($category['page_title']);
    $rootController->setPageKeywords($category['page_keywords']);
    $rootController->setPageDescription($category['page_description']);
    $rootController->displayPage('offer/show.html');
}
