<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\RegisterAdminController;

$user->CheckPrivileges('stat_administration');


$module = new RegisterAdminController();
$moduleName = 'rejestr-zmian';
$rootController->assign('menu_group', 'Ustawienia');

function showArticles(){
    global $rootController, $module, $moduleName, $page, $moduleConfiguration;
    $pages = $module->getPagesAdmin();
    $items = $module->loadArticlesAdmin($pages, $page);
    $rootController->assign('items', $items);
    $rootController->assign('pages', $pages);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'settings');

    $rootController->setPageTitle($GLOBALS['REJESTR_ZMIAN'] ?? 'Rejestr');
    $rootController->displayPage('register/list.html');
}

?>