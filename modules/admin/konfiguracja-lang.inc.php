<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$user->CheckPrivileges('page_config');

$module = $rootController->loadConfigController();
$moduleName = 'konfiguracja-lang';
$rootController->assign('menu_group', 'settings');

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

switch ($action) {
    case 'save_all':
        $return = $module->updateLangAll($rootController->post->getAll());

        if ($return === true) {
            $rootController->assign('info', $GLOBALS['ZMIANY_W_KONFIGURACJI_ZOSTALY_ZAPISANE']);
        } else {
            $rootController->assign('error', $GLOBALS['WYSTAPIL_BLAD_PODCZAS_ZSPISYWANIA_ZMIAN_W_KONFIGURACJI']);
        }
        $rootController->redirectPage('module.php?moduleName=' . $moduleName );
        die;

    case 'zapisz':
        $return = $module->updateOptionLang($rootController->post->get('conf_name'), $rootController->post->get('conf_value'));

        if ($return === true) {
            $rootController->assign('info', $GLOBALS['ZMIANY_W_KONFIGURACJI_ZOSTALY_ZAPISANE']);
        } else {
            $rootController->assign('error', $GLOBALS['WYSTAPIL_BLAD_PODCZAS_ZSPISYWANIA_ZMIAN_W_KONFIGURACJI']);
        }
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        die;

    case 'Kasuj':
        $return = $module->delete($rootController->post->get('conf_name'));

        if ($return === true) {
            $rootController->assign('info', $GLOBALS['OPCJA_ZOSTALA_SKASOWANA']);
        } else {
            $rootController->assign('error', $GLOBALS['WYSTAPIL_BLAD_PODCZAS_KASOWANIA_OPCJI']);
        }
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        die;

    case 'new_option':
        $return = $module->createLangOption($rootController->post->getAll());

        if ($return === true) {
            $rootController->assign('info', $GLOBALS['NOWA_OPCJA_ZOSTALA_DODANA']);
        } else {
            $rootController->assign('error', $GLOBALS['WYSTAPIL_BLAD_PODCZAS_DODAWANIA_NOWEJ_OPCJI']);
        }
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        die;

}

function showArticles() {
    global $module, $rootController, $moduleName;

    $rootController->assign('config', $module->loadLangConfig());
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'settings');
    $rootController->setPageTitle($GLOBALS['KONFIGURACJA_STRONY'] ?? 'Konfiguracja strony');
    $rootController->displayPage('config-lang/edit.html');
}

function showAdd() {
    global $rootController, $moduleName, $LANG_MAIN;

    $rootController->assign('module_name', $moduleName);
    $rootController->assign('lang_main', $LANG_MAIN);
    $rootController->assign('menu_group', 'settings');
    $rootController->assign('menu_selected', 'slownik');
    $rootController->setPageTitle('Zarządzanie słownikiem');
    $rootController->displayPage('config-lang/add.html');
}

?>