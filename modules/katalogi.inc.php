<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FilesController;
use Controllers\PagesController;
use Controllers\ModuleController;
use Controllers\CatalogController;

$moduleName = 'katalogi';
$filesController = new FilesController();
$pagesController = new PagesController();
$catalogController = new CatalogController();
$moduleController = new ModuleController();

$filesType = ModuleController::getFileType($moduleName);

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

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

$path[0]['name'] = $moduleConfiguration['title'];
$path[0]['url'] = BASE_URL . '/' . $moduleConfiguration['title_url'] ;

$category = $catalogController->loadCategory($strPageUrl);
if($category){
    $path[] = [
        'name'  => $category['title'],
        'url'   => $category['url']
    ];
}


if (!$pages = $cache->getVariable('pages')) {
    if(isset($category['id']))
        $pages = $catalogController->getPages(category_id: $category['id']);
    else
        $pages = $catalogController->getPages();
    $cache->saveVariable($pages, "pages");
}
$page = 1;
if ($rootController->hasParameter('page')) {
    $page = $rootController->getRequestParameter('page');
}
$mainCategories = $catalogController->getMainCategories();

if (!$articles = $cache->getVariable('articles')) {
    if($category)
        $articles = $catalogController->getItems($pages, $page, category_id :$category['id']);
    else
        $articles = $catalogController->getItems($pages, $page);
    $cache->saveVariable($articles, "articles");
}

foreach($articles as $key=>$article){
    $articles[$key]['files'] = $filesController->loadFiles($article['id'], $filesType);
}

if($category)
    $otherUrls = $catalogController->getCategoryController()->getOtherUrls($category, $LANG_ID, true);
else
    $otherUrls = ModuleController::getModuleUrls($moduleConfiguration['title_url'], $LANG_ID);


$rootController->assign('other_urls', $otherUrls);

$rootController->assign('category', $category);
$rootController->assign('main_categories', $mainCategories);
$rootController->assign('articles', $articles);
$rootController->assign('pages', $pages);
$rootController->assign('page', $page);
$rootController->assign('module', $moduleConfiguration);

$rootController->assign('path', $path);
$rootController->setPageTitle(TITLE_PREFIX . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($moduleConfiguration['page_keywords']);
$rootController->setPageDescription($moduleConfiguration['page_description']);

$rootController->displayPage( 'catalogs/main.html');
if (DEBUG_MODE == 1) {
    // krotka statystyka
        //echo "\n".'<!--'."\n";
        echo 'Ilość zapytań MySQL: ' . $root->db->count_queries() . "\n";
        echo 'Czas wykonywania zapytań: ' . $root->db->get_execution_time() . "\n";

        echo 'Czas generowania strony: ' . substr(get_micro_time($page_start), 0, 5) . ' sek.' . "\n";
        //echo '-->';
    }
    //die;





/*
$path[0]['name'] = 'Katalogi';
$path[0]['url'] = '';


$rootController->assign('path', $path);
$rootController->assign('module', $moduleConfiguration);
$rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_PRODUCTS'] . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($moduleConfiguration['page_keywords'] ?? '');
$rootController->setPageDescription($moduleConfiguration['page_description'] ?? '');
$rootController->displayPage( 'catalogs/main.html');
*/