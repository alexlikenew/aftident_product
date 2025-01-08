<?php
require_once ROOT_PATH . '/includes/autoload.php';
//require_once ROOT_PATH . '/includes/newsletter.class.php';

$oNewsletter = new Newsletter($root);

$uid = '';
if (is_array($root->params) && count($root->params) > 0) {
    $uid = $root->params[0];
}

if (!empty($uid)) {
    $oNewsletter->_countOdebrano($uid);
} else {
    $oNewsletter->_countClik($root->request->get->get('id'));
    header('Location: ../show.html');
}
?>