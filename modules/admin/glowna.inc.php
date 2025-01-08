<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/rejestr.class.php';

$oRejestr = new Rejestr($root);
$modul = 'glowna';

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'save':
        $root->conf->UpdateOptionLang('main_page', $root->request->post->get('main_page'));
        $oRejestr->addWpis('Strona główna', '/', 'zmieniono', 'main');
        $root->messages->setInfo('Zmiany zostały zapisane!');
        $root->redirectPage($modul . '.php');
        break;

    default:
        break;
}

$main_page = $root->conf->LoadOptionLangAdmin('main_page');

$tpl->assign('main_page', $main_page);
$tpl->assign('menu_selected', 'glowna');
$tpl->setPageTitle($GLOBALS['ZAWARTOSC_STRONY_GLOWNEJ']);
$tpl->displayPage('strony/glowna.html');
?>