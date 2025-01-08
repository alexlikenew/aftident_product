<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FilesController;
use Controllers\PagesController;
use Controllers\SaleDepartmentController;
use Controllers\ModuleController;

$module = 'kontakt';
$filesController = new FilesController();
$pagesController = new PagesController();
$saleDepartmentController = new SaleDepartmentController();

$moduleController = new ModuleController();

$moduleConfiguration = $moduleController->getModuleConf($module);

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

if ($action == 'send') {

    if ($res = $pagesController->sendContact($rootController->post->getAll())) {
        echo json_encode([
            'status'    => 'success',
            'message'   => $pagesController->getInfo(),
            ]);
        die;
    }
}

$page = $pagesController->getItem('kontakt');

$rootController->assign('page', $page);

$path[0]['name'] = 'Kontakt';
$path[0]['url'] = '';

$tokensmax = sizeof(file(ROOT_PATH . '/js/token/tokens.txt'));
$tokenid = rand(0, $tokensmax - 1);

//$items = $saleDepartmentController->getItems(1);
//dd($items);
$rootController->assign('pageType', 'kontakt');
//$rootController->assign('workers', $items);
$rootController->assign('tokenid', $tokenid);
$rootController->assign('path', $path);
$rootController->assign('module', $moduleConfiguration);
if(TITLE_PREFIX)
    $rootController->setPageTitle(TITLE_PREFIX .  ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
else
    $rootController->setPageTitle( $moduleConfiguration['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($moduleConfiguration['page_keywords'] ?? '');
$rootController->setPageDescription($moduleConfiguration['page_description'] ?? '');
$rootController->displayPage( 'contact/main.html');
?>