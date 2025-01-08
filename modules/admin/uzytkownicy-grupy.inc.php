<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$user->CheckPrivileges('users_administration');
use Controllers\UsersAdminController;

$rootController->assign('menu_group', 'users');
$moduleName = 'uzytkownicy-grupy';
$module = new UsersAdminController();

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

switch ($action) {
    case 'add':
        showAddGroup();

        die;
    case 'add_group':
        $module->addGroup($rootController->post->getAll());
        $rootController->redirectPage('admin/module.php?moduleName=' . $moduleName);
        break;

    case 'edit_group':
        $module->updateGroup($rootController->post->get('group_id'), $rootController->post->get('privileges'));
        $module->reloadPrivileges();
        $rootController->redirectPage('admin/module.php?moduleName=' . $moduleName);
        break;

    case 'delete_group':
        $module->deleteGroup($rootController->get->get('id'));
        $rootController->redirectPage('admin/module.php?moduleName=' . $moduleName);
        break;

    case 'change_name':
        $module->changeGroupName($rootController->post->get('id'), $rootController->post->get('name'));
        $rootController->redirectPage('admin/module.php?moduleName=' . $moduleName);
        break;

    case 'load_group':
        showGroup($rootController->get->get('group_id'));
        die;

    default:
        showGroups();
        die();
}

function showAddGroup() {
    global $rootController, $module, $moduleName;
    $groups = $module->getGroups();
    $privileges = $module->getPrivileges();

    $rootController->assign('groups', $groups);
    $rootController->assign('privileges', $privileges);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_selected', 'users_groups');
    $rootController->setPageTitle('Dodaj nową grupę');
    $rootController->displayPage('users/group-add.html');
}

/* funkcja wyswietla uprawnienia dla jednej grupy */

function showGroup($group_id) {
    global $rootController, $module, $moduleName;
    $privileges = $module->getGroupPrivileges($group_id);
    $group_name = $module->getGroupName($group_id);

    $rootController->assign('group_name', $group_name);
    $rootController->assign('privileges', $privileges);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_selected', 'uzytkownicy_grupy');
    $rootController->setPageTitle($GLOBALS['ZARZADZANIE_GRUPAMI_UZYTKOWNIKOW'] ?? 'Zarządzanie grupami użytkowników');
    $rootController->displayPage('users/group-edit.html');
}

/* funkcja wyswietla grupy */

function showGroups() {
    global $rootController, $module, $moduleName;
    $groups = $module->getGroups();
    $privileges = $module->getPrivileges();

    $rootController->assign('groups', $groups);
    $rootController->assign('privileges', $privileges);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_selected', 'uzytkownicy-grupy');
    $rootController->setPageTitle($GLOBALS['ZARZADZANIE_GRUPAMI_UZYTKOWNIKOW'] ?? 'Zarządzanie grupami użytkowników');
    $rootController->displayPage('users/groups.html');
}

?>