<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\PagesAdminController;

$user->CheckPrivileges('strony_administration');
$rootController->assign('menu_group', 'pages');
$module = new PagesAdminController();
$moduleName = 'strony';

switch($action){
    case 'create':

        if ($id = $module->create($rootController->post->getAll(), $rootController->files->getAll())) {
            $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=edit&id='.$id);
        } else {
            showAdd();
        }
        die;
    case 'editMain':
        showEditMain();
        die;
}

function showEdit($id, $tab = 'main', $duplicate = false) {
    global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController;
    $article = $module->getArticleById($id);

    if ($article) {
        $templates = $module->getAllPageTemplates($configController->getOption('default_template'), 'pages');
        $rootController->assign('page_templates', $templates);

        $blocks = $module->getBlocksById($id, $filesType, false);

        $blocksIds = [];
        foreach($blocks as $block){
            $blocksIds[] = $block['id'];
        }

        //$allBlocks = $blocksController->getUniversalBlocks(true, $blocksIds);
        $rootController->assign('blocks', $blocks);
        //$rootController->assign('all_blocks', $allBlocks);

        $galleries = $gallery->loadGalleriesNames();
        $description = $module->loadDescriptionById($id);
        $scale = $module->getScaleSize();
        $files = $files->loadFilesAdmin($article['id'], $filesType);
        $mainLang = $configController->getLangMain();
        $rootController->assign('duplicate', $duplicate);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('main-page', 1);
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('files_type', $filesType);
        $rootController->assign('files', $files);
        $rootController->assign('scale', $scale);
        $rootController->assign('item', $article);
        $rootController->assign('opis', $description);
        $rootController->assign('galleries', $galleries);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('menu_group', $moduleConfiguration['module_group']);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle($GLOBALS['EDYCJA_AKTUALNOSCI'] ?? 'Edycja');
        if($ajax){
            $rootController->display( $moduleConfiguration['templates_dir'].'/edit.html');
            die();
        }

        $rootController->displayPage( $moduleConfiguration['templates_dir'].'/edit.html');
    } else {
        $rootController->redirectPage('module.php?moduleName='.$moduleName);
    }
}

function showEditMain($tab = 'content') {
    global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController;
    $article = $module->getMainPage();

    if ($article) {
        $templates = $module->getAllPageTemplates($configController->getOption('default_template'), 'pages');
        $rootController->assign('page_templates', $templates);

        $blocks = $module->getBlocksById($article['id'], $filesType, false);

        $blocksIds = [];
        foreach($blocks as $block){
            $blocksIds[] = $block['id'];
        }

        //$allBlocks = $blocksController->getUniversalBlocks(true, $blocksIds);
        $rootController->assign('blocks', $blocks);
        //$rootController->assign('all_blocks', $allBlocks);

        $galleries = $gallery->loadGalleriesNames();
        $description = $module->loadDescriptionById($article['id']);
        $scale = $module->getScaleSize();
        $files = $files->loadFilesAdmin($article['id'], $filesType);
        $mainLang = $configController->getLangMain();
        //$rootController->assign('duplicate', $duplicate);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('files_type', $filesType);
        $rootController->assign('files', $files);
        $rootController->assign('scale', $scale);
        $rootController->assign('item', $article);
        $rootController->assign('opis', $description);
        $rootController->assign('galleries', $galleries);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('menu_group', $moduleConfiguration['module_group']);
        $rootController->assign('main_page', 1);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle($GLOBALS['EDYCJA_AKTUALNOSCI'] ?? 'Edycja');
        if($ajax){
            $rootController->display( $moduleConfiguration['templates_dir'].'/edit.html');
            die();
        }

        $rootController->displayPage( $moduleConfiguration['templates_dir'].'/edit.html');
    } else {
        $rootController->redirectPage('module.php?moduleName='.$moduleName);
    }
}


?>