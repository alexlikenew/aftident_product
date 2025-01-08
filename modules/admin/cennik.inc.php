<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\PriceListAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'offer');

$moduleName = 'cennik';
$module = new PriceListAdminController();


function showAdd($tab = 'content') {
    global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax;

    $opcje['op_page_title'] = $configController->getOption('op_page_title');
    $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    $opcje['op_page_description'] = $configController->getOption('op_page_description');
    $mainLang = $configController->getLangMain();

    $rootController->assign('lang_main', $mainLang);

    $rootController->assign('module_name', $moduleName);
    $rootController->assign('opcje', $opcje);
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('tab_selected', $tab);
    $rootController->setPageTitle('Nowy element');

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add.html');
}

function showArticles() {

    global $module, $page, $rootController, $moduleName, $moduleConfiguration, $logger, $parentId;

    if(!$module instanceof \Interfaces\ControllerInterface){
        $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
        $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
        $rootController->redirectPage('admin/module.php?moduleName=strony');
    }

    if($parentId)
        $rootController->assign('path', $module->getPathByPid($parentId));

    $pages = $module->getPagesAdmin();

    $articles = $module->loadArticlesAdmin($pages, $page);

    $rootController->assign('pages', $pages);
    $rootController->assign('articles', $articles);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'offer');
    $rootController->setPageTitle( $moduleConfiguration['name'] ?? '');

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/list.html');
}