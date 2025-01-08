<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\ConfigController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'settings');

$moduleName = 'kontakt';

if(isset($configController))
    $module = $configController;
else
    $module = new ConfigController();

switch($action){
    case 'Zapisz':

        $module->createOrUpdateContact($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName='.$moduleName);
        die;
    default:
        showContact();
        die;
}



function showContact(){
    global $module, $moduleName, $moduleConfiguration, $rootController;

    $contact = json_decode(base64_decode($module->getOption('contact')), true);

    $rootController->assign('contact', $contact);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('menu_group', $moduleConfiguration['module_group']);
    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/edit.html');


}

?>