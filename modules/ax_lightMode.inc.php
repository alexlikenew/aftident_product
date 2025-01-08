<?php

if (!defined('SCRIPT_CHECK')) {
    die('No-Hack here buddy.. ;)');
}

$mode =  $rootController->getRequestParameter('lightMode');
if($mode == 1)
    $_SESSION['darkMode'] = 1;
else
    $_SESSION['darkMode'] = 0;
/*
if(!isset($data['token']) || $data['token'] != getCSRFToken()){
    die;
}

$result = $rootController->sendContact($data);


if($result['status'] == 'success')
    echo json_encode([
        'message'   => $rootController->getInfo(),
    ]);
else
    echo json_encode([
        'message'   => $rootController->getError(),
    ]);
*/
die;
