<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FilesController;
use Controllers\PagesController;
use Controllers\ModuleController;
use Controllers\InspirationsController;
use Controllers\GalleryController;
use Controllers\NewsController;
use Controllers\ProductController;

$moduleName = 'inspiracje';
$filesController = new FilesController();
$pagesController = new PagesController();
$inspirationController = new InspirationsController();
$galleryController = new GalleryController();
$newsController = new NewsController();
$productController = new ProductController();

$strPageUrl = '';
if (is_array($rootController->params) && count($rootController->getParams()) > 0) {
    $strPageUrl = end($rootController->params);
}

$print = 0;
if ($rootController->hasParameter('print')) {
    $print = $rootController->getParameter('print');
}

$rootController->assign('menu_selected', BASE_URL . '/' . $moduleConfiguration['title_url'] );


$path[0]['name'] = $moduleConfiguration['title'];

$path[0]['url'] = BASE_URL . '/' . $moduleConfiguration['title_url'] ;

if (!$article = $cache->getVariable('article')) {
    if (!empty($strPageUrl)) {
        $article = $inspirationController->loadArticle($strPageUrl);
        $cache->saveVariable($article, "article");
    }
}

$news = $newsController->getRandomItems(4);
$rootController->assign('otherItems', $news);

if (!$article) {
    if (!$pages = $cache->getVariable('pages')) {
        $pages = $inspirationController->getPages();
        $cache->saveVariable($pages, "pages");
    }

    $page = 1;
    if ($rootController->hasParameter('page')) {
        $page = $rootController->getRequestParameter('page');
    }

    if (!$articles = $cache->getVariable('articles')) {
        $articles = $inspirationController->getItems($pages, $page);
        $cache->saveVariable($articles, "articles");
    }

    $otherUrls = ModuleController::getModuleUrls($moduleConfiguration['title_url'], $LANG_ID);
    $rootController->assign('other_urls', $otherUrls);

    $rootController->assign('articles', $articles);
    $rootController->assign('pages', $pages);
    $rootController->assign('page', $page);
    $rootController->assign('module', $moduleConfiguration);

    $rootController->assign('path', $path);
    if(TITLE_PREFIX)
        $rootController->setPageTitle(TITLE_PREFIX .  ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
    else
        $rootController->setPageTitle( $moduleConfiguration['page_title'] . TITLE_SUFFIX);
    $rootController->setPageKeywords($moduleConfiguration['page_keywords']);
    $rootController->setPageDescription($moduleConfiguration['page_description']);

    $rootController->displayPage( 'inspirations/main.html');

    die;
}

if (($article['auth'] == 1) and !LOGGED) {
    $rootController->displayError($GLOBALS['_PAGE_AUTH']);
    die;
}

$productsIds = json_decode($article['products']);
$article['products'] = [];
if(is_array($productsIds) && !empty($productsIds)){
    foreach($productsIds as $pId){
        $article['products'][] = $productController->getArticleById($pId);
    }
}


if ($article['comments'] == 1) {

    $comments_conf['group'] = Comments::NEWS;
    $comments_conf['parent_id'] = $article['id'];
    $comments_conf['reload_url'] = $article['url'];

    require_once ROOT_PATH . '/includes/comments.inc.php';
}

$rootController->assign('print', true);
if ($print == 1) {
    $rootController->assign('article', $article);
    $rootController->display('misc/print.html');
    die;
}

if ($article['active'] != 1) {
    $rootController->displayError($GLOBALS['_PAGE_UPDATING']);
    die;
}

if (!empty($article['gallery_id'])) {
    if (!empty($article['gallery_id'])) {
        $gallery = $galleryController->getArticleById($article['gallery_id']);
        if($gallery['active']){
            $article['gallery'] = $gallery;
            $article['gallery']['photos'] = $galleryController->getPhotos($article['gallery_id']);

        } else {
            unset($gallery);
        }
    }
}

$otherUrls = $inspirationController->getOtherUrls($article, $LANG_ID);

$rootController->assign('other_urls', $otherUrls);

$path[1]['name'] = $article['title'];
$path[1]['url'] = $article['url'];

//$articles = $blogController->getAll(0, 0, $article['id']);
//shuffle($articles);
//dump($article); die();
//$rootController->assign('articles', array_slice($articles,0, 5));
$baseURL = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
$rootController->assign('baseURL', $baseURL);
$rootController->assign('module', $moduleConfiguration);
$rootController->assign('path', $path);
$rootController->assign('article', $article);
$rootController->setPageTitle($article['page_title']);
$rootController->setPageKeywords($article['page_keywords']);
$rootController->setPageDescription($article['page_description']);
$rootController->displayPage( 'inspirations/show.html');
?>