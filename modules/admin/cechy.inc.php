<?php
if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FeaturesAdminController;
use Controllers\ProductAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'offer');

$moduleName = 'cechy';
$module = new FeaturesAdminController();
$featureId = $rootController->getRequestParameter('feature_id');

switch($action){
    case 'add_value':
        showAddValue($featureId);
        die;

    case 'edit_value':
        showEditValue($rootController->get->get('id'));
        die;
    case 'create_value':
        $module->createValue($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&feature_id='.$featureId);
        die;
    case 'update_value':
        $module->updateValue($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&feature_id='.$featureId);
        die;
    case 'set_active':
        if ($rootController->get->has('id')) {
            if($rootController->get->has('value_id'))
                $module->setValueActive($rootController->get->get('value_id'), true);
            else
                $module->setActive($rootController->get->get('id'), true);
            echo json_encode(['status'  => 'success']);
        }
        die();
    //$rootController->redirectPage('module.php?moduleName=' . $moduleName);
    //break;

    case 'set_inactive':
        if ($rootController->get->has('id')) {
            if($rootController->get->has('value_id'))
                $module->setValueActive($rootController->get->get('value_id'), false);
            else
                $module->setActive($rootController->get->get('id'), false);
            echo json_encode(['status'  => 'success']);
        }
        die();
    case 'delete_value':
        if ($rootController->get->has('id'))
            $module->deleteValue($rootController->get->get('id'));

        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&feature_id='.$featureId);
        die;



}

function showArticles(){
    global $module, $page, $rootController, $moduleName, $moduleConfiguration, $logger, $featureId;

    $pages = $module->getPagesAdmin();

    $articles = $module->getByFeatureId($featureId, $pages, $page, forAdmin: true);

    $rootController->assign('articles', $articles);
    if($featureId)
        $rootController->assign('feature_id', $featureId);

    $rootController->assign('page', $page);
    $rootController->assign('pages', $pages);
    $rootController->setPageTitle('Zarządzanie cechami');
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('menu_group', 'offer');
    $rootController->assign('module_name', $moduleName);
    $rootController->displayPage('features/list.html');
}

function showAdd($tab = 'content') {
    global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax;

    $categoriesController = (new ProductAdminController())->getCategoryController();
    $opcje['op_page_title'] = $configController->getOption('op_page_title');
    $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    $opcje['op_page_description'] = $configController->getOption('op_page_description');
    $mainLang = $configController->getLangMain();

    $categories = $categoriesController->getAll(true);
    $rootController->assign('categories', $categories);
    $scale = $module->getScaleSize();
    $rootController->assign('lang_main', $mainLang);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('scale', $scale);
    $rootController->assign('opcje', $opcje);
    $rootController->assign('galleries', $gallery->loadGalleriesNames());
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('tab_selected', $tab);
    $rootController->setPageTitle('Nowy element');

    $rootController->displayPage( 'features/add.html');
}

function showAddValue($tab = 'content') {
    global $configController, $rootController, $moduleName, $moduleConfiguration, $featureId, $ajax;
    //$categoriesController = (new ProductAdminController())->getCategoryController();
    $mainLang = $configController->getLangMain();

    //$categories = $categoriesController->getAll(true);
    $rootController->assign('value_edit', true);
    //$rootController->assign('categories', $categories);
    $rootController->assign('feature_id', $featureId);
    $rootController->assign('lang_main', $mainLang);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('menu_group', 'offer');
    $rootController->assign('tab_selected', $tab);
    $rootController->setPageTitle('Nowa wartość');

    $rootController->displayPage( 'features/add-value.html');
}

function showEditValue($id, $tab = 'content', $duplicate = false) {
    global $module, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax;

    $article = $module->getFeatureValue($id);

    if ($article) {
        $description = $module->loadValueDescriptionById($id);


        $mainLang = $configController->getLangMain();
        $rootController->assign('value_edit', true);
        $rootController->assign('duplicate', $duplicate);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('item', $article);
        $rootController->assign('opis', $description);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle($GLOBALS['EDYCJA_AKTUALNOSCI'] ?? 'Edycja');
        if($ajax){
            $rootController->display( $moduleConfiguration['templates_dir'].'/edit-value.html');
            die();
        }

        $rootController->displayPage( 'features/edit.html');
    } else {
        $rootController->redirectPage('module.php?moduleName='.$moduleName);
    }
}

if(!function_exists('showEdit')){
    function showEdit($id, $tab = 'content', $duplicate = false) {
        global $module, $gallery, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax ;
        $categoriesController = (new ProductAdminController())->getCategoryController();
        $article = $module->getArticleById($id);

        if ($article) {
            $galleries = $gallery->loadGalleriesNames();
            $description = $module->loadDescriptionById($id);
            $categories = $categoriesController->getAll(true);

            $mainLang = $configController->getLangMain();
            $rootController->assign('categories', $categories);
            $rootController->assign('duplicate', $duplicate);
            $rootController->assign('module_name', $moduleName);
            $rootController->assign('lang_main', $mainLang);
            $rootController->assign('item', $article);
            $rootController->assign('opis', $description);
            $rootController->assign('galleries', $galleries);
            $rootController->assign('module_name', $moduleName);
            $rootController->assign('menu_group', 'offer');
            $rootController->assign('tab_selected', $tab);
            $rootController->setPageTitle('Edycja');
            if($ajax){
                $rootController->display( $moduleConfiguration['templates_dir'].'/edit.html');
                die();
            }

            $rootController->displayPage( 'features/edit.html');
        } else {
            $rootController->redirectPage('module.php?moduleName='.$moduleName);
        }
    }
}