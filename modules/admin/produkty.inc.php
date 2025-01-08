<?php
if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ProductAdminController;
use Controllers\FeaturesAdminController;
use Controllers\PriceStepsAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'pages');

$moduleName = 'produkty';
$module = new ProductAdminController();
$featuresController = new FeaturesAdminController();
$priceStepsController = new PriceStepsAdminController();

switch($action){
    case 'update_price_list':
        $module->updatePriceList($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=edit&id='.$rootController->post->get('parent_id'));
        die;
}

if(!function_exists('showAdd')){
    function showAdd($tab = 'content', $new_category = false) {
        global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax, $parentId,  $category_id, $featuresController;

        $opcje['op_page_title'] = $configController->getOption('op_page_title');
        $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
        $opcje['op_page_description'] = $configController->getOption('op_page_description');
        $mainLang = $configController->getLangMain();
        $scale = $module->getScaleSize();
        $rootController->assign('main_offers', $module->createCategorySelect('category_id', selected: $category_id));
        $rootController->assign('parent_id', $parentId);
        $rootController->assign('path', $module->getCategoryController()->getPathByPid($category_id));
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('scale', $scale);
        $rootController->assign('opcje', $opcje);
        $rootController->assign('galleries', $gallery->loadGalleriesNames());
        $rootController->assign('menu_selected', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->assign('category_id', $category_id);
        $rootController->setPageTitle('Nowy element');
        if($new_category)
            $rootController->displayPage( 'categories/add.html');
        else
            $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add.html');
    }
}

if(!function_exists('showEdit')){
    function showEdit($id, $tab = 'content', $duplicate = false) {

        global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController,  $featuresController, $priceStepsController;

        $article = $module->getArticleById($id);

        if ($article) {
            $galleries = $gallery->loadGalleriesNames();
            $description = $module->loadDescriptionById($id);

            $scale = $module->getScaleSize();
            $files = $files->loadFilesAdmin($article['id'], $filesType);
            $blocks = $module->getBlocksById($id, $filesType, false);
            $allFeatures = $featuresController->getAll();
            $rootController->assign('all_features', $allFeatures);
            $rootController->assign('features', $article['features']);
            $blocksIds = [];
            foreach($blocks as $block){
                $blocksIds[] = $block['id'];
            }

            $priceSteps = $priceStepsController->getAll();

            $rootController->assign('price_steps', $priceSteps);
            $rootController->assign('path', $module->getCategoryController()->getPathByPid($article['category_id']));
            $rootController->assign('main_offers', $module->createCategorySelect('category_id', selected: $article['category_id']));
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

    $categories = $module->getCategoryController()->getByPid($category_id, false);
  
    if($category_id){
        $category = $module->getCategoryById($category_id);
        $categoryDescription = $module->getCategoryDescription($category_id);
        $rootController->assign('current_cat', $category);
        $rootController->assign('category_description', $categoryDescription);
        $rootController->assign('path', $module->getCategoryController()->getPathByPid($category_id));

        if($category['parent_id'])
            $rootController->assign('category_parent', 'module.php?moduleName=produkty&'.$moduleName.'='.$category['parent_id']);
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