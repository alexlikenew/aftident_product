<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$user->logOut();

header('Location: ./index.php');
die;
?>