<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\RealisationsAdminController;
use Controllers\OfferAdminController;
use Controllers\ClientAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'pages');

$moduleName = 'realizacje';
$module = new RealisationsAdminController();
$offerController = new OfferAdminController();
$clientsController = new ClientAdminController();

$category_id = null;
if($rootController->hasParameter('category_id'))
    $category_id = $rootController->getRequestParameter('category_id');

if(!function_exists('showAdd')){
    function showAdd($tab = 'content', $new_category = false) {
        global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax, $parentId, $offerController, $clientsController, $category_id;

        $opcje['op_page_title'] = $configController->getOption('op_page_title');
        $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
        $opcje['op_page_description'] = $configController->getOption('op_page_description');
        $mainLang = $configController->getLangMain();
        $clients = $clientsController->getAll();
        $rootController->assign('clients', $clients);
        $scale = $module->getScaleSize();
        $rootController->assign('main_offers', $offerController->createHtmlSelect('category_id'));
        $rootController->assign('offers', $offerController->createHtmlSelect('offer_id', 'id', [], '', '', true));
        $rootController->assign('parent_id', $parentId);
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('category_id', $category_id);
        $rootController->assign('scale', $scale);
        $rootController->assign('opcje', $opcje);
        $rootController->assign('galleries', $gallery->loadGalleriesNames());
        $rootController->assign('menu_selected', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle('Nowy element');

        if($new_category)
            $rootController->displayPage( 'categories/add.html');
        else
            $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add.html');

    }
}

if(!function_exists('showEdit')){
    function showEdit($id, $tab = 'content', $duplicate = false) {

        global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController, $offerController, $clientsController;

        $article = $module->getArticleById($id);

        if ($article) {
            $galleries = $gallery->loadGalleriesNames();
            $description = $module->loadDescriptionById($id);

            $scale = $module->getScaleSize();
            $files = $files->loadFilesAdmin($article['id'], $filesType);
            $articleOfferIds = $module->getRealisationOffers($id);
            $blocks = $module->getBlocksById($id, $filesType, false);

            $blocksIds = [];
            foreach($blocks as $block){
                $blocksIds[] = $block['id'];
            }

            $allBlocks = $blocksController->getUniversalBlocks(true, $blocksIds);
            $clients = $clientsController->getAll();
            $rootController->assign('clients', $clients);
            $rootController->assign('blocks', $blocks);
            $rootController->assign('all_blocks', $allBlocks);
            $rootController->assign('main_offers', $module->createCategorySelect('category_id', 'id', $article['category_id'], '', ''));
            $rootController->assign('offers', $offerController->createHtmlSelect('offer_id', 'id', $articleOfferIds, '', '', true));
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
}

function showArticles() {
    global $module, $page, $rootController, $moduleName, $moduleConfiguration, $logger, $category_id;
    if(!$module instanceof \Interfaces\ControllerInterface){
        $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
        $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
        $rootController->redirectPage('admin/module.php?moduleName=strony');
    }

    $pages = $module->getPagesAdmin();
    $articles = $module->loadArticlesAdmin($pages, $page, $category_id);

    $categories = $module->getCategoryController()->getByPid($category_id);

    if($category_id){
        $category = $module->getCategoryById($category_id);
        $categoryDescription = $module->getCategoryDescription($category_id);
        $rootController->assign('current_cat', $category);
        $rootController->assign('category_description', $categoryDescription);

        if($category['parent_id'])
            $rootController->assign('category_parent', 'module.php?moduleName='.$moduleName.'&category_id='.$category['parent_id']);
        else
            $rootController->assign('category_parent', 'module.php?moduleName='.$moduleName);
    }

    $rootController->assign('categories', $categories);
    $rootController->assign('pages', $pages);
    $rootController->assign('articles', $articles);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('category_id', $category_id);
    $rootController->assign('menu_group', $moduleConfiguration['module_group']);
    $rootController->setPageTitle( $moduleConfiguration['name'] ?? '');
    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/list.html');
}