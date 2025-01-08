<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('stat_administration');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/pagesAdmin.class.php';

$oPagesAdmin = new PagesAdmin($root);

$page = 1;
if ($root->request->hasParameter('page')) {
    $page = $root->request->getParameter('page');
}

$limit = 50;

$tpl->assign('wyniki', $oPagesAdmin->LoadWyniki($limit, $page));
$tpl->assign('pages', $oPagesAdmin->GetPagesSearch());
$tpl->assign('page', $page);
$tpl->assign('interval', $limit * ($page - 1));
$tpl->assign('menu_selected', 'wyniki_wyszukiwarki');
$tpl->setPageTitle($GLOBALS['FRAZY_WPISANE_W_WYSZUKIWARCE']);
$tpl->displayPage('statystyki/wyniki-wyszukiwarki.html');
?>