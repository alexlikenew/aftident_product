<?php

if (!defined('SCRIPT_CHECK')) {
    die('No-Hack here buddy.. ;)');
}

use Controllers\ContactController;
use Controllers\RoomsController;

$module = new ContactController();
$roomController = new RoomsController();



$data = [];
parse_str( $rootController->getRequestParameter('form'), $data);

if(!isset($data['csrf_token']) || $data['csrf_token'] != getCSRFToken()){
    die;
}

$data['room'] = $roomController->getArticleById($data['room']);

$result = $rootController->sendReservation($data);
//$module->saveContact($data, ContactController::CONTACT_MESSAGE);

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
