<?php

if (!defined('SCRIPT_CHECK')) {
    die('No-Hack here buddy.. ;)');
}

use Controllers\GalleryAdminController;

$user->CheckPrivileges('galeria_administration');
$rootController->assign('menu_group', 'pages');
$module = new GalleryAdminController('gallery');
$moduleName = 'galeria';

switch ($action) {
    case 'Usuń zaznaczone z galerii':
        if ($rootController->post->has('pliki')) {
            $module->deleteSelected($rootController->post->get('gallery_id'), $rootController->post->get('pliki'));
        }
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('gallery_id'));
    case 'move_photo':
        $module->movePhoto($rootController->post->get('id'), $rootController->post->get('order'));

        echo json_encode(['status'  => 'success']);
        die();

    case 'add_photos':
        $module->addPhotos($rootController->post->get('gallery_id'), $rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('gallery_id'));
        break;

    case 'zmien_opis':
        $module->changeDescription($rootController->post->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('gallery_id'));
        break;

    case 'edit_thumb':
        showEditThumb($rootController->post->get('id'));
        die();

    case 'save_thumb':
        if ($rootController->post->has('foto_id')) {
            $module->saveThumb($rootController->post->getAll());
            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('gallery_id'));
        }
        die;

    case 'save_photo':
        if ($rootController->post->has('foto_id')) {
            $module->changeDescription($rootController->post->getAll());
            $module->saveThumb($rootController->post->getAll());
            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('gallery_id'));
        }
        break;

    case 'delete_photo':
        $response = $module->deleteSelected($rootController->post->get('parent_id'), $rootController->post->get('new_foto'));
        echo json_encode($response);
        die;

    case 'Porzuć zmiany':
        if ($rootController->post->has('return')) {
            if ($rootController->post->get('return') == 'list') {
                $rootController->redirectPage($moduleName . '.php');
            } else {
                $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('gallery_id'));
            }
        } else {
            $rootController->redirectPage('module.pl?moduleName=' . $moduleName );
        }
        die;
    case 'move':
        $module->move($rootController->post->get('id'), $rootController->post->get('order'));
        echo json_encode(['status'  => 'success']);
        die();

}


function showAdd($tab = 'content') {
    global $rootController, $configController, $module, $moduleName;

    $opcje['thumb_height_default'] = $configController->getOption('thumb_height_default');
    $opcje['thumb_width_default'] = $configController->getOption('thumb_width_default');
    $opcje['op_page_title'] = $configController->getOption('op_page_title');
    $opcje['op_page_keywords'] = $configController->getOption('op_page_keywords');
    $opcje['op_page_description'] = $configController->getOption('op_page_description');

    $mainLang = $configController->getLangMain();

    $rootController->assign('lang_main', $mainLang);

    $rootController->assign('opcje', $opcje);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('zakladka_selected', $tab);
    $rootController->setPageTitle('Nowa galeria zdjęć');
    $rootController->displayPage('galleries/add.html');
}

function showEditThumb($id) {
    global $module, $moduleName, $rootController, $configController;

    $photo = $module->getPhoto($id);

    if ($photo) {
        $size = $module->getThumbSize($photo['gallery_id']);
        $thumb_ratio = $size['width'] / $size['height'];
        $photo_ratio = $photo['photo']['size']['width'] / $photo['photo']['size']['height'];
        $opis = $module->getPhotoDescription($id);
        $gallery = $module->getGalleryById($photo['gallery_id']);

        $mainLang = $configController->getLangMain();

        $rootController->assign('lang_main', $mainLang);

        $thumb_conf = array();
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

        $preview = array();
        $preview['width'] = 150;
        $preview['height'] = round(150 / $thumb_ratio);
        $rootController->assign('module_name', $moduleName);
        $rootController->assign('opis', $opis);
        $rootController->assign('size', $size);
        $rootController->assign('preview', $preview);
        $rootController->assign('thumb_conf', $thumb_conf);
        $rootController->assign('gallery', $gallery);
        $rootController->assign('photo', $photo);
        $rootController->display('galleries/thumb-edit.html');
    }
}

function showEdit($id) {
    global $module, $rootController, $moduleName, $back, $configController;

    $gallery = $module->getGalleryById($id);
    $photos = $module->getPhotosAdmin($id);

    if (isset($_SERVER['SystemRoot'])) {
        if (preg_match('/WINDOWS/i', $_SERVER['SystemRoot'])) {
            $path = BASE_URL;
        } else {
            $path = ROOT_PATH;
        }
    } else {
        $path = ROOT_PATH;
    }

    $opis = $module->getGalleryDescription($id);

    $mainLang = $configController->getLangMain();
    $rootController->assign('lang_main', $mainLang);
    $rootController->assign('back', $back);
    $rootController->assign('opis', $opis);
    $rootController->assign("path", $path);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('gallery_url', $module->getUrl());
    $rootController->assign("gallery_dir", "gallery");
    $rootController->assign('lista_ile_row', ceil(600 / $gallery['width']));
    $rootController->assign('gallery', $gallery);
    $rootController->assign('photos', $photos);
    $rootController->assign('menu_selected', 'galeria');
    $rootController->setPageTitle('Zarządzanie galerią zdjęć');
    $rootController->displayPage('galleries/edit.html');
}

