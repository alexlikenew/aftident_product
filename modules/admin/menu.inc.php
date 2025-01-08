<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ModuleController;
use Controllers\PagesController;
//use Controllers\OfferController;
use Controllers\MenuAdminController;
use Controllers\ProductAdminController;
use Controllers\CatalogAdminController;

use Models\Menu;

$user->CheckPrivileges('menu_administration');

$module = new MenuAdminController();

$pagesController = new PagesController();
$modules = new ModuleController();
//$offers = new OfferController();
$productController = new ProductAdminController();
$catalogController = new CatalogAdminController();
$moduleName = 'menu';
//$rootController->generateSitemap();
$mm = [];
$mm[Menu::GROUP_TOP]['name'] = 'menu górne';
$mm[Menu::GROUP_TOP]['group'] = Menu::GROUP_TOP;
$mm[Menu::GROUP_LEFT]['name'] = 'menu główne';
$mm[Menu::GROUP_LEFT]['group'] = Menu::GROUP_LEFT;
$mm[Menu::GROUP_BOTTOM]['name'] = 'menu dolne';
$mm[Menu::GROUP_BOTTOM]['group'] = Menu::GROUP_BOTTOM;

$grupa = Menu::GROUP_TOP;
if ($rootController->hasParameter('group')) {
    $grupa = $rootController->getRequestParameter('group');
}
$mm_sel = $mm[$grupa];

$pid = 0;
if ($rootController->hasParameter('pid')) {
    $pid = $rootController->getRequestParameter('pid');
}

switch ($action) {
    case 'move':
        if ($rootController->post->has('id')) {
            if($module->move($rootController->post->get('id'), $rootController->post->get('order')))
                echo json_encode([
                    'status'    => 'SUCCESS'
                ]);
        }else{
            echo json_encode([
                'status'    => 'ERROR'
            ]);
        }
        die();

    case 'change_menu':
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&pid=' . $pid . '&group=' . $grupa);
        die;

    case 'show_tree':
        showTree();
        die;
    case 'Zapisz':
        try{

            if ($module->update($rootController->post->getAll())) {

                $rootController->redirectPage('module.php?moduleName='.$moduleName.'&pid='.$rootController->post->get('pid').'&group='.$rootController->post->get('group'));
            } else {
                $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id'));
            }
            die;
        }catch(Exception $e){
            dd($e->getMessage());
        }
}

function showTree() {
    global $module, $mm, $rootController;

    $menu_tree = $module->loadTree($mm);
    $types = $module->getTypes();

    $rootController->assign('mm', $mm);
    $rootController->assign('map', $menu_tree);
    $rootController->assign('types', $types);
    $rootController->assign('menu_group', 'Zarządzanie stroną');

    $rootController->display('menu/tree.html');
    die();
}

function showAdd() {
    global $module, $pagesController, $modules, $languages, $mm_sel, $pid, $depth, $rootController, $moduleName, $productController;
    $config = $rootController->loadConfigController();
    $mainLang = $config->getLangMain();
    $path = $module->loadPathTitle($pid);

    $types = $module->getTypes();

    $pages = array();
    foreach ($languages as $item) {
        $pages[$item['id']] = $pagesController->getNames($item['id']);
    }



    $category_select = $productController->getCategoryController()->createHtmlSelect('category_id', 'id', 0, '');
    $rootController->assign('category_select', $category_select);

    $modulesList = $modules->loadNames();
//    $offersList = $offers->getItems(0, 0);

    $rootController->assign('lang_main', $mainLang);
    $rootController->assign('mm_sel', $mm_sel);
    $rootController->assign('pid', $pid);
    $rootController->assign('depth', $depth);
    $rootController->assign('path', $path);
    $rootController->assign('pages', $pages);
    $rootController->assign('modules', $modulesList);
//    $rootController->assign('offers', $offersList);
    $rootController->assign('types', $types);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'pages');
    $rootController->setPageTitle('Zarządzanie menu');
    $rootController->displayPage($moduleName . '/add.html');
}

function showEdit($id) {
    global $module, $pagesController, $modules, $languages, $mm_sel, $rootController, $moduleName, $productController, $catalogController;
    $config = $rootController->loadConfigController();
    $mainLang = $config->getLangMain();

    $article = $module->getArticleById($id);

    $opis = $module->getItemDescription($id);

    $types = $module->getTypes();

    $pages = [];
    $catalog_category_select = [];
    $category_select = [];
    foreach ($languages as $item) {
        $pages[$item['id']] = $pagesController->getNames($item['id']);
        $catalog_category_select[$item['id']] = $catalogController->getCategoryController()->createHtmlSelect('catalog_category_id', 'id', $opis[$item['id']]['target_id'], '');
        $category_select[$item['id']] = $productController->getCategoryController()->createHtmlSelect('category_id', 'id', $opis[$item['id']]['target_id'], '');

    }

    $rootController->assign('catalog_category_select', $catalog_category_select);
    $rootController->assign('category_select', $category_select);

    $modulesList = $modules->loadNames();
    $rootController->assign('lang_main', $mainLang);
    $rootController->assign('mm_sel', $mm_sel);
    $rootController->assign('pages', $pages);
    $rootController->assign('modules', $modulesList);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('opis', $opis);
    $rootController->assign('item', $article);
    $rootController->assign('types', $types);
    $rootController->assign('menu_group', 'pages');
    $rootController->setPageTitle('Edycja');
    $rootController->displayPage('menu/edit.html');
}


function showArticles() {

    global $module, $pagesController, $modules, $languages, $pid, $mm_sel, $mm, $rootController, $moduleName;
    $pID = empty($pid) ? 0 : $pid;

    $menu = $module->load($pID, $mm_sel['group'], '', false);

    $types = $module->getTypes();
    $pages = array();
    foreach ($languages as $item) {
        $pages[$item['id']] = $pagesController->getNames($item['id']);
    }
    $modulesList = $modules->loadNames();

    $path = $module->loadPath($pID, $mm_sel['group']);
    $upPID = ($pID != 0) ? $module->getPid($pID) : 'none';

    $rootController->assign('mm', $mm);
    $rootController->assign('mm_sel', $mm_sel);
    $rootController->assign('pid', $pID);
    $rootController->assign('upperPID', $upPID);
    $rootController->assign('path', $path);
    $rootController->assign('menu', $menu);
    $rootController->assign('types', $types);
    $rootController->assign('pages', $pages);
    $rootController->assign('modules', $modulesList);

    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'pages');
    $rootController->setPageTitle('Zarządzanie menu');
    $rootController->displayPage('menu/list.html');
}

?>