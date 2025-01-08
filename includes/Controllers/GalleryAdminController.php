<?php

namespace Controllers;
use Classes\Image;

class GalleryAdminController extends GalleryController
{
    public function __construct($table = 'gallery')
    {
        parent::__construct($table);
    }

    public function loadArticlesAdmin($pages, $page){
        $result = $this->model->loadGaleries(false);

        foreach ($result as $key=>$value) {

            $result[$key]['photo'] = $this->loadRandomPhoto($value['id']);

            $rresult[$key]['url'] =  $this->module . '/' . $value['title_url'] ;
            $result[$key]['content_short'] = strip_tags($value['content_short']);
        }

        return $result;
    }

    public function create(array $data, ?array $files = null){

        $id = $this->model->create($data);

            if ($id) {
                if (!is_dir($this->dir)) {
                    $old_umask = umask(0);
                    mkdir($this->dir, 0755);
                    umask($old_umask);
                }

                if (!is_dir($this->dir . DIRECTORY_SEPARATOR . $id)) {
                    $old_umask = umask(0);
                    mkdir($this->dir . DIRECTORY_SEPARATOR . $id, 0755);
                    umask($old_umask);
                }

                $this->model->saveDescription($id, $data);

                return [
                    'message'=> $this->setInfo($GLOBALS['_ADMIN_CREATE_SUCCESS']),
                    'status'    => true
                    ];
            } else {
                return [
                    'message'   => $this->setError($GLOBALS['_ADMIN_CREATE_ERROR']),
                    'status'    => false
                ];
            }


    }

    public function delete(int $id = null){
        $this->deleteAllPhotos($id);

        $galleryOrder = $this->model->getGalleryOrder($id);

        $this->model->deleteGallery($id);

        $this->model->updateOrder($galleryOrder);

        deleteDir($this->dir . DIRECTORY_SEPARATOR . $id);

        $this->setInfo($GLOBALS['_ADMIN_DELETE_SUCCESS']);
        return true;
    }

    public function deleteAllPhotos($id){
        $photos = $this->model->getAllPhotos($id);
        foreach ($photos as $photo) {
            $min_name = nameThumb($photo['name'], "_s");
            unlink($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $min_name);
            $this->model->deletePhotoDescription($photo['id']);

        }

        $affectedRow = $this->model->deleteAllGalleryPhotos($id);

        $info = $GLOBALS['_ADMIN_PHOTOS_DELETE'];
        $info = str_replace('#PHOTOS', $affectedRow, $info);
        $this->setInfo($info);
        return true;
    }

    public function getPhotosAdmin($id){

        $photos = $this->model->getGalleryPhotos($id);

        foreach($photos as $key=>$photo) {
            $photos[$key] = $this->getPhotoUrl($photo['name'], $id);
            $photos[$key]['id'] = $photo['id'];
        }

        return $photos;
    }

    public function update(array $data):bool{

        if ($this->model->updateGallery($data)) {
            foreach ($data['title'] as $i => $title) {
                $data['title'][$i] = prepareString($title, true);
                $data['content_short'][$i] = prepareString($data['content_short'][$i], true);
                $lang = ConfigController::loadLangById($i);
                if ($lang['gen_title']) {
                    $data['title_url'][$i] = ConfigController::makeUniqueUrl(make_url($data['title'][$i]), $this->model->getTableDescription(), "title_url", $i, $data['id']);
                } else {
                    $data['title_url'][$i] = $data['title_url'][lang_main] . '-' . $lang['code'];
                }

                $lang_active = (isset($data['lang_active'][$i])  && $data['lang_active'][$i])? 1 : 0;

                $this->updateOrCreateDescription($data, $lang_active, $i);
            }

            //$url = '/' . $this->modul . '/' . $post['title_url'][lang_main] . '.html';
            //$this->rejestr->addWpis($post['title'][$i], $url, 'zmieniono', $this->modul);

            $this->setInfo($GLOBALS['_ADMIN_UPDATE_SUCCESS']);
            return true;
        } else {
            $this->setError($GLOBALS['_ADMIN_UPDATE_ERROR']);
            return false;
        }
    }

    public function updateOrCreateDescription($data, $activeLang, $langId){
        if ($this->model->checkDescriptionExist($data['id'], $langId)) {
            $this->model->updateGalleryDescription($data, $activeLang, $langId);
        } else {
            $this->model->createGalleryDescription($data, $activeLang, $langId);
        }
    }

