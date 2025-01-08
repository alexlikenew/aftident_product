<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\CsrAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'pages');

$moduleName = 'zaangazowanie-spoleczne';
$module = new CsrAdminController();