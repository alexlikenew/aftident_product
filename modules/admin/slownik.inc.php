<?php
if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\DictionaryAdminController;

$user->CheckPrivileges('page_config');

$module = new DictionaryAdminController('slownik');

$moduleName = 'slownik';

switch ($action) {
    case 'save_all':
        $module->saveAll($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        die;
    case 'check_label':
        showCheckLabel($rootController->post->get('label'));
        die;

    case 'make':
        $module->makeSQL();
        die();

    case 'read':
        $module->readSQL();
        die();

    case 'import_form':
        showImport();
        die;

    case 'import':
        $module->importExcel($rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        die;

    case 'export':
        $module->ExportExcel();
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        die;
}

function showAdd() {
    global $rootController, $moduleName, $LANG_MAIN;

    $rootController->assign('module_name', $moduleName);
    $rootController->assign('lang_main', $LANG_MAIN);
    $rootController->assign('menu_group', 'settings');
    $rootController->assign('menu_selected', 'slownik');
    $rootController->setPageTitle('Zarządzanie słownikiem');
    $rootController->displayPage('dictionary/add.html');
}

function showArticles() {
    global $module, $page, $rootController, $LANG_MAIN, $moduleName;

    $pages = $module->countArticlesAdmin();
    $result = $module->loadArticlesAdmin($pages, $page);

    $rootController->assign('lang_main', $LANG_MAIN);
    $rootController->assign('pages', $pages);
    $rootController->assign('page', $page);
    $rootController->assign('query_string', '');
    $rootController->assign('articles', $result);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'settings');
    $rootController->setPageTitle('Zarządzanie słownikiem');
    $rootController->displayPage('dictionary/list.html');
}

function showSearch($keyword) {
    global $module, $page, $rootController, $LANG_MAIN, $moduleName;

    $result = $module->searchIds($keyword['keywords'], $page);

    $rootController->assign('pages', 1);
    $rootController->assign('page', $page);
    if (!empty($keyword)) {
        $rootController->assign('query_string', '&action=szukaj&keyword=' . $keyword);
    } else {
        $rootController->assign('query_string', '');
    }
    $rootController->assign('lang_main', $LANG_MAIN);
    $rootController->assign('keyword', $keyword);
    $rootController->assign('articles', $result);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'settings');
    $rootController->setPageTitle('Zarządzanie słownikiem');
    $rootController->displayPage('dictionary/list.html');
}

function showImport() {
    global $rootController;

    $rootController->assign('menu_selected', 'slownik');
    $rootController->setPageTitle($GLOBALS['IMPORT_SLOWNIKA'] ?? 'Import Słownika');
    $rootController->displayPage('dictionary/import.html');
}

function showCheckLabel($label) {
    global $module;
    $result = $module->checkLabel($label);

    echo $result;
}

?>
