<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');
use Controllers\UsersAdminController;
$user->CheckPrivileges('users_administration');


$module = new UsersAdminController();
$rootController->assign('menu_group', 'users');
$moduleName = 'uzytkownicy-uprawnienia';

switch ($action) {
    case 'add_privilege':
        $module->addPrivilege($rootController->post->getAll());
        $module->reloadPrivileges();
        $rootController->redirectPage('admin/module.php?moduleName=' . $moduleName );
        die;

    case 'edit_privilege':
        $module->updatePrivilege($rootController->post->getAll());
        $module->reloadPrivileges();
        $rootController->redirectPage('admin/module.php?moduleName=' . $moduleName);
        die;

    case 'delete_privilege':
        $module->deletePrivilege($rootController->get->get('privilege_id'));
        $module->reloadPrivileges();
        $rootController->redirectPage('admin/module.php?moduleName=' . $moduleName);
        die;

    default:
        $module->reloadPrivileges();
        showPrivileges();
        die;
}

/* funkcja wyswietla uprawnienia */

function showPrivileges() {
    global $rootController, $module, $moduleName;

    $privileges = $module->getPrivileges();

    $rootController->assign('privileges', $privileges);

    $rootController->assign('module_name', $moduleName);
    $rootController->setPageTitle('Zarządzanie uprawnieniami użytkowników');
    $rootController->displayPage('users/privileges.html');
}

?>