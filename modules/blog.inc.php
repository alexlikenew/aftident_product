<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FilesController;
use Controllers\BlogController;
use Controllers\ModuleController;
use Controllers\GalleryController;
use Controllers\CommentsController;
use Controllers\NewsController;


$module = 'blog'; // nazwa modulu, tabela w bazie, link w adresie
$filesController = new FilesController();
$blogController = new BlogController();
$galleryController = new GalleryController();
$moduleController = new ModuleController();
$newsController = new NewsController();

$moduleConfiguration = $moduleController->getModuleConf($module);
$rootController->assign('module', $moduleConfiguration);

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
}

$print = 0;
if ($rootController->hasParameter('print')) {
    $print = $rootController->getParameter('print');
}

$rootController->assign('menu_selected', BASE_URL . '/' . $module . '.html');

$path[] = [
    'name' => $moduleConfiguration['title'],
    'url' => BASE_URL . '/' . $moduleConfiguration['title_url'] . '.html'
];

if (!$article = $cache->getVariable('article')) {
    if (!empty($strPageUrl)) {
        $article = $blogController->loadArticle($strPageUrl);
        $cache->saveVariable($article, "article");
    }
}

if (!$article) {
    if (!$pages = $cache->getVariable('pages')) {
        $pages = $blogController->getPages();
        $cache->saveVariable($pages, "pages");
    }

    $page = 1;
    if ($rootController->hasParameter('page')) {
        $page = $rootController->getRequestParameter('page');
    }

    if (!$articles = $cache->getVariable('articles')) {
//        $articles = $blogController->getItems($pages, $page);

        $from = (($page - 1) * $blogController->getPageLimit()) + 1;

        $articles = $blogController->getLastItems(limit: 20,  from: $from);
        $cache->saveVariable($articles, "articles");
    }

    $lastAdded = $blogController->getLastItems(1);

    $rootController->assign('last_added', $lastAdded);

    //$randomItems = $newsController->getRandomItems(3);
    //$rootController->assign('random_items', $randomItems);

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

    $rootController->displayPage($module . '/main.html');

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

$lastAdded = $blogController->getLastItems(4, toSkip: $article['id']);

$rootController->assign('last_added', $lastAdded);

$path[] = [
    'name'  => $article['title'],
    'url'   => $article['url'],
    ];
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
$rootController->displayPage($module . '/show.html');