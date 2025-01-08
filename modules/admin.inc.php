<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$token = getCSRFToken();
$rootController->assign('csrf_token', $token);
require_once ROOT_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'module.inc.php';
die;


