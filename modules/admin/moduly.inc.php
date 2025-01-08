<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FilesAdminController;
use Controllers\NewsAdminController;
use Controllers\GalleryAdminController;
use Controllers\ModuleAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('modules_administration');

$moduleName = 'moduly'; // nazwa modulu, tabela w bazie, link w adresie
$module = new ModuleAdminController();
$rootController->assign('menu_group', 'settings');

function showAdd($tab = 'content') {
    global $rootController, $moduleName, $tab;

    $opcje['op_page_title'] = 1;
    $opcje['op_page_keywords'] = 1;
    $opcje['op_page_description'] = 1;

    $rootController->assign('opcje', $opcje);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('tab', $tab);
    $rootController->setPageTitle($GLOBALS['NOWY_MODUL'] ?? "Nowy moduł");
    $rootController->displayPage('modules/add.html');
}

function showEdit($id, $tab = 'content') {
    global $module, $rootController, $moduleName, $configController;

    $article = $module->getArticleById($id);

    if ($article) {
        $description = $module->loadDescriptionById($id);
        $mainLang = $configController->getLangMain();
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('opis', $description);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->assign('item', $article);
        $rootController->assign('menu_selected', $moduleName);
        $rootController->assign('tab', $tab);
        $rootController->setPageTitle($GLOBALS['EDYCJA_MODULU'] ?? "Edycja modułu");
        $rootController->displayPage('modules/edit.html');
    } else {
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
    }
}

function showArticles() {
    global $module, $moduleName, $page, $rootController;
    $pages = $module->getPagesAdmin();
    $articles = $module->loadArticlesAdmin($pages, $page);

    $rootController->assign('pages', $pages);
    $rootController->assign('articles', $articles);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->setPageTitle($GLOBALS['ZARZADZANIE_MODULAMI'] ?? 'Zarządzanie modułami');
    $rootController->displayPage( 'modules/list.html');
}

?>