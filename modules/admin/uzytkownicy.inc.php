<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\UsersAdminController;
$user->CheckPrivileges('users_administration');

$rootController->assign('menu_group', 'users');
$moduleName = 'uzytkownicy';

$module = new UsersAdminController();

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

switch ($action) {
    case 'add':
        showAddUser();
        die();

    case 'edit_user':
        if ($rootController->get->has('id')) {
            showEdit($rootController->get->get('id'));
        } else {
            $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        }
        break;

    case 'add_new_user':

        if ($module->create($rootController->post->getAll())) {

            $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        } else {
            showAddUser();
        }
        die();

    case 'save_user':
        if ($module->update($rootController->post->getAll())) {
            $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        } else {
            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit_user&id=' . $rootController->post->get('id'));
        }
        break;

    case 'delete_user':
        if ($rootController->get->has('id')) {
            $module->delete($rootController->get->get('id'));
        }
        $rootController->redirectPage($moduleName . '.php');
        break;
}

function showAddUser() {
    global $module, $rootController, $moduleName;

    $rootController->assign('error', $module->getError());
    $rootController->assign('groups', $module->getGroups());
    $rootController->assign('module_name', $moduleName);
    $rootController->setPageTitle($GLOBALS['DODAWANIE_NOWEGO_UZYTKOWNIKA'] ?? 'Nowy użytkownik');
    $rootController->displayPage('users/add.html');
}

function showArticles() {
    global $module, $rootController, $moduleConfiguration, $moduleName;

    $page = 1;
    if ($rootController->get->has('page')) {
        $page = $rootController->get->get('page');
    }
    $limit = 50;
    if ($rootController->get->has('limit')) {
        $temp = $rootController->get->get('limit');
        if (!empty($temp)) {
            $limit = $temp;
        }
    }

    $filter = [];
    if ($rootController->get->has('order_type')) {
        $filter['order_type'] = $rootController->get->get('order_type');
        if (empty($filter['order_type'])) {
            $filter['order_type'] = 'ASC';
        }
    } else {
        $filter['order_type'] = 'ASC';
    }
    if ($rootController->get->has('login')) {
        $filter['order_field'] = $rootController->get->get('order_field');
        if (empty($filter['order_field'])) {
            $filter['order_field'] = 'first_name';
        }
    } else {
        $filter['order_field'] = 'id';
    }
    if ($rootController->get->has('login')) {
        $filter['login'] = $rootController->get->get('login');
    } else {
        $filter['login'] = '';
    }
    if ($rootController->get->has('firm_name')) {
        $filter['firm_name'] = $rootController->get->get('firm_name');
    } else {
        $filter['firm_name'] = '';
    }
    if ($rootController->get->has('first_name')) {
        $filter['first_name'] = $rootController->get->get('first_name');
    } else {
        $filter['first_name'] = '';
    }
    if ($rootController->get->has('last_name')) {
        $filter['last_name'] = $rootController->get->get('last_name');
    } else {
        $filter['last_name'] = '';
    }
    if ($rootController->get->has('email')) {
        $filter['email'] = $rootController->get->get('email');
    } else {
        $filter['email'] = '';
    }
    if ($rootController->get->has('business')) {
        $filter['business'] = $rootController->get->get('business');
        if ($filter['business'] < 0) {
            $filter['business'] = '%';
        }
    }
    if ($rootController->get->has('active')) {
        $filter['active'] = $rootController->get->get('active');
        if ($filter['active'] < 0) {
            $filter['active'] = '%';
        }
    }
    if ($rootController->get->has('admin_login')) {
        $filter['admin_login'] = $rootController->get->get('admin_login');
    }
    if ($rootController->get->has('group_id')) {
        $filter['group_id'] = $rootController->get->get('group_id');
    }

    $rootController->assign('module_name', $moduleName);
    $rootController->assign('pages', $module->getUsersPages($filter, $limit));
    $rootController->assign('page', $page);
    $rootController->assign('interval', $limit * $page);
    $rootController->assign('users', $module->getUsers($filter, $page, $limit));
    $rootController->assign('groups', $module->getGroups());
    if (!empty($_SERVER['QUERY_STRING'])) {
        $query_array = explode('&', $_SERVER['QUERY_STRING']);
        $query = '';
        $first = true;
        foreach ($query_array as $key => $value) {
            $temp = explode('=', $value);
            if ($temp[0] != 'page') {
                if (!$first) {
                    $query .= '&';
                } else {
                    $first = false;
                }
                $query .= $temp[0] . '=' . $temp[1];
            }
        };
        $rootController->assign('query_string', '&' . $query);
    } else {
        $rootController->assign('query_string', '');
    }

    $rootController->assign('menu_selected', $moduleName);
    $rootController->setPageTitle('Zarządzanie użytkownikami');
    $rootController->displayPage('users/list.html');
}

function showEdit($id) {
    global $rootController, $module, $moduleName;

    if ($user = $module->getUserById($id)) {
        if (!empty($_POST)) {
            $user = $_POST;
        }
        $groups = $module->getGroups();

        $rootController->assign('u', $user);
        $rootController->assign('groups', $groups);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('menu_selected', 'uzytkownicy');
        $rootController->setPageTitle($GLOBALS['EDYCJA_UZYTKOWNIKA'] ?? 'Edycja użytkownika');
        $rootController->displayPage('users/edit.html');
    } else {
        showArticles();
    }
}

?>