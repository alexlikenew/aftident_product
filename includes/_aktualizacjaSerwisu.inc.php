<?php


if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

require_once ROOT_PATH . '/includes/autoload.php';

if (NEWSLETTER == 1) {

    $oNewsletter = new Newsletter($root);

    $oNewsletter->wysliAutomat(100);
}
?>