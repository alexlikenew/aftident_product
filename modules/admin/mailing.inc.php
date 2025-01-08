<?php



if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

$user->CheckPrivileges('biuletyn_administration');

use Controllers\NewsletterAdminController;

$module = new NewsletterAdminController();
$moduleName = 'mailing';

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

switch ($action) {
    case 'edit':
        showEditForm($rootController->get->get('id'));
        die;

    case 'Zapisz':
        $module->updateTemplate($rootController->post->getAll());
        $rootController->redirectPage('module?moduleName='.$moduleName);
        break;

    case 'Zapisz i kontynuuj edycję':
        $module->updateTemplate($rootController->post->getAll());
        $rootController->redirectPage('module?moduleName='.$moduleName . '&action=edit&id=' . $rootController->post->get('id'));
        break;

    case 'Dodaj szablon':
        $module->addTemplate($rootController->post->getAll());
        $rootController->redirectPage('module?moduleName='.$moduleName);
        break;

    case 'Porzuć zmiany':
        $rootController->redirectPage('module?moduleName='.$moduleName);
        break;

    default:
        showMenu();
        die;
}

function showEditForm($id) {
    global $module, $rootController, $moduleName;
    $mail = $module->loadTemplate($id);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('mail', $mail);
    $rootController->assign('menu_selected', 'mailing');
    $rootController->assign('menu_group', 'Newsletter');
    $rootController->setPageTitle('Zarządzanie szablonami biuletynu');
    $rootController->displayPage('newsletter/mailing-template-edit.html');
}

function showMenu() {
    global $module, $rootController, $moduleName;
    $template = $module->loadTemplateAll();
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('mail_tpl', $template);
    $rootController->assign('menu_selected', 'mailing');
    $rootController->assign('menu_group', 'Newsletter');
    $rootController->setPageTitle('Zarządzanie szablonami biuletynu');
    $rootController->displayPage('newsletter/mailing-templates.html');
}