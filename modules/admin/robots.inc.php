<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/rejestr.class.php';

$oRejestr = new Rejestr($root);
$modul = 'robots';

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'save':
        file_put_contents(ROOT_PATH."/robots.txt", $_POST['value']);
        $oRejestr->addWpis('Robots', '', 'zmieniono', 'robots');
        $root->messages->setInfo($GLOBALS['ZMIANY_ZOSTALY_ZAPISANE']);
        $root->redirectPage($modul . '.php');
        break;
    default:
        break;
}

if(!is_writable( ROOT_PATH."/robots.txt" ))
{
    $tpl->assign('show_writable_error', 1);
}



$value = file_get_contents(ROOT_PATH."/robots.txt");

$tpl->assign('value', $value);
$tpl->assign('menu_selected', 'robots');
$tpl->setPageTitle("Podgląd pliku robots.txt");
$tpl->displayPage('misc/robots.html');
?>