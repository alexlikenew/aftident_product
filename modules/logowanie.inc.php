<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ModuleController;

if(isset($moduleController) && is_a($moduleController, ModuleController::class))
    $moduleController = new ModuleController();

$modul_conf = $moduleController->getModuleConf('logowanie');

$path[0]['name'] = $GLOBALS['_PAGE_LOGIN_USER'];
$path[0]['url'] = BASE_URL . '/konto/logowanie.html';
$rootController->assign('logowanie', true);
$rootController->assign('path', $path);
$rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_LOGIN_USER'] . ' - ' . $modul_conf['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($modul_conf['page_keywords']);
$rootController->setPageDescription($modul_conf['page_description']);

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

switch ($action) {
    case 'log_in':
        if ($user->logIn($rootController->post->getAll())) {
            $rootController->assign('logged', true);
            $rootController->assign('user', $_SESSION['user']);

            $rootController->redirectPage(BASE_URL . '/');
        } else {
            $rootController->displayPage('uzytkownik/logowanie.html');
        }
        break;
    default:
        $rootController->displayPage('uzytkownik/logowanie.html');
        break;
}
