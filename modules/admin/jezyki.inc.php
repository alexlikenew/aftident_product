<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('page_config');
$rootController->assign('menu_group', 'settings');

$moduleName = 'jezyki';
$module = $rootController->loadConfigController();

switch($action){
    case 'Zapisz':
        try{
            if ($module->updateLang($rootController->post->getAll())) {
                $rootController->redirectPage('module.php?moduleName='.$moduleName);
            } else {
                $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id'));
            }
            break;
        }catch(Exception $e){
            dd($e->getMessage());
        }
    case 'create':
        if ($module->createLang($rootController->post->getAll())) {
            $rootController->redirectPage('module.php?moduleName='.$moduleName);
        } else {
            showAdd();
        }
        break;
    case 'delete':
        if ($rootController->get->has('id')) {

            $module->deleteLang($rootController->get->get('id'));
        }
        $rootController->redirectPage('module.php?moduleName='.$moduleName);
        break;
    case 'set_active':
        if ($rootController->get->has('id')) {
            $module->setLangActive($rootController->get->get('id'), true);
            echo json_encode(['status'  => 'success']);
        }
        die();
    //$rootController->redirectPage('module.php?moduleName=' . $moduleName);
    //break;

    case 'set_inactive':
        if ($rootController->get->has('id')) {
            $module->setLangActive($rootController->get->get('id'), false);
            echo json_encode(['status'  => 'success']);
        }
        die();

}

function showArticles() {

    global $module, $page, $rootController, $moduleName, $moduleConfiguration, $logger;

    if(!$module instanceof \Interfaces\ControllerInterface){
        $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
        $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
        $rootController->redirectPage('admin/module.php?moduleName=aktualnosci');
    }

    $pages = 1; //$module->getPagesAdmin();

    $articles = $module->getLang(true);

    $rootController->assign('pages', $pages);
    $rootController->assign('articles', $articles);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->setPageTitle($moduleConfiguration['name']?? 'Języki');


    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/list.html');
}

function showEdit($id, $tab = 'main') {
    global $module, $rootController, $moduleName, $moduleConfiguration, $ajax;

    $article = $module->getLangById($id);

    if ($article) {
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('item', $article);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle( 'Edycja języka');
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
    global $rootController, $moduleName, $moduleConfiguration, $ajax;


    $rootController->assign('module_name', $moduleName);

    $rootController->assign('module_name', $moduleName);
    $rootController->assign('tab_selected', $tab);
    $rootController->setPageTitle( 'Dodaj język');
    if($ajax){
        $rootController->display( $moduleConfiguration['templates_dir'].'/add.html');
        die();
    }

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add.html');
}


?>