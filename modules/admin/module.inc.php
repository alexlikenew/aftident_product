<?php
namespace Controllers;
if (!defined('SCRIPT_CHECK'))
die('No-Hack here buddy.. ;)');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

use Controllers\ModuleController;
use Symfony\Component\ErrorHandler\Debug;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Controllers\BlocksAdminController;

$logger = new Logger('admin-modules');
$blocksController = new BlocksAdminController();
if($rootController->hasParameter('ajax') && (!$rootController->hasParameter('token') || $rootController->getRequestParameter('token') != getCSRFToken())){
    die('');
}

$page = 1;
if ($rootController->hasParameter('page')) {
    $page = $rootController->getRequestParameter('page');
}

$action = '';
if ($rootController->hasParameter('action')) {
    $action = $rootController->getRequestParameter('action');
}

$tab = 'content';
if ($rootController->hasParameter('tab')) {
    $tab = $rootController->getRequestParameter('tab');
}

$ajax = false;
$ajax = $rootController->getRequestParameter('ajax');
$moduleName = false;
$moduleName = $rootController->getRequestParameter('moduleName');

$files = new FilesAdminController();
$gallery = new GalleryAdminController();
$filesType = ModuleController::getFileType($moduleName);
$moduleConfiguration = ModuleController::loadModuleConfiguration($moduleName);
//dump($moduleName);
//dd($moduleConfiguration);
$parentId = 0;
    $parentId = $rootController->getRequestParameter('cat');
$rootController->assign('parent_id', $parentId);

$category_id = null;
if($rootController->hasParameter('category_id'))
    $category_id = $rootController->getRequestParameter('category_id');



if($moduleName){

    if(file_exists(__DIR__.DIRECTORY_SEPARATOR.$moduleName.'.inc.php')){

        require_once($moduleName.'.inc.php');
    }
    //else
    // {
    //    $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
    //   $logger->error(date('y-m-d H:i:s').' Module '.$moduleName.' don\'t exist');
    //    $rootController->assign('error', 'Nie ma takiego modułu!');
    //    $rootController->display('misc/error.html');
    //    die();
    //}
}
//else{
//    $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
//   $logger->error(date('y-m-d H:i:s').' Module '.$moduleName.' don\'t exist');
//
//    $rootController->redirectPage('admin');
//}

/**** Load default functions ****/

