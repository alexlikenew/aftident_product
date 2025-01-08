<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

// w razie pożaru odkomentować
die();

require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/users.class.php';
//require_once ROOT_PATH . '/includes/passwordHelper.class.php';

$Users = new Users($root);
$Users->resetUserPassword($root->request);

die();
