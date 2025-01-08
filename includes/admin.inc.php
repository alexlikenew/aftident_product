<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\UsersAdminController;

if ($_SERVER['HTTP_HOST'] != $LANG_MAIN['domain'] && $_SERVER['HTTP_HOST'] != 'localhost') {
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL = "https://";
    } else {
        $pageURL = "http://";
    }
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $LANG_MAIN['domain'] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $LANG_MAIN['domain'] . $_SERVER['REQUEST_URI'];
    }
    redirect301($pageURL);
}

$rootController->setTemplatePath('admin');
$rootController->assign('TPL_URL', $CONF['base_url'] . '/templates/admin');

$user = new UsersAdminController();

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

if (!$user->isLogged()) {
    if ($action == 'log_in') {
        if (!$user->logIn($rootController->post->getAll(), true)) {
            $rootController->display('misc/loguj.html');
            die;
        }
    }
    elseif($action == 'resetPassword'){
        $rootController->assign('token', getCSRFToken());
        $rootController->display('misc/password-remind.html');
        die();

    }
    elseif($action == 'remindPass'){

        if($rootController->post->get('csrf') != getCSRFToken()){
            $rootController->setError('Błędny token', true);
            die();
        }
        $result = $user->remindPass($rootController->post->get('email'));
        if(!$result){

            $rootController->setError($user->getError());
            $rootController->assign('email', $rootController->post->get('email'));
            $rootController->display('misc/password-remind.html');
            die();
        }
        else{
            $rootController->display('misc/info.html');
            die();
        }
    }else {
        $rootController->display('misc/loguj.html');
        die;
    }
}

if (!$user->logAdmin()) {
    $rootController->display('misc/loguj.html');
    die;
}

$rootController->assign('user', $_SESSION['user']);
if (DEBUG_MODE == 1)
    echo '<div class="center error">DEBUG_MODE ON</div>';
?>