if(!function_exists('showAdd')){
    function showAdd($tab = 'content', $new_category = false) {
        global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax;

        $opcje['op_page_title'] = $configController->getOption('op_page_title');
        $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
        $opcje['op_page_description'] = $configController->getOption('op_page_description');
        $mainLang = $configController->getLangMain();

        $scale = $module->getScaleSize();
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('scale', $scale);
        $rootController->assign('opcje', $opcje);
        $rootController->assign('galleries', $gallery->loadGalleriesNames());
        $rootController->assign('menu_selected', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle('Nowy element');

        if($new_category)
            $rootController->displayPage( 'categories/add.html');
        else
            $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add.html');
    }
}

if(!function_exists('showEdit')){
    function showEdit($id, $tab = 'content', $duplicate = false) {

        global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController;

        $article = $module->getArticleById($id);

        if ($article) {
            $galleries = $gallery->loadGalleriesNames();
            $description = $module->loadDescriptionById($id);

            $scale = $module->getScaleSize();
            $files = $files->loadFilesAdmin($article['id'], $filesType);

            $blocks = $module->getBlocksById($id, $filesType, false);

            $blocksIds = [];
            foreach($blocks as $block){
                    if($block['items']){
                        foreach($block['items'] as $b)
                            $blocksIds[] = $b['id'];
                    }
                    else
                        $blocksIds[] = $block['id'];
            }


            $allBlocks = $blocksController->getUniversalBlocks(true, $blocksIds);

            $rootController->assign('blocks', $blocks);
            $rootController->assign('all_blocks', $allBlocks);

            $mainLang = $configController->getLangMain();
            $rootController->assign('duplicate', $duplicate);
            $rootController->assign('module_name', $moduleName);
            $rootController->assign('lang_main', $mainLang);
            $rootController->assign('files_type', $filesType);
            $rootController->assign('files', $files);
            $rootController->assign('scale', $scale);
            $rootController->assign('item', $article);
            $rootController->assign('opis', $description);
            $rootController->assign('galleries', $galleries);
            $rootController->assign('module_name', $moduleName);
            $rootController->assign('menu_group', $moduleConfiguration['module_group']);
            $rootController->assign('tab_selected', $tab);
            $rootController->setPageTitle('Edycja');
            if($ajax){
                $rootController->display( $moduleConfiguration['templates_dir'].'/edit.html');
                die();
            }

            $rootController->displayPage( $moduleConfiguration['templates_dir'].'/edit.html');
        } else {
            $rootController->redirectPage('module.php?moduleName='.$moduleName);
        }
    }
}

if(!function_exists('showEditCategory')){
    function showEditCategory($id, $tab = 'content', $duplicate = false) {

        global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController;
        $categoryController = $module->getCategoryController();
        $article = $categoryController->getArticleById($id);

        if ($article) {
            $galleries = $gallery->loadGalleriesNames();

            $description = $categoryController->loadDescriptionById($id);

            $scale = $categoryController->getScaleSize();
            //$files = $files->loadFilesAdmin($article['id'], $filesType);

            //$blocks = $module->getBlocksById($id, $filesType, false);

            //$blocksIds = [];
            //foreach($blocks as $block){
            //    $blocksIds[] = $block['id'];
            //}

            //$allBlocks = $blocksController->getUniversalBlocks(true, $blocksIds);

            //$rootController->assign('blocks', $blocks);
            //$rootController->assign('all_blocks', $allBlocks);

            $mainLang = $configController->getLangMain();
            $rootController->assign('duplicate', $duplicate);
            $rootController->assign('module_name', $moduleName);
            $rootController->assign('lang_main', $mainLang);
            //$rootController->assign('files_type', $filesType);
            //$rootController->assign('files', $files);
            $rootController->assign('scale', $scale);
            $rootController->assign('item', $article);
            $rootController->assign('opis', $description);
            $rootController->assign('galleries', $galleries);
            $rootController->assign('module_name', $moduleName);
            $rootController->assign('menu_group', $moduleConfiguration['module_group']);
            $rootController->assign('tab_selected', $tab);
            $rootController->setPageTitle('Edycja');
            if($ajax){
                $rootController->display( 'categories/edit.html');
                die();
            }

            $rootController->displayPage( 'categories/edit.html');
        } else {
            $rootController->redirectPage('module.php?moduleName='.$moduleName);
        }
    }
}

if(!function_exists('showArticles')){
    function showArticles() {

        global $module, $page, $rootController, $moduleName, $moduleConfiguration, $logger, $parentId;

        if(!$module instanceof \Interfaces\ControllerInterface){
            $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
            $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
            $rootController->redirectPage('admin/module.php?moduleName=strony');
        }

        if($parentId)
            $rootController->assign('path', $module->getPathByPid($parentId));

        $pages = $module->getPagesAdmin();

        $articles = $module->loadArticlesAdmin($pages, $page);

        $rootController->assign('pages', $pages);
        $rootController->assign('articles', $articles);
        $rootController->assign('page', $page);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('menu_group', $moduleConfiguration['module_group']);
        $rootController->setPageTitle( $moduleConfiguration['name'] ?? '');

        $rootController->displayPage( $moduleConfiguration['templates_dir'].'/list.html');
    }
}

if(!function_exists('showEditThumb')){
    function showEditThumb($id, $type, $key = 'photo') {
        global $module, $rootController, $moduleName, $moduleConfiguration;
        if(!$key)
            $key = 'photo';
        $article = $module->getArticleById($id);
        if ($article) {

            $size = $module->getThumbSize($type);
            $photo = $module->getPhotoInfo($article[$key]['name'], $article['id'], $key);

            $thumb_ratio = $size['width'] / $size['height'];
            $photo_ratio = $photo[$key]['size']['width'] / $photo[$key]['size']['height'];

            $thumb_conf = [];
            $thumb_conf['photo_width'] = $photo['photo']['size']['width'];
            $thumb_conf['photo_height'] = $photo['photo']['size']['height'];
            if ($thumb_ratio < $photo_ratio) {
                $thumb_conf['width'] = round($photo['photo']['size']['height'] * $thumb_ratio);
                $thumb_conf['height'] = $photo['photo']['size']['height'];
                $thumb_conf['x'] = ($photo['photo']['size']['width'] - $thumb_conf['width']) / 2;
                $thumb_conf['y'] = 0;
            } else {
                $thumb_conf['width'] = $photo['photo']['size']['width'];
                $thumb_conf['height'] = round($photo['photo']['size']['width'] / $thumb_ratio);
                $thumb_conf['x'] = 0;
                $thumb_conf['y'] = ($photo['photo']['size']['height'] - $thumb_conf['height']) / 2;
            }

            $preview = [];
            $preview['width'] = 150;
            $preview['height'] = round(150 / $thumb_ratio);

            $rootController->assign('photo_key', $key);
            $rootController->assign('size', $size);
            $rootController->assign('module_name', $moduleName);
            $rootController->assign('preview', $preview);
            $rootController->assign('thumb_conf', $thumb_conf);
            $rootController->assign('type', $type);
            $rootController->assign('item', $article);
            $rootController->display( 'root/thumb-edit.html');
        }
    }
}

if(!function_exists('showEditFile')){
    function showEditFile($id) {
        global $rootController, $files, $moduleName;

        $file  = $files->getFileById($id);

        $rootController->assign('module_name', $moduleName);
        $rootController->assign('file', $file);
        $rootController->display( 'root/file-edit.html');

    }
}

if(!function_exists('showSearch')){
    function showSearch($data, $page = 1){
        global $rootController, $module, $moduleName, $logger, $moduleConfiguration;

        if(!$module instanceof \Interfaces\ControllerInterface){
            $logger->pushHandler(new StreamHandler(ROOT_PATH . '/logs/admin-modules.log', Logger::ERROR));
            $logger->error(date('y-m-d H:i:s').' Access from '.$_SERVER['REQUEST_URI']);
            $rootController->redirectPage('admin/module.php?moduleName=strony');
        }
        $articles = $module->searchItems($data['keywords']);

        $pages = $module->getPagesAdmin();

        $rootController->assign('keywords', $data['keywords']);
        $rootController->assign('pages', $pages);
        $rootController->assign('articles', $articles);
        $rootController->assign('page', $page);
        $rootController->assign('module_name', $moduleName);
        $rootController->setPageTitle( $moduleConfiguration['name'] ?? '');

        $rootController->displayPage( $moduleConfiguration['templates_dir'].'/list.html');
    }
}

if(!function_exists('showEditBlock')){
    function showEditBlock($id)
    {
        global $module, $moduleName, $rootController, $blocksController;

        $types = $blocksController->loadTypes();
        $rootController->assign('types', $types);
        $rootController->assign('types', $types);
        $block = $module->getBlockById($id, false);

        $blockDescriptions = $module->getBlockDescription($id);

        $rootController->assign('descriptions', $blockDescriptions);
        $rootController->assign('block', $block);
        $rootController->assign('module_name', $moduleName);
        $rootController->display('blocks' .DIRECTORY_SEPARATOR.'block-edit.html');
        die;
    }
}

/**** END Load default functions ****/


switch ($action) {
    /**** Item actions ****/
    case 'add':

        showAdd();
        break;

    case 'create':

        if ($module->create($rootController->post->getAll(), $rootController->files->getAll())) {
            if($rootController->post->get('category_id'))
                $rootController->redirectPage('module.php?moduleName='.$moduleName.'&cat='.$rootController->post->get('category_id'));
            else
                $rootController->redirectPage('module.php?moduleName='.$moduleName);
        } else {
            showAdd();
        }
        break;

    case 'edit':

        if ($rootController->hasParameter('id')) {
            showEdit($rootController->getRequestParameter('id'), $tab);
        } else {
            $rootController->redirectPage('module.php?moduleName='.$moduleName);
        }
        break;

    case 'Zapisz':
        try{

            if ($module->update($rootController->post->getAll())) {
                if($module->editPhoto($rootController->post->getAll(), $rootController->files->getAll())){
                    $url = 'module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id') . '&tab=photo';
                    if($rootController->post->get('item_module'))
                        $url .= '&item_module='.$rootController->post->get('item_module').'&module_id='.$rootController->post->get('module_id').'&item_id='.$rootController->post->get('item_id');
                    $rootController->redirectPage($url);
                    //$rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id') . '&tab=photo');
                    die;
                }
                if($rootController->getRequestParameter('item_module'))
                    $rootController->redirectPage('module.php?moduleName=' . $rootController->getRequestParameter('item_module') . '&action=edit&id=' . $rootController->post->get('item_id').'&tab=blocks');
                elseif($moduleName == 'bloki')
                    $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=show_main');
                elseif($category_id = $rootController->getRequestParameter('category_id'))
                    $rootController->redirectPage('module.php?moduleName='.$moduleName.'&category_id='.$category_id);
                else
                    $rootController->redirectPage('module.php?moduleName='.$moduleName.'&cat='.$rootController->post->get('parent_id'));
            } else {
                $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id'));
            }
            break;
        }catch(Exception $e){
            dd($e->getMessage());
        }


    case 'Zapisz i kontynuuj edycję':

        if(isset($ajax) && $ajax){
            $result = array();
            parse_str($rootController->post->getAll()['formData'], $result);

            if($res = $module->update($result)){
                showResult($res, "zaktualizowano pomyślnie");
//                 $rootController->assign('info', "zaktualizowano pomyślnie");
//                 $rootController->display('misc/info.html');
            }
            else{
                showResult(false, $module->getError());
            }
            die();
        }
        $module->update($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id'));
        break;

    case 'set_active':
        if ($rootController->get->has('id')) {
            $module->setActive($rootController->get->get('id'), true);
            echo json_encode(['status'  => 'success']);
        }
        die();
        //$rootController->redirectPage('module.php?moduleName=' . $moduleName);
        //break;

    case 'set_inactive':
        if ($rootController->get->has('id')) {
            $module->setActive($rootController->get->get('id'), false);
            echo json_encode(['status'  => 'success']);
        }
        die();
        //$rootController->redirectPage('module.php?moduleName=' . $moduleName);
        //break;

    case 'duplicate':
        if ($rootController->get->has('id')) {
            showEdit($rootController->get->get('id'), $tab, true);
        } else {
            $rootController->redirectPage('module.php?moduleName=' . $moduleName);
        }
        break;
    case 'save_duplicate':
        $id = $rootController->post->get('id');

        if ($module->duplicate($rootController->post->getAll(), $files->loadFilesAdmin($id, $filesType), $module->getBlocksById($id, $filesType, true), $filesType)) {
            if($rootController->post->get('category_id'))
                $rootController->redirectPage('module.php?moduleName='.$moduleName.'&cat='.$rootController->post->get('category_id'));
            elseif($rootController->post->get('item_module')){
                $rootController->redirectPage('module.php?moduleName='.$rootController->getRequestParameter('item_module').'&action=edit&id='.$rootController->getRequestParameter('item_id').'&tab=blocks');
            }
            elseif($rootController->post->get('moduleName') == 'bloki'){
                $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=show_main');
            }
            else
                $rootController->redirectPage('module.php?moduleName='.$moduleName);
        } else {
            showAdd();
        }
        break;

    case 'delete':
        if ($rootController->hasParameter('id'))
            $module->delete($rootController->get->get('id'));

        $rootController->redirectPage('module.php?moduleName='.$moduleName);
        break;
    case 'massDelete':
        $module->massDelete($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName='.$moduleName);
        break;
    /***** END Item action ****/

    /** category actions **/
    /** category actions **/
    case 'category_add':

        showAdd('content', true);
        break;
    case 'category_create':

        if ($module->createCategory($rootController->post->getAll(), $rootController->files->getAll())) {
            if($rootController->post->get('category_id'))
                $rootController->redirectPage('module.php?moduleName='.$moduleName.'&category_id='.$rootController->post->get('category_id'));
            else
                $rootController->redirectPage('module.php?moduleName='.$moduleName);
        } else {
            showAdd('content', true);
        }
        break;
    case 'category_delete':
        if ($rootController->hasParameter('id'))
            if(!$module->deleteCategory($rootController->get->get('id')))
                echo json_encode([
                    'status'    => 'error',
                    'message'   => $module->getError(),
                ]);
        die;
    case 'category_edit':
        if ($rootController->hasParameter('id')) {
            showEditCategory($rootController->getRequestParameter('id'), $tab, false);
        } else {
            $rootController->redirectPage('module.php?moduleName='.$moduleName);
        }
        break;
    case 'category_save':
        try{

            if ($module->categoryUpdate($rootController->post->getAll())) {

                if($module->editCategoryPhoto($rootController->post->getAll(), $rootController->files->getAll())){

                    $rootController->redirectPage('module.php?moduleName='. $moduleName . '&action=category_edit&id=' . $rootController->post->get('id') . '&tab=photo');
                    die;
                }
                if($rootController->getRequestParameter('item_module')){
                    $rootController->redirectPage('module.php?moduleName=' . $rootController->getRequestParameter('item_module') . '&category_id=' . $rootController->post->get('item_id').'&tab=content');
                    die;
                }
                $category = $module->getCategoryById($rootController->post->get('id'));
                $rootController->redirectPage('module.php?moduleName='.$moduleName.'&category_id='.$category['parent_id']);
            } else {
                $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&category_id=' . $rootController->post->get('id'));
            }
            break;
        }catch(Exception $e){
            dd($e->getMessage());
        }
        die;
    case 'delete_category_photo':

        $module->deleteCategoryPhoto($rootController->get->get('id'));
        $rootController->redirectPage('module.php?moduleName='. $moduleName . '&action=category_edit&id=' . $rootController->get->get('id') . '&tab=photo');
        break;
    case 'add_category_photo':
        $module->editCategoryPhoto($rootController->post->getAll(), $rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=category_edit&id=' . $rootController->post->get('id') . '&tab=photo');
        break;
    case 'update_category_description':
        $module->updateCategoryDescription($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&category_id=' . $rootController->post->get('id'));
        break;


    /** END category actions **/
    /**** Photo Action ****/

    case 'create_thumb':
        if ($rootController->get->has('id')) {
            $module->createThumb($rootController->get->get('id'), $rootController->get->get('type'));
            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->get->get('id') . '&tab=photo');
        }
        break;

    case 'edit_thumb':
        if ($rootController->post->has('id')) {
            showEditThumb($rootController->post->get('id'), $rootController->post->get('type'), $rootController->post->get('key_type') ?? null);
        }
        break;

    case 'save_thumb':
        if ($rootController->post->has('id')) {
            $module->saveThumb($rootController->post->getAll());
            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id') . '&tab=photo');
        }
        break;
    case 'Dodaj zdjęcie':

        if($module->editPhoto($rootController->post->getAll(), $rootController->files->getAll())){
            $url = 'module.php?moduleName='. $moduleName . '&action=edit&id=' . $rootController->post->get('id') . '&tab=photo';
            if($rootController->post->get('item_module'))
                $url .= '&item_module='.$rootController->post->get('item_module').'&module_id='.$rootController->post->get('module_id').'&item_id='.$rootController->post->get('item_id');
            $rootController->redirectPage($url);
        }
        else
            $rootController->setError($module->getError(), true);
        die;

    case 'delete_photo':

        $module->deletePhoto($rootController->get->get('id'));
        $url = 'module.php?moduleName='. $moduleName . '&action=edit&id=' . $rootController->get->get('id') . '&tab=photo';
        if($rootController->get->get('item_module'))
            $url .= '&item_module='.$rootController->get->get('item_module').'&module_id='.$rootController->get->get('module_id').'&item_id='.$rootController->get->get('item_id');
        $rootController->redirectPage($url);
        die;
    case 'Dodaj wideo':
        $module->editVideo($rootController->post->getAll(), $rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id') . '&tab=video');
        die;
    case 'delete_video':
        $module->deleteVideo($rootController->get->get('id'));
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('id') . '&tab=video');
        die;

    /**** END Photo actions ****/

    /**** File actions ****/

    case 'add_plik':

        if($files->add($rootController->post->getAll(), $rootController->files->getAll())){
            /**
             * @TODO AJAX RESPONSE
             */

            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('parent_id') . '&tab=files');
            die();
        }
        else{

            $rootController->assign('error', $files->getError());
            $rootController->display('misc/error.html');
            die();
        }

    case 'edit_file':
        if ($rootController->post->has('id')) {
            showEditFile($rootController->post->get('id'));
        }
        die();
    case 'save_file':

        $files->updateFile($rootController->post->getAll(), $rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('parent_id') . '&tab=files');
        break;
    case 'delete_file':
        if ($rootController->get->has('id')) {
            if($files->delete($rootController->get->get('id'))){
                $rootController->setInfo('Pomyślnie usunięto');

            }
            else{
                // TODO obsługa błędu
            }
        }

        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->get->get('parent_id') . '&tab=files');
        die;
    case 'move_file':
        $files->move($rootController->post->get('id'), $rootController->post->get('order'));
        $rootController->redirectPage('module.php?moduleName='. $moduleName . '&action=edit&id=' . $rootController->post->get('parent_id') . '&tab=files');
        die();
    /**** END File actions ****/

    /**** List actions ****/
    case 'search':
        showSearch($rootController->post->getAll());
        die;
    case 'move':
        $module->move($rootController->post->get('id'), $rootController->post->get('order'));
        echo json_encode(['status'  => 'success']);
        die();
    /**** END List actions ****/
    /***BLOCKS***/
    case 'assign_blocks':

        $module->assignBlocks($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName='. $rootController->post->get('item_module') . '&action=edit&id=' . $rootController->post->get('item_id') . '&tab=content');
        die;
    case 'delete_block_assign':
        $module->deleteBlockAssign($rootController->get->getAll());
        $rootController->redirectPage('module.php?moduleName='. $rootController->get->get('moduleName') . '&action=edit&id=' . $rootController->post->get('parent_id') . '&tab=content');
        die;
    case 'move_block':
        $module->moveBlock($rootController->post->get('id'), $rootController->post->get('order'));
        die;
    case 'add_block':
        if($rootController->post->get('parent_id')){
            $module->createBlock($rootController->post->getAll());
        }
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=edit&id=' . $rootController->post->get('parent_id') . '&tab=content');
        die;
    case 'edit_block':
        if ($rootController->post->has('id')) {
           showEditBlock($rootController->post->get('id'));
        }
        die;
    case 'save_block':
        if($rootController->post->has('id')){
            $module->updateBlock($rootController->post->getAll());
        }
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=edit&id=' . $rootController->post->get('parent_id') . '&tab=content');
        die;
    case 'delete_block':

        if($rootController->hasParameter('id'))
            $module->deleteBlock($rootController->get->get('id'));
        $rootController->redirectPage('module.php?moduleName='.$moduleName.'&action=edit&id=' . $rootController->get->get('parent_id') . '&tab=content');
        die;
    case 'set_block_active':
        if ($rootController->get->has('id')) {
            $module->setBlockActive($rootController->get->get('id'), true);
            echo json_encode(['status'  => 'success']);
        }
        die();

    case 'set_block_inactive':
        if ($rootController->get->has('id')) {
            $module->setBlockActive($rootController->get->get('id'), false);
            echo json_encode(['status'  => 'success']);
        }
        die();

    /***END BLOCKS***/
    default:
        showArticles();
        break;
}



