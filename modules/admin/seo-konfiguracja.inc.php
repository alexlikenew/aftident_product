<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$moduleName = 'seo-konfiguracja';
$rootController->assign('menu_group', 'Ustawienia');

if(!isset($configController))
    $configController = $rootController->loadConfigController();

switch ($action) {
    case 'zapisz_konfiguracja':
        $configController->update([
            'name'  =>  'op_page_title',
            'value' =>  $rootController->post->get('op_page_title')
        ]);
        $configController->update([
            'name'  => 'op_page_keywords',
            'value' => $rootController->post->get('op_page_keywords')
        ]);
        $configController->update([
            'name'  => 'op_page_description',
            'value' => $rootController->post->get('op_page_description')
        ]);
        $configController->updateOptionLang([
            'name'  => 'page_title',
            'value' => $rootController->post->get('page_title')
        ]);
        $configController->updateOptionLang([
            'name'  =>'page_title_prefix',
            'value' => $rootController->post->get('page_title_prefix')
        ]);
        $configController->updateOptionLang([
            'name'  => 'page_title_suffix',
            'value' => $rootController->post->get('page_title_suffix')
        ]);
        $configController->updateOptionLang([
            'name'  => 'page_keywords',
            'value' => $rootController->post->get('page_keywords')
        ]);
        $configController->updateOptionLang([
            'name'  => 'page_description',
            'value' => $rootController->post->get('page_description')
        ]);
        $rootController->redirectPage('module.php?moduleName=' . $moduleName );
        die;

    default:
        showUstawienia();
        die;
}

function showUstawienia() {
    global $configController, $rootController, $moduleName;

    $opcje['op_page_title'] = $configController->getOption('op_page_title');
    $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    $opcje['op_page_description'] = $configController->getOption('op_page_description');
    $page_title = $configController->getOptionLangAdmin('page_title');
    $page_title_prefix = $configController->getOptionLangAdmin('page_title_prefix');
    $page_title_suffix = $configController->getOptionLangAdmin('page_title_suffix');
    $page_keywords = $configController->getOptionLangAdmin('page_keywords');
    $page_description = $configController->getOptionLangAdmin('page_description');

    $mainLang = $configController->getLangMain();

    $rootController->assign('lang_main', $mainLang);

    $rootController->assign('opcje', $opcje);
    $rootController->assign('page_title', $page_title);
    $rootController->assign('page_title_prefix', $page_title_prefix);
    $rootController->assign('page_title_suffix', $page_title_suffix);
    $rootController->assign('page_keywords', $page_keywords);
    $rootController->assign('page_description', $page_description);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'settings');
    $rootController->setPageTitle($GLOBALS['KONFIGURACJA_DOMYSLNA_SEO'] ?? 'Konfiguracj domyślna SEO');
    $rootController->displayPage('config-seo/edit.html');
}

?>