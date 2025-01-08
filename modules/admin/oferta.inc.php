<?php

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');


use Controllers\OfferAdminController;

$user->CheckPrivileges('aktualnosci_administration');
$rootController->assign('menu_group', 'Zarządzanie stroną');
$moduleName = 'oferta'; // nazwa modulu, tabela w bazie, link w adresie
$module = new OfferAdminController();



switch($action){
    case 'create':

        if ($module->create($rootController->post->getAll(), $rootController->files->getAll())) {
            if($rootController->post->get('parent_id'))
                $rootController->redirectPage('module.php?moduleName='.$moduleName.'&cat='.$rootController->post->get('parent_id'));
            else
                $rootController->redirectPage('module.php?moduleName='.$moduleName);
        } else {
            showAdd();
        }
        die;
}


if (!function_exists('showArticles')) {
    function showArticles()
    {
        global $module, $page, $rootController, $moduleName, $moduleConfiguration, $logger, $parentId;

        $articles = $module->getByPid($parentId);


        foreach ($articles as $key => $item) {
            $articles[$key]['url'] = '/'.$item['title_url'];
        }

        $rootController->assign('category_jump', $module->createHtmlSelect('cat', 'id', $parentId, 'this.form.submit();'));
        $rootController->assign('articles', $articles);
        $rootController->assign('path', $module->getPathByPid($parentId));

        $upperPid = empty($cat) ? 'none' : $module->getParentIdByPid($cat);

        $rootController->assign('page', $page);
        //$rootController->assign('pages', $pages);

        $rootController->assign('query_string', "&cat=" . $cat);

        $category_select = $module->createHtmlSelect("id_category", 'id', 0);
        $rootController->assign('category_select', $category_select);

        $rootController->setPageTitle('Zarządzanie kategoriami');
        $rootController->assign('menu_selected', 'offer');
        $rootController->assign('menu_group', $moduleConfiguration['module_group']);
        $rootController->assign('module_name', $moduleName);
        $rootController->displayPage($moduleConfiguration['templates_dir'].'/list.html');
    }
}

if(!function_exists('showAdd')){
    function showAdd($tab = 'content') {
        global $module, $gallery, $configController, $rootController, $moduleName, $moduleConfiguration, $ajax, $parentId;


        if($parentId){
            $path = $module->getPath($module->getPathById($parentId));
            array_pop($path);
            $rootController->assign('path', $path);
        }


        $opcje['op_page_title'] = $configController->getOption('op_page_title');
        $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
        $opcje['op_page_description'] = $configController->getOption('op_page_description');
        $mainLang = $configController->getLangMain();

        $scale = $module->getScaleSize();
        $rootController->assign('parent_id', $parentId);
        $rootController->assign('lang_main', $mainLang);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('scale', $scale);
        $rootController->assign('opcje', $opcje);
        $rootController->assign('galleries', $gallery->loadGalleriesNames());
        $rootController->assign('menu_selected', $moduleName);
        $rootController->assign('tab_selected', $tab);
        $rootController->setPageTitle('Nowy element');

        $rootController->displayPage( $moduleConfiguration['templates_dir'].'/add.html');
    }
}

if(!function_exists('showEdit')){
    function showEdit($id, $tab = 'content', $duplicate = false) {

        global $module, $gallery, $files, $filesType, $rootController, $moduleName, $configController, $moduleConfiguration, $ajax, $blocksController;

        $article = $module->getArticleById($id);

        if ($article) {
            if($article['parent_id']){
                $path = $module->getPath($module->getPathById($article['id']));
                array_pop($path);
                $rootController->assign('path', $path);

            }

            $galleries = $gallery->loadGalleriesNames();
            $description = $module->loadDescriptionById($id);

            $scale = $module->getScaleSize();
            $files = $files->loadFilesAdmin($article['id'], $filesType);

            $blocks = $module->getBlocksById($id, $filesType, false);

            $blocksIds = [];
            foreach($blocks as $block){
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


?>