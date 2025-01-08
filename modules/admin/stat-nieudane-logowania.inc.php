<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('stat_administration');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/usersAdmin.class.php';

$oUsers = new UsersAdmin($root);

$page = 1;
if ($root->request->hasParameter('page')) {
    $page = $root->request->getParameter('page');
}

$limit = 50;

$tpl->assign('log', $oUsers->LoadLog($limit, $page));
$tpl->assign('pages', $oUsers->GetPagesLog($limit));
$tpl->assign('page', $page);
$tpl->assign('interval', $limit * ($page - 1));
$tpl->assign('menu_selected', 'stat_nieudane_logowania');
$tpl->setPageTitle($GLOBALS['NIEUDANE_LOGOWANIA']);
$tpl->displayPage('statystyki/nieudane-logowania.html');
?>