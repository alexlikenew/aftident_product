<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ProjectsAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'pages');

$moduleName = 'projekty-unijne';
$module = new ProjectsAdminController();