    public function addPhotos($id,  $files){
        $GalleryConfig = $this->getGalleryConfig($id);
        $this->thumb_width = $GalleryConfig['width'];
        $this->thumb_height = $GalleryConfig['height'];
        $this->watermark = $GalleryConfig['watermark'];
        $this->watermark_file = $GalleryConfig['watermark_file'];
        $this->watermark_x = $GalleryConfig['watermark_x'];
        $this->watermark_y = $GalleryConfig['watermark_y'];
        $this->watermark_position = $GalleryConfig['watermark_position'];

        $this->createDir($id);

        foreach($files['photo']['tmp_name'] as $key=>$photo){
            try{

                if (move_uploaded_file($files['photo']['tmp_name'][$key], $this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $files['photo']['name'][$key])) {

                    $oImage = new Image($this->dir . DIRECTORY_SEPARATOR . $id);

                    $oImage->setFile($files['photo']['name'][$key]);
                    $oImage->saveWebP($files['photo']['name'][$key]);
                    $oImage->ScaleImage($this->scale_width, $this->scale_height);
                    $oImage->createBlurImage($files['photo']['name'][$key]);
                    $oImage->ThumbFromCenter($this->thumb_width, $this->thumb_height, '_s');

                    if ($this->watermark) {
                        $filename_s = nameThumb($files['photo']['name'][$key], '_s');
                        $source = $this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename_s;
                        $this->AddWatermark($source, $this->dir . DIRECTORY_SEPARATOR . 'watermark' . DIRECTORY_SEPARATOR . $this->watermark_file, $this->watermark_x, $this->watermark_y, $this->watermark_position);
                    }

                    $this->model->addPhotoToGallery($id, $files, $key);

                }
                else{
                    $error = 'Nie można zapisać zdjęcia!';
                }
            }catch(\Exception $e){
                $error = $e->getMessage();
            }

        }

        if (empty($error)) {
            return true;
        } else {

            $this->setError($error);
            return false;
        }
    }


    public function deleteSelected($galleryId, $fileIds){
        if (is_array($fileIds)) {
            $success = false;
            foreach ($fileIds as $id) {
                $photoData = $this->model->getPhotoData($id);
                if ($photoData) {

                    if ($this->model->deletePhoto($id) > 0) {

                        $this->model->deletePhotoDescription($id);

                        unlink($this->dir . DIRECTORY_SEPARATOR . $galleryId . DIRECTORY_SEPARATOR . $photoData['name']);

                        $types = self::getOtherAppends();
                        foreach($types as $type){
                            $name = nameThumb($photoData['name'], $type['append'], $type['ext']);
                            unlink($this->dir . DIRECTORY_SEPARATOR . $galleryId . DIRECTORY_SEPARATOR . $name);
                        }

                        $this->model->updateGalleryPhotosOrder($galleryId, $photoData['order']);
                        $success = true;
                    } else {
                        $success = false;
                    }
                }
            }
            if ($success) {
                $this->setInfo($GLOBALS['_ADMIN_PHOTOS_DETACHED']);
                return true;
            }
        } else {
            return false;
        }

    }

    public function getPhoto($id) {

        $data = $this->model->getPhotoData($id);

        if ($data['id']) {
            if (file_exists($this->dir . DIRECTORY_SEPARATOR . $data['gallery_id'] . DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR . $data['name'])) {
                $data['photo']['src'] = $this->url . DIRECTORY_SEPARATOR . $data['gallery_id'] . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $data['name'];
                $temp = getimagesize($this->dir . DIRECTORY_SEPARATOR . $data['gallery_id'] . DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR .  $data['name']);
                $data['photo']['size']['width'] = $temp[0];
                $data['photo']['size']['height'] = $temp[1];
                $data['photo']['name'] = $data['name'];
            }

            $filename = nameThumb($data['name'], "_s");
            if (file_exists($this->dir . DIRECTORY_SEPARATOR . $data['gallery_id'] . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $filename)) {
                $data['small']['src'] = $this->url . DIRECTORY_SEPARATOR . $data['gallery_id'] . DIRECTORY_SEPARATOR . $filename;
                $temp = getimagesize($this->dir . DIRECTORY_SEPARATOR . $data['gallery_id'] .DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $filename);
                $data['small']['size']['width'] = $temp[0];
                $data['small']['size']['height'] = $temp[1];
            }

            return $data;
        }

        return false;
    }

    public function getPhotoDescription($id){
        $data = $this->model->getPhotoDescription($id);
        $descriptions = [];
        foreach($data as $desc) {
            $descriptions[$desc['language_id']] = $desc;
        }
        return $descriptions;
    }

    public function changeDescription($post){

        if (!$post['foto_id'])
            return false;

        foreach ($post['title'] as $i => $title) {
            $post['title'][$i] = prepareString($title, true);
            $desc = $this->model->getPhotoDescription($post['foto_id'], $i);
            if($desc)
                $this->model->updatePhotoDescription($post, $i);
            else
                $this->model->createPhotoDescription($post, $i);
        }

        return true;

    }

    public function saveThumb($post){
        if (!$post['foto_id'])
            return false;

        $photo = $this->getPhoto($post['foto_id']);

        $config = $this->getGalleryConfig($post['gallery_id']);
        $oImage = new Image($this->dir . DIRECTORY_SEPARATOR . $post['gallery_id']);
        $oImage->setFile($photo['name']);
        $oImage->Thumb($post['x'], $post['y'], $post['x2'], $post['y2'], $config['width'], $config['height'], '_s');

        return true;
    }

    function movePhoto($id, $order_new) {
        $item = $this->getPhoto($id);

        if ($item)
            return $this->model->updatePhotoOrder($item, $order_new);

        return false;
    }

    public function move(int $id, int $order_new): bool{
        $gallery = $this->getGalleryById($id);

        if($gallery){
            return $this->model->updateItemsOrder($gallery, $order_new);
        }
        return false;
    }

}
