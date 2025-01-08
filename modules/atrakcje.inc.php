<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');


use Controllers\ModuleController;
use Controllers\AttractionsController;
//use Controllers\InspirationsController;
use Controllers\RoomsController;

$module = 'atrakcje';
//$filesController = new FilesController();
//$pagesController = new PagesController();
$newsController = new AttractionsController();
$roomsController = new RoomsController();

$moduleController = new ModuleController();
//$inspirationController = new InspirationsController();

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

if (!$article = $cache->getVariable('article')) {
    if (!empty($strPageUrl)) {
        $article = $newsController->loadArticle($strPageUrl);
        $cache->saveVariable($article, "article");
    }
}

$allRooms = $roomsController->getAll();
$rootController->assign('all_rooms', $allRooms);

if (!$article) {
    if (!$pages = $cache->getVariable('pages')) {
        $pages = $newsController->getPages();
        $cache->saveVariable($pages, "pages");
    }

    $page = 1;
    if ($rootController->hasParameter('page')) {
        $page = $rootController->getRequestParameter('page');
    }

    if (!$articles = $cache->getVariable('articles')) {
        $articles = $newsController->getItems($pages, $page);
        $cache->saveVariable($articles, "articles");
    }

    $otherUrls = ModuleController::getModuleUrls($moduleConfiguration['title_url'], $LANG_ID);
    $rootController->assign('other_urls', $otherUrls);

    //$inspirations = $inspirationController->getRandomItems(2);
    //$rootController->assign('inspirations', $inspirations);

    $rootController->assign('articles', $articles);
    $rootController->assign('pages', $pages);
    $rootController->assign('page', $page);
    $rootController->assign('module', $moduleConfiguration);

    $rootController->assign('path', $path);
    if(TITLE_PREFIX)
        $rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_BLOG'] . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
    else
        $rootController->setPageTitle( $moduleConfiguration['page_title'] . TITLE_SUFFIX);
    $rootController->setPageKeywords($moduleConfiguration['page_keywords']);
    $rootController->setPageDescription($moduleConfiguration['page_description']);

    $rootController->displayPage( 'attractions/main.html');

    die;
}

if (($article['auth'] == 1) and !LOGGED) {
    $rootController->displayError($GLOBALS['_PAGE_AUTH']);
    die;
}

if ($article['comments'] == 1) {

    $comments_conf['group'] = Comments::NEWS;
    $comments_conf['parent_id'] = $article['id'];
    $comments_conf['reload_url'] = $article['url'];

    require_once ROOT_PATH . '/includes/comments.inc.php';
}

$otherUrls = $newsController->getOtherUrls($article, $LANG_ID);

$rootController->assign('other_urls', $otherUrls);

$randomItems = $newsController->getRandomItems(20, $article['id']);
$rootController->assign('otherItems', $randomItems);
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

$path[1]['name'] = $article['title'];
$path[1]['url'] = $article['url'];
$articles = $newsController->getAll(0, 0, $article['id']);
shuffle($articles);
$rootController->assign('articles', array_slice($articles,0, 5));
$baseURL = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'];
$rootController->assign('menuBlack', 1);
$rootController->assign('baseURL', $baseURL);
$rootController->assign('module', $moduleConfiguration);
$rootController->assign('path', $path);
$rootController->assign('article', $article);
$rootController->setPageTitle($article['page_title']);
$rootController->setPageKeywords($article['page_keywords']);
$rootController->setPageDescription($article['page_description']);
$rootController->displayPage( 'attractions/show.html');
