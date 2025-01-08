<?php


if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$oUsers->CheckPrivileges('page_config');


$oRejestr = new Rejestr($root);
$modul = 'htaccess';

$action = '';
if ($root->request->hasParameter('action')) {
    $action = $root->request->getParameter('action');
}

switch ($action) {
    case 'save':
        if(file_put_contents(ROOT_PATH."/.htaccess", $_POST['value']))
        {
            $oRejestr->addWpis('Htaccess', '', 'zmieniono', 'htaccess');
            $root->messages->setInfo($GLOBALS['ZMIANY_ZOSTALY_ZAPISANE']);
            $root->redirectPage($modul . '.php'); 
        }
        else
        {
            $root->messages->setError("Wystąpił błąd podczas zapisywania pliku");
        }

        break;
    default:
        break;
}

if(!is_writable( ROOT_PATH."/.htaccess" ))
{
    $tpl->assign('show_writable_error', 1);
}



$value = file_get_contents(ROOT_PATH."/.htaccess");

$tpl->assign('value', $value);
$tpl->assign('menu_selected', 'htaccess');
$tpl->setPageTitle("Podgląd pliku .htaccess");
$tpl->displayPage('misc/htaccess.html');
?>