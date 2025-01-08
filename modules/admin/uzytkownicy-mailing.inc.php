<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');
$modul = 'uzytkownicy-mailing';

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'save':
        $root->conf->UpdateExtra($root->request->post->get('id'), $root->request->post->get('mail_value'));
        $root->messages->setInfo($GLOBALS['ZMIANY_ZOSTALY_ZAPISANE']);
        $root->redirectPage($modul . '.php');
        break;

    case 'edit':
        showEditForm($root->request->get->get('id'));
        break;

    default:
        showMenu();
        break;
}

function showMenu() {
    global $root;

    $mail_tpl = $root->conf->LoadAllDescriptionExtra('mail_%');
    foreach ($mail_tpl as $key => $value) {
        $matches = array();
        preg_match('/<b>(.*?)<\/b>/i', $value['description'], $matches);
        $mail_tpl[$key]['title'] = $matches[0];
    }

    $root->tpl->assign('mail_tpl', $mail_tpl);
    $root->tpl->assign('menu_selected', 'uzytkownicy_mailing');
    $root->tpl->setPageTitle($GLOBALS['ZARZADZANIE_SZABLONAMI_BIULETYNU']);
    $root->tpl->displayPage('mailing/pokaz.html');
}

function showEditForm($id) {
    global $root;

    $option = $root->conf->LoadOptionWithDescriptionExtra($id);

    $root->tpl->assign('mail_value', htmlspecialchars($option['value']));
    $root->tpl->assign('description', $option['description']);
    $root->tpl->assign('menu_selected', 'uzytkownicy_mailing');
    $root->tpl->setPageTitle($GLOBALS['EDYCJA_SZABLONU_BIULETYNU']);
    $root->tpl->displayPage('mailing/edytuj.html');
}

?>