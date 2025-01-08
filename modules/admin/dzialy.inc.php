<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\DepartmentAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'offer');

$moduleName = 'dzialy';
$module = new DepartmentAdminController();