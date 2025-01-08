<?php


if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/slownik_adminAdmin.class.php';

$oSlownik = new Slownik_Admin_Admin($root);
$modul = 'slownik_admin';

$page = 1;
if ($root->request->hasParameter('page')) {
    $page = $root->request->getParameter('page');
}

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

$keyword = '';
if ($root->request->hasParameter('keyword')) {
    $keyword = $root->request->getParameter('keyword');
}

switch ($action) {
    case 'save_all':
        $oSlownik->SaveAll($root->request->post->getAll());
        $root->redirectPage($modul . '.php');
        break;

    case 'delete':
        $oSlownik->Delete($root->request->get->get('id'));
        $root->redirectPage($modul . '.php');
        break;

    case 'add':
        showAdd();
        break;

    case 'save':
        $oSlownik->Save($root->request->post->getAll());
        $root->redirectPage($modul . '.php');
        break;

    case 'save_new':
        $oSlownik->Add($root->request->post->getAll());
        $root->redirectPage($modul . '.php');
        break;

    case 'szukaj':
        showSzukajWyniki($keyword);
        break;

    case 'check_label':
        showCheckLabel($root->request->post->get('label'));
        break;

    case 'make':
        $oSlownik->MakeSQL();
        die();
        break;

    case 'read':
        $oSlownik->ReadSQL();
        die();
        break;

    case 'import_form':
        showImport();
        break;

    case 'import':
        $oSlownik->ImportExcel($root->request->files->getAll());
        $root->redirectPage($modul . '.php');
        break;

    case 'export':
        $oSlownik->ExportExcel();
        $root->redirectPage($modul . '.php');
        break;

    default:
        showSlownik();
        break;
}

function showAdd() {
    global $tpl;

    $tpl->assign('menu_selected', 'slownik_admin');
    $tpl->setPageTitle($GLOBALS['ADMIN_ZARZADZANIE_SLOWNIKIEM_PANELU']);
    $tpl->displayPage('slownik_admin/dodaj.html');
}

function showSlownik() {
    global $oSlownik, $page, $tpl;

    $result = $oSlownik->LoadArticlesAdmin($page);

    $tpl->assign('pages', $oSlownik->getArticlesAdmin());
    $tpl->assign('page', $page);
    $tpl->assign('query_string', '');
    $tpl->assign('articles', $result);
    $tpl->assign('menu_selected', 'slownik_admin');
    $tpl->setPageTitle($GLOBALS['ADMIN_ZARZADZANIE_SLOWNIKIEM_PANELU']);
    $tpl->displayPage('slownik_admin/list.html');
}

function showSzukajWyniki($keyword) {
    global $oSlownik, $page, $tpl;

    $result = $oSlownik->Szukaj($keyword, $page);

    $tpl->assign('pages', $oSlownik->getSzukajPages($keyword));
    $tpl->assign('page', $page);
    if (!empty($keyword)) {
        $tpl->assign('query_string', '&action=szukaj&keyword=' . $keyword);
    } else {
        $tpl->assign('query_string', '');
    }
    $tpl->assign('keyword', $keyword);
    $tpl->assign('articles', $result);
    $tpl->assign('menu_selected', 'slownik_admin');
    $tpl->setPageTitle($GLOBALS['ADMIN_ZARZADZANIE_SLOWNIKIEM_PANELU']);
    $tpl->displayPage('slownik_admin/list.html');
}

function showImport() {
    global $tpl;

    $tpl->assign('menu_selected', 'slownik_admin');
    $tpl->setPageTitle($GLOBALS['ADMIN_IMPORT_SLOWNIKA_PANELU']);
    $tpl->displayPage('slownik_admin/import.html');
}

function showCheckLabel($label) {
    global $oSlownik;

    $result = $oSlownik->checkLabel($label);

    echo $result;
}

?>