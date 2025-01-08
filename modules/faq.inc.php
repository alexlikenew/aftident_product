<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');



use Controllers\FaqController;


$module = 'faq'; // nazwa modulu, tabela w bazie, link w adresie
$faqController = new FaqController();


$moduleConfiguration = $moduleController->getModuleConf($module);
$rootController->assign('module', $moduleConfiguration);


if (!$moduleConfiguration['active']) {
    if (!BASE_URL)
        redirect301('/');
    else
        redirect301(BASE_URL);
    die;
}
$pages = $faqController->getPages(true);
$items = $faqController->getItems($pages);

$rootController->assign('articles', $items);
$strPageUrl = '';
if (is_array($rootController->getParams()) && count($rootController->getParams()) > 0) {
    $strPageUrl = $rootController->getParams[0];
}

$print = 0;
if ($rootController->hasParameter('print')) {
    $print = $rootController->getParameter('print');
}

$rootController->assign('menu_selected', BASE_URL . '/' . $module . '.html');

$path[] = [
    'name'  => $moduleConfiguration['title'],
    'url'   => BASE_URL . '/' . $moduleConfiguration['title_url'] . '.html',
    ];

$articles = $faqController->getAll(true);
//dd($articles);
$rootController->assign('articles', $articles);
$rootController->assign('path', $path);
if(TITLE_PREFIX)
    $rootController->setPageTitle(TITLE_PREFIX  . ' - ' . $moduleConfiguration['page_title'] . TITLE_SUFFIX);
else
    $rootController->setPageTitle(TITLE_PREFIX .  $moduleConfiguration['page_title'] . TITLE_SUFFIX);
$rootController->setPageKeywords($moduleConfiguration['page_keywords']);
$rootController->setPageDescription($moduleConfiguration['page_description']);
$rootController->displayPage($module . '/main.html');

die;


