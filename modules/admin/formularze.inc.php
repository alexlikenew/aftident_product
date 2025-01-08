<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\FormAdminController;
use Controllers\FormController;
use Controllers\Inputs\FormInputController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'pages');

$moduleName = 'formularze';
$module = new FormAdminController();

switch($action){
    case 'show_inputs':
        if($rootController->getRequestParameter('id')){
            showInputsList($rootController->getRequestParameter('id'));
            die;
        }
    case 'edit_input':
        if($rootController->getRequestParameter('id')){
            showEditInput($rootController->getRequestParameter('id'), $rootController->getRequestParameter('type'));
            die;
        }
    case 'add_input':
        if($rootController->getRequestParameter('parent_id')){
            showAddInput($rootController->getRequestParameter('parent_id'));
            die;
        }
    case 'create_input':

        if($rootController->getRequestParameter('parent_id')){
            $module->saveInput($rootController->post->getAll());
        }
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=show_inputs&id='.$rootController->getRequestParameter('parent_id'));
        die;
    case 'update_input':
        if($rootController->getRequestParameter('parent_id')){
            $module->updateInput($rootController->post->getAll());
        }
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=show_inputs&id='.$rootController->getRequestParameter('parent_id'));
        die;
    case 'delete_input':
        if($rootController->getRequestParameter('id')){
            $module->deleteInput($rootController->getRequestParameter('id'), $rootController->getRequestParameter('type'));
        }
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=show_inputs&id='.$rootController->getRequestParameter('parent_id'));
        die;

}

function showInputsList($id){
    global $module, $page, $rootController, $moduleName, $moduleConfiguration, $logger;

    if(!$module instanceof \Interfaces\ControllerInterface){
        $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
        $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
        $rootController->redirectPage('admin/module.php?moduleName=strony');
    }

    $articles = $module->getInputsInfo($id);

    $rootController->assign('input_types', FormController::$types);
    $rootController->assign('parent_id', $id);
    $rootController->assign('pages', $pages);
    $rootController->assign('articles', $articles);
    $rootController->assign('page', $page);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('menu_group', $moduleConfiguration['module_group']);
    $rootController->setPageTitle( $moduleConfiguration['name'] ?? '');

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/inputs-list.html');
}

function showAddInput($id) {
    global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax;

    $opcje['op_page_title'] = $configController->getOption('op_page_title');
    $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    $opcje['op_page_description'] = $configController->getOption('op_page_description');
    $mainLang = $configController->getLangMain();
    $types = $module->getInputsTypes();

    $rootController->assign('input_types', $types);
    $rootController->assign('lang_main', $mainLang);
    $rootController->assign('parent_id', $id);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('opcje', $opcje);
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('tab_selected', 'content');
    $rootController->setPageTitle('Nowy element');

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add-input.html');
}

function showEditInput($id, $type) {
    global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax;

    $opcje['op_page_title'] = $configController->getOption('op_page_title');
    $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    $opcje['op_page_description'] = $configController->getOption('op_page_description');
    $mainLang = $configController->getLangMain();
    $types = $module->getInputsTypes();
    $input = FormInputController::getByType($type);
    $input->setDataById($id);
    $rootController->assign('content', $input->getContent());
    $lang = json_decode(file_get_contents(ROOT_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . "langs" . DIRECTORY_SEPARATOR . "pl.json"), true);
    $rootController->assign('titles', $input->getTitles());
    $rootController->assign('item', $input);
    $rootController->assign('lang_config', $lang);
    $rootController->assign('input_types', $types);
    $rootController->assign('lang_main', $mainLang);
    $rootController->assign('parent_id', $id);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('opcje', $opcje);
    $rootController->assign('menu_selected', $moduleName);
    $rootController->assign('tab_selected', 'content');
    $rootController->setPageTitle('Nowy element');

    $rootController->displayPage( $moduleConfiguration['templates_dir'].'/edit-input.html');
}