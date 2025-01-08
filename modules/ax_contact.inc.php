<?php

if (!defined('SCRIPT_CHECK')) {
    die('No-Hack here buddy.. ;)');
}

use Controllers\ContactController;

$module = new ContactController();

$data = [];
parse_str( $rootController->getRequestParameter('form'), $data);

if(!isset($data['csrf_token']) || $data['csrf_token'] != getCSRFToken()){
    die;
}

$result = $rootController->sendContact($data);
if($data['offer_category'])
    $module->saveContact($data, ContactController::OFFER_MESSAGE);
else
    $module->saveContact($data, ContactController::CONTACT_MESSAGE);

if($result['status'] == 'success'){
    echo json_encode([
        'status'    => 'success',
        'message'   => $GLOBALS['FORMULARZ_WYSLANE'],
    ]);
}
else
    echo json_encode([
        'status'    => 'error',
        'message'   => $GLOBALS['FORMULARZ_BLAD'],
    ]);
die;
