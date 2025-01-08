<?php

if(!defined('SCRIPT_CHECK')) die('No-Hack here buddy.. ;)');

use Controllers\RedirectsAdminController;

$user -> CheckPrivileges('page_config');
$module = new RedirectsAdminController();
$moduleName='redirects';
$rootController->assign('menu_group', 'settings');

function showArticles()
{
    global $rootController, $module, $moduleName;

    $_GET['page'] = !empty($_POST['page']) ? $_POST['page'] : $_GET['page'];
    $_GET['page'] = empty($_GET['page']) ? 1 : $_GET['page'];
    $pages = $module->getArticlesAdmin();
    $rootController->assign('pages', $pages);
    $rootController->assign('page', $_GET['page'] ?? 1);
    $result = $module->loadArticlesAdmin($pages, $_GET['page'] ?? 1);
    $rootController->assign('articles', $result);

    $rootController->setPageTitle('Zarządzanie przekierowaniami');
    $rootController->assign('module_name', $moduleName);
    $rootController->displayPage('redirects/list.html');
}

?>