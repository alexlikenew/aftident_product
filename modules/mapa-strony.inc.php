<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ModuleController;
use Controllers\MenuController;
use Controllers\NewsController;
use Controllers\FaqController;
use Controllers\GalleryController;



$search = '';

$search = $rootController->getRequestParameter('keyword');

if($search){
    $result = $rootController->getSearchResult($search);
    echo json_encode([
        'status'    => 'success',
        'data'      => $result
    ]);
    die;
}


$rootController->assign('menu_selected', BASE_URL . '/'.$moduleConfiguration['title_url']);

$path[0]['name'] = $moduleConfiguration['title'];
$path[0]['url'] = BASE_URL . '/'.$moduleConfiguration['title_url'];

$sitemap = $rootController->getSitemapData();

$rootController->assign('menuBlack', 1);
$rootController->assign('path', $path);
$rootController->assign('module', $moduleConfiguration);
$rootController->assign('sitemap', $sitemap);
$rootController->setPageTitle(TITLE);
$rootController->setPageKeywords(KEYWORDS);
$rootController->setPageDescription(DESCRIPTION);
$rootController->displayPage('mapa-strony.html');
?>
