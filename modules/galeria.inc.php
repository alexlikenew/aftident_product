<?php


if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

if (!BASE_URL)
    redirect301('/');
else
    redirect301(BASE_URL);
die;

use Controllers\GalleryController;
use Controllers\ModuleController;

$moduleName = 'galeria';

$module = new GalleryController('gallery');

$moduleController = new ModuleController();

$moduleConfiguration = $moduleController->getModuleConf($moduleName);

if (!$moduleConfiguration['active']) {
    if (!BASE_URL)
        redirect301('/');
    else
        redirect301(BASE_URL);
    die;
}

$title_url = '';
if (is_array($rootController->getParams()) && count($rootController->getParams()) > 0) {
    $title_url = $rootController->params[0];
}

$rootController->assign('menu_selected', BASE_URL . '/galeria.html');

$path[0]['name'] = $GLOBALS['_PAGE_GALLERY'];
$path[0]['url'] = BASE_URL . '/galeria';

if (empty($title_url)) {
    if (!$galleries = $cache->getVariable('galleries')) {
        $galleries = $module->loadGalleries();
        $cache->saveVariable($galleries, "galleries");
    }

    $rootController->assign('header', $GLOBALS['_PAGE_GALLERY']);

    $rootController->assign('galleries', $galleries);

    $rootController->assign('path', $path);
    if(TITLE_PREFIX)
        $rootController->setPageTitle(TITLE_PREFIX  . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
    else
        $rootController->setPageTitle( $moduleConfiguration['page_title'] . TITLE_SUFFIX);
    $rootController->setPageKeywords($moduleConfiguration['page_keywords']);
    $rootController->setPageDescription($moduleConfiguration['page_description']);
    $rootController->displayPage($moduleName . '/list.html');
} else {
    if (!$gallery = $cache->getVariable('gallery')) {
        $gallery = $module->loadGallery($title_url);
        $cache->saveVariable($gallery, "gallery");
    }

    if ($gallery['active'] != 1) {
        $rootController->displayError($GLOBALS['_PAGE_UPDATING']);
        die;
    }

    $module->countArticle($gallery['id']); //zlicza odwiedziny galerii

    if (!$photos = $cache->getVariable('photos')) {
        $photos = $module->getPhotos($gallery['id'], $SEOCONF['page_keywords']);
        $cache->saveVariable($photos, "photos");
    }

    $rootController->assign('gallery', $gallery);
    $rootController->assign('photos', $photos);

    if ($gallery['comments'] == 1) {
        //require_once ROOT_PATH . '/includes/comments.class.php';

        $comments_conf['group'] = Comments::GALLERIES;
        $comments_conf['parent_id'] = $gallery['id'];
        $comments_conf['reload_url'] = $gallery['url'];

        //require_once ROOT_PATH . '/includes/comments.inc.php';
    }
    $path[1]['name'] = $gallery['title'];
    $path[1]['url'] = $gallery['url'];

    $rootController->assign('header', $gallery['title']);

    $rootController->assign('path', $path);
    $rootController->setPageTitle($gallery['page_title']);
    $rootController->setPageKeywords($gallery['page_keywords']);
    $rootController->setPageDescription($gallery['page_description']);
    $rootController->displayPage($moduleName . '/show.html');
}
?>