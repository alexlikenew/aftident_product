<?php

/*
 *      Panel administracyjny / zarzadzanie stroną główną
 *      plik:       admin/glowna.inc.php
 *      autor:      Technetium [Tc]
 *                  Przemek Szalko
 *                  01 czerwiec 2007
 *      modify:     Marek Kleszyk
 *                  05 luty 2009
 *      system:     T.CMS-4.0-SEO
 */

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\UsersAdminController;
$oUsers = new UsersAdminController();
$oUsers->CheckPrivileges('page_config');

// require_once ROOT_PATH . '/includes/rejestrZmian.class.php';

// $oRejestr = new Rejestr($root);
$modul = 'konfiguracja-mail';

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->post->get('action');
}

global $configController;
switch ($action) {
    case 'save_all':
        $configController->UpdateExtra('auth_email', $rootController->post->get('auth_email'));
		$configController->UpdateExtra('auth_pass', $rootController->post->get('auth_pass'));
		$configController->UpdateExtra('auth_smtp', $rootController->post->get('auth_smtp'));
		$configController->UpdateExtra('biuro_email', $rootController->post->get('biuro_email'));
		$configController->UpdateExtra('auth_port', $rootController->post->get('auth_port'));
		$configController->UpdateExtra('auth_host', $rootController->post->get('auth_host'));
		$configController->UpdateExtra('auth_auth', $rootController->post->get('auth_auth'));
        // $oRejestr->addWpis('Konfiguracja mailera', '/', 'zmieniono', 'mailing');
        echo('Zmiany zostały zapisane!');
        $rootController->redirectPage($modul . '.php');
        break;

    default:
        break;
}

$auth_email = $configController->LoadOptionExtra('auth_email');
$auth_pass = $configController->LoadOptionExtra('auth_pass');
$auth_smtp = $configController->LoadOptionExtra('auth_smtp');
$biuro_email = $configController->LoadOptionExtra('biuro_email');
$auth_port = $configController->LoadOptionExtra('auth_port');
$auth_host = $configController->LoadOptionExtra('auth_host');
$auth_auth = $configController->LoadOptionExtra('auth_auth');

$rootController->assign('auth_email', $auth_email);
$rootController->assign('auth_pass', $auth_pass);
$rootController->assign('auth_smtp', $auth_smtp);
$rootController->assign('biuro_email', $biuro_email);
$rootController->assign('auth_port', $auth_port);
$rootController->assign('auth_host', $auth_host);
$rootController->assign('auth_auth', $auth_auth);

$rootController->assign('menu_selected', 'konfiguracja-mail');
$rootController->assign('menu_group', 'Ustawienia');
$rootController->setPageTitle($GLOBALS['KONFIGURACJA_MAILERA'] ?? 'Konfiguracja mailera');
$rootController->displayPage('config-mail/edit.html');
?>