<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('stat_administration');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/newsletter.class.php';

$oNewsletter = new Newsletter($root);

$tpl->assign('mail_tpl', $oNewsletter->LoadTemplateAll());
$tpl->assign('stats', $oNewsletter->getStats());

$tpl->setPageTitle($GLOBALS['STATYSTYKA_WYSYLANIA_BIULETYNU']);
$tpl->assign('menu_selected', 'statystyka_biuletyn');
$tpl->displayPage('statystyki/statystyka-biuletyn.html');
?>