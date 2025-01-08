<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ModuleController;

if(isset($moduleController) && is_a($moduleController, ModuleController::class))
    $moduleController = new ModuleController();

$modul_conf = $moduleController->getModuleConf('rejestracja');

$path[0]['name'] = $GLOBALS['_PAGE_REGISTER'];
$path[0]['url'] = BASE_URL . '/rejestracja.html';
$rootController->assign('path', $path);
$rootController->setPageTitle(TITLE_PREFIX . $GLOBALS['_PAGE_REGISTER'] . ' - ' . $modul_conf['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($modul_conf['page_keywords']);
$rootController->setPageDescription($modul_conf['page_description']);

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getParameter('action');
}

switch ($action) {
    case 'register':
        if ($user->create($rootController->post->getAll())) {
            $rootController->displayInfo();
        } else {
            $rootController->displayPage('uzytkownik/rejestracja.html');
        }
        break;

    case 'activate':
        if ($user->setActive($rootController->get->get('id'))) {
            $rootController->displayInfo();
        } else {
            $rootController->displayError();
        }
        break;

    case 'remove':
        if ($user->delete($rootController->get->get('id'))) {
            $rootController->displayInfo();
        } else {
            $rootController->displayError();
        }
        break;

    default:
        $rootController->displayPage('uzytkownik/rejestracja.html');
        break;
}
?>