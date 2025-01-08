<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');
$moduleName = 'haslo';
$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}


switch ($action) {
    case 'save_pass':
        if ($user->changePass($rootController->post->getAll(), true)) {
            $rootController->redirectPage('module.php');
        } else {
            showPassForm();
        }
        break;

    default:
        showPassForm();
        die();
}

function showPassForm() {
    global $rootController, $moduleName;
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_selected', 'haslo');
    $rootController->assign('menu_group', 'settings');
    $rootController->setPageTitle($GLOBALS['ZMIANA_HASLA'] ?? 'Zmiana hasła');
    $rootController->displayPage('config/password-change.html');
}

?>