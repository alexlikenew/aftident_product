<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

if ($oUser->logOut()) {
    $tpl->assign('logged', false);
    $root->redirectPage(BASE_URL . '/logowanie.html');
} else {
    $tpl->displayError();
}
?>