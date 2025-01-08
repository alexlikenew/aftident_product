<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$user->CheckPrivileges('page_config');

$module = $rootController->loadConfigController();
$moduleName = 'konfiguracja';
$rootController->assign('menu_group', 'Ustawienia');

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

switch ($action) {
    case 'save_all':
        $return = $module->updateAll($rootController->post->getAll());

        if ($return === true) {
            $rootController->assign('info', $GLOBALS['ZMIANY_W_KONFIGURACJI_ZOSTALY_ZAPISANE']);
        } else {
            $rootController->assign('error', $GLOBALS['WYSTAPIL_BLAD_PODCZAS_ZSPISYWANIA_ZMIAN_W_KONFIGURACJI']);
        }
        $rootController->redirectPage('module.php?moduleName=' . $moduleName );
        die;

    case 'zapisz':
        $return = $module->update($rootController->post->get('conf_name'), $rootController->post->get('conf_value'));

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
        $return = $module->create($rootController->post->getAll());

        if ($return === true) {
            $rootController->assign('info', $GLOBALS['NOWA_OPCJA_ZOSTALA_DODANA']);
        } else {
            $rootController->assign('error', $GLOBALS['WYSTAPIL_BLAD_PODCZAS_DODAWANIA_NOWEJ_OPCJI']);
        }
        $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        die;

    default:
        showKonfiguracja();
        die();
}

function showKonfiguracja() {
    global $module, $rootController, $moduleName;

    $rootController->assign('config', $module->loadToEdit());
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', 'settings');
    $rootController->setPageTitle($GLOBALS['KONFIGURACJA_STRONY'] ?? 'Konfiguracja strony');
    $rootController->displayPage('config/edit.html');
}

?>