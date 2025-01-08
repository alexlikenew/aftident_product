<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\InspirationsAdminController;
use Controllers\ProductAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'pages');

$moduleName = 'inspiracje';
$module = new InspirationSAdminController();
$productController = new ProductAdminController();


function showEdit($id, $tab = 'content', $duplicate = false) {

    global $module, $gallery, $productController, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController;

    $article = $module->getArticleById($id);

    if ($article) {
        $galleries = $gallery->loadGalleriesNames();
        $description = $module->loadDescriptionById($id);
        $article['products'] = json_decode($article['products'], true);

        $scale = $module->getScaleSize();
        $files = $files->loadFilesAdmin($article['id'], $filesType);

        $blocks = $module->getBlocksById($id, $filesType, false);
        $allProducts = $productController->getAll();
        $rootController->assign('products', $allProducts);

        $blocksIds = [];
        foreach($blocks as $block){
            $blocksIds[] = $block['id'];
        }

        $allBlocks = $blocksController->getUniversalBlocks(true, $blocksIds);

        $rootController->assign('blocks', $blocks);
        $rootController->assign('all_blocks', $allBlocks);

        $mainLang = $configController->getLangMain();
        $rootController->assign('duplicate', $duplicate);
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
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle('Edycja');
        if($ajax){
            $rootController->display( $moduleConfiguration['templates_dir'].'/edit.html');
            die();
        }

        $rootController->displayPage( $moduleConfiguration['templates_dir'].'/edit.html');
    } else {
        $rootController->redirectPage('module.php?moduleName='.$moduleName);
    }
}