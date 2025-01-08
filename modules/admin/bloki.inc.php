<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\BlocksAdminController;
use Controllers\PagesController;
use Controllers\OfferController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'pages');

$type_id = $rootController->getRequestParameter('type_id');



$moduleName = 'bloki';
$module = new BlocksAdminController();
$pageController = new PagesController();
$offerController = new OfferController();

switch($action){
    case 'type_list':
        showArticles();
        die;
    case 'type_add':
        showTypeAdd();
        die;
    case 'type_edit':

        if($rootController->hasParameter('id'))
            showTypeEdit($rootController->getRequestParameter('id'));
        else
            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=type_list');
        die;
    case 'create_type':
        $module->createType($rootController->post->getAll(), $rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=type_list');
        die();
    case 'update_type':
        $module->updateType($rootController->post->getAll(), $rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=type_list');
        die();
    case 'delete_type':

        if(!$module->typeHasItems($rootController->getRequestParameter('id')))
            $module->deleteType($rootController->getRequestParameter('id'));
        else{
            echo json_encode([
                'status'    => 'error',
                'message'   => 'Istnieją bloki tego typu!'
            ]);
            die;
        }


        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=type_list');
        die();
    case 'delete_type_photo':
        $module->deleteTypePhoto($rootController->get->get('id'));
        $url = 'module.php?moduleName='. $moduleName . '&action=type_edit&id=' . $rootController->get->get('id') ;
        if($rootController->get->get('item_module'))
            $url .= '&item_module='.$rootController->get->get('item_module').'&module_id='.$rootController->get->get('module_id').'&item_id='.$rootController->get->get('item_id');
        $rootController->redirectPage($url);
        die;
    case 'change_block_type':
        $result = $module->changeBlockType($rootController->post->get('id'), $rootController->post->get('type_id'));

        if($result){
            echo json_encode([
                'status'    => 'success'
            ]);

        }
        else
            echo json_encode([
                'status'    => 'error'
            ]);
        die;
    case 'show_items':
        showItems($rootController->getRequestParameter('id'));
        die;
    case 'show_main':
        showTemplates();
        die;
    case 'create':
        if ($id = $module->create($rootController->post->getAll(), $rootController->files->getAll())) {
            if ($rootController->post->get('item_module')) {
                $rootController->redirectPage('module.php?moduleName=bloki&action=edit&id=' .$id.'&item_id='.$rootController->post->get('item_id').'&module_id='.$rootController->post->get('module_id').'&item_module='.$rootController->post->get('item_module'));
            } else
                $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=show_main');
        }
        else {
            showAdd();
        }
        die;
}


function showItems($id){
    global $module, $page, $rootController, $moduleName, $type_id, $moduleConfiguration, $logger;

    if(!$module instanceof \Interfaces\ControllerInterface){
        $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
        $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
        $rootController->redirectPage('admin/module.php?moduleName=aktualnosci');
    }

    $articles = $module->getChildrenById($id);

}
/*
function showArticles() {

    global $module, $page, $rootController, $moduleName, $type_id, $moduleConfiguration, $logger;

    if(!$module instanceof \Interfaces\ControllerInterface){
        $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
        $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
        $rootController->redirectPage('admin/module.php?moduleName=aktualnosci');
    }

    $pages = $module->getPagesAdmin();
    $types = $module->loadTypes('universal');
    $articles = $module->loadArticlesAdmin($pages, $page, true, $type_id, true);

    if($type_id)
        $rootController->assign('type_id', $type_id);

    $rootController->assign('types', $types);
    $rootController->assign('pages', $pages);
    $rootController->assign('articles', $articles);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', $moduleConfiguration['module_group']);
    $rootController->setPageTitle( $moduleConfiguration['name']);


    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/list.html');
}
*/
function showEdit($id, $tab = 'content', $duplicate = false) {
    global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $pageController, $languages, $offerController, $moduleController, $moduleConfiguration, $ajax;

    $article = $module->getArticleById($id);

    if ($article) {

        $galleries = $gallery->loadGalleriesNames();
        $description = $module->loadDescriptionById($id);
        //$types = $module->loadTypes();
        //$rootController->assign('types', $types);
        $types = $module->getLinkTypes();
        $rootController->assign('types', $types);
        $scale = $module->getScaleSize();
        $files = $files->loadFilesAdmin($article['id'], $filesType);
        $mainLang = $configController->getLangMain();

        if($rootController->hasParameter('item_module')){
            $rootController->assign('item_module', $rootController->getRequestParameter('item_module'));
            $rootController->assign('item_id', $rootController->getRequestParameter('item_id'));
            $rootController->assign('module_id', $rootController->getRequestParameter('module_id'));
        }

        if($article['parent_id']){
            $parentBlock = $module->getBlockById($article['parent_id']);
            $rootController->assign('parent_block', $parentBlock);
        }

        $pages = array();
        foreach ($languages as $item) {
            $pages[$item['id']] = $pageController->getNames($item['id']);
        }
        $rootController->assign('pages', $pages);
        $modulesList = $moduleController->loadNames();
        $offersList = $offerController->getAll();

        $rootController->assign('modules', $modulesList);
        $rootController->assign('offers', $offersList);

        $rootController->assign('duplicate', $duplicate);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('files_type', $filesType);
        $rootController->assign('files', $files);
        $rootController->assign('scale', $scale);
        $rootController->assign('item', $article);
        $rootController->assign('opis', $description);
        $rootController->assign('galleries', $galleries);
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

function showAdd($tab = 'content') {
    global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax;

    $opcje['op_page_title'] = $configController->getOption('op_page_title');
    $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    $opcje['op_page_description'] = $configController->getOption('op_page_description');
    $mainLang = $configController->getLangMain();


    $scale = $module->getScaleSize();
    if($rootController->hasParameter('item_module')){
        $rootController->assign('item_module', $rootController->getRequestParameter('item_module'));
        $rootController->assign('item_id', $rootController->getRequestParameter('item_id'));
        $rootController->assign('module_id', $rootController->getRequestParameter('module_id'));
        $types = $module->loadTypes($rootController->getRequestParameter('module_id'));
        //$parentBlocks = $module->getParentsByIds($rootController->getRequestParameter('module_id'), $rootController->getRequestParameter('item_id'));
        //$rootController->assign('parent_blocks', $parentBlocks);
    }
    else
        $types = $module->loadTypes('universal');


    $parentBlocks = $module->getParentsByIds($rootController->getRequestParameter('module_id'), $rootController->getRequestParameter('item_id'));
    $rootController->assign('parent_blocks', $parentBlocks);

    $rootController->assign('types', $types);
    $rootController->assign('lang_main', $mainLang);

    $rootController->assign('module_name', $moduleName);
    $rootController->assign('scale', $scale);
    $rootController->assign('opcje', $opcje);
    $rootController->assign('galleries', $gallery->loadGalleriesNames());
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('tab_selected', $tab);
    $rootController->setPageTitle('Nowy element');

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add.html');
}

function showArticles() {

    global $module, $page, $rootController, $moduleName, $languages, $moduleConfiguration, $moduleController, $logger;

    if(!$module instanceof \Interfaces\ControllerInterface){
        $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
        $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
        $rootController->redirectPage('admin/module.php?moduleName=aktualnosci');
    }

    $pages = $module->getPagesAdmin($page);
    $articles = $module->loadTypes();

    $modulesData = $moduleController->loadNames();
    $modulesList = [];
    foreach($modulesData as $value){
        $modulesList[$value['id']] = $value;
    }
    $rootController->assign('modules', $modulesList);

    $blocksQuantity = $module->getQuantityByType();
    $rootController->assign('quantity', $blocksQuantity);
    $rootController->assign('pages', $pages);
    $rootController->assign('articles', $articles);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', $moduleConfiguration['module_group']);
    $rootController->setPageTitle( $moduleConfiguration['name']);


    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/types-list.html');
}

function showTypeEdit($id, $tab = 'main', $duplicate = false) {
    global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $pageController, $languages, $moduleConfiguration, $ajax;

    $article = $module->getTypeById($id);

    if ($article) {
        $templates = $module->getAllPageTemplates($configController->getOption('default_template'), 'blocks');
        $rootController->assign('page_templates', $templates);
        $mainLang = $configController->getLangMain();
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('files_type', $filesType);
        $rootController->assign('files', $files);
        $rootController->assign('item', $article);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle($GLOBALS['EDYCJA_AKTUALNOSCI'] ?? 'Edycja');
        if($ajax){
            $rootController->display( $moduleConfiguration['templates_dir'].'/edit.html');
            die();
        }
        $rootController->displayPage( $moduleConfiguration['templates_dir'].'/type_edit.html');
    } else {
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=type_list');
    }
}

function showTypeAdd($tab = 'content') {
    global $module, $gallery, $configController, $rootController, $moduleName, $moduleController, $pageController, $languages, $moduleConfiguration, $ajax;

    //$opcje['op_page_title'] = $configController->getOption('op_page_title');
    //$opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    //$opcje['op_page_description'] = $configController->getOption('op_page_description');
    $mainLang = $configController->getLangMain();

    $modulesList = $moduleController->loadNames();
    $rootController->assign('modules', $modulesList);
    $templates = $module->getAllPageTemplates($configController->getOption('default_template'), 'blocks');
    $rootController->assign('page_templates', $templates);

    $rootController->assign('lang_main', $mainLang);

    $rootController->assign('module_name', $moduleName);

    //$rootController->assign('opcje', $opcje);
    //$rootController->assign('galleries', $gallery->loadGalleriesNames());
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('tab_selected', $tab);
    $rootController->setPageTitle('Nowy typ');

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/type_add.html');
}

?>
