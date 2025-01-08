<?php

if (!defined('SCRIPT_CHECK')) {
    die('No-Hack here buddy.. ;)');
}

use Controllers\NewsletterController;

$newsletterController = new NewsletterController();

$data = [];
parse_str( $rootController->getRequestParameter('form'), $data);

if(!isset($data['token']) || $data['token'] != getCSRFToken()){
    die;
}




$result = $newsletterController->assignUser($data);


echo json_encode($result);

die;