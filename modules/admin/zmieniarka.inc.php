<?php
if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

use Controllers\SliderAdminController;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$user->CheckPrivileges('galeria_administration');
$rootController->assign('menu_group', 'pages');
$module = new SliderAdminController();
$moduleName = 'zmieniarka';



switch($action){
    case 'add_photos':

        $module->addPhotos($rootController->post->get('slider_id'), $rootController->files->getAll());
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('slider_id'));
        break;
    case 'edit_thumb':
        showEditThumb($rootController->post->get('id'));
        die();
    case 'save_photo':
        if ($rootController->post->has('foto_id')) {
            $module->changeDescription($rootController->post->getAll());
            $module->saveThumb($rootController->post->getAll());
            $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('slider_id'));
        }
        break;
    case 'delete_photo':
        $response = $module->deleteSelected($rootController->post->get('parent_id'), $rootController->post->get('new_foto'));
        echo json_encode($response);
        die;
    case 'move_photo':
        $module->movePhoto($rootController->post->get('id'), $rootController->post->get('order'));
        echo json_encode(['status'  => 'success']);
        die();
    case 'delete_photos':

        $response = $module->deleteSelected($rootController->post->get('slider_id'), $rootController->post->get('pliki'));
        $rootController->redirectPage('module.php?moduleName=' . $moduleName . '&action=edit&id=' . $rootController->post->get('slider_id'));
        die;
}

function showEditThumb($id) {
    global $module, $moduleName, $rootController, $configController;

    $photo = $module->getPhoto($id);

    if ($photo) {
        $size = $module->getThumbSize($photo['slider_id']);

        $thumb_ratio = $size['width'] / $size['height'];
        $photo_ratio = $photo['photo']['size']['width'] / $photo['photo']['size']['height'];
        $opis = $module->getPhotoDescription($id);

        $gallery = $module->getSliderById($photo['slider_id']);

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
        $rootController->display('sliders/thumb-edit.html');
    }
}

function showEdit($id, $tab = 'main') {
    global $module, $rootController, $moduleName, $configController, $moduleConfiguration;

    $slider = $module->getArticleById($id);

    $photos = $module->getPhotos($id, '', false);

    if (isset($_SERVER['SystemRoot'])) {
        if (preg_match('/WINDOWS/i', $_SERVER['SystemRoot'])) {
            $path = BASE_URL;
        } else {
            $path = ROOT_PATH;
        }
    } else {
        $path = ROOT_PATH;
    }

    $mainLang = $configController->getLangMain();

    $rootController->assign('lang_main', $mainLang);
    $rootController->assign("path", $path);
    $rootController->assign('module_name', $moduleName);
    $rootController->assign('slider_url', $module->getUrl());
    $rootController->assign("slider_dir", "slider");
    if($slider['width'])
        $rootController->assign('lista_ile_row', ceil(600 / $slider['width']));
    else
        $rootController->assign('lista_ile_row', 600);
    $rootController->assign('slider', $slider);
    $rootController->assign('photos', $photos);
    $rootController->assign('menu_selected', 'zmieniarka');
    $rootController->setPageTitle('Zarządzanie zmieniarkami');
    $rootController->displayPage('sliders/edit.html');
}

?>