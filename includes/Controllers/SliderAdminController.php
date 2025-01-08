<?php


namespace Controllers;


use Classes\Image;

class SliderAdminController extends SliderController
{
    public function __construct($table = 'slider')
    {
        parent::__construct($table);
    }

    public function addPhotos($id,  $files){

        $sliderConfig = $this->model->getById($id);
        $this->thumb_width = $sliderConfig['width'];
        $this->thumb_height = $sliderConfig['height'];
        $this->watermark = $sliderConfig['watermark'];
        $this->watermark_file = $sliderConfig['watermark_file'];
        $this->watermark_x = $sliderConfig['watermark_x'];
        $this->watermark_y = $sliderConfig['watermark_y'];
        $this->watermark_position = $sliderConfig['watermark_position'];

        $this->createDir($id);

        foreach($files['photo']['tmp_name'] as $key=>$photo){
            try{
                if (move_uploaded_file($files['photo']['tmp_name'][$key], $this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $files['photo']['name'][$key])) {

                    $oImage = new Image($this->dir . DIRECTORY_SEPARATOR . $id);
                    $oImage->setFile($files['photo']['name'][$key]);
                    $oImage->ScaleImage($this->scale_width, $this->scale_height);
                    $oImage->createBlurImage($files['photo']['name'][$key]);
                    $oImage->ThumbFromCenter($this->thumb_width, $this->thumb_height, '_s');

                    if ($this->watermark) {
                        $filename_s = nameThumb($files['photo']['name'][$key], '_s');
                        $source = $this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename_s;
                        $this->AddWatermark($source, $this->dir . DIRECTORY_SEPARATOR . 'watermark' . DIRECTORY_SEPARATOR . $this->watermark_file, $this->watermark_x, $this->watermark_y, $this->watermark_position);
                    }

                    $this->model->addPhotoToSlider($id, $files, $key);
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

    public function deleteAllPhotos($id){

        $photos = $this->model->getAllPhotos($id);

        foreach ($photos as $photo) {
            $min_name = nameThumb($photo['name'], "_s");
            unlink($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $min_name);
            $this->model->deletePhotoDescription($photo['id']);
        }

        $affectedRow = $this->model->deleteAllSliderPhotos($id);

        $info = $GLOBALS['_ADMIN_PHOTOS_DELETE'];
        $info = str_replace('#PHOTOS', $affectedRow, $info);
        $this->setInfo($info);
        return true;
    }

    public function delete(int $id = null){
        $this->deleteAllPhotos($id);
        $this->model->deleteSlider($id);
        deleteDir($this->dir . DIRECTORY_SEPARATOR . $id);
        $this->setInfo($GLOBALS['_ADMIN_DELETE_SUCCESS']);
        return true;
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

    public function getPhoto($id) {

        $data = $this->model->getPhotoData($id);

        if ($data['id']) {
            if (file_exists($this->dir . DIRECTORY_SEPARATOR . $data['slider_id'] . DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR . $data['name'])) {
                $data['photo']['src'] = $this->url . DIRECTORY_SEPARATOR . $data['slider_id'] . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $data['name'];
                $temp = getimagesize($this->dir . DIRECTORY_SEPARATOR . $data['slider_id'] . DIRECTORY_SEPARATOR. DIRECTORY_SEPARATOR .  $data['name']);
                $data['photo']['size']['width'] = $temp[0];
                $data['photo']['size']['height'] = $temp[1];
                $data['photo']['name'] = $data['name'];
            }

            $filename = nameThumb($data['name'], "_s");
            if (file_exists($this->dir . DIRECTORY_SEPARATOR . $data['slider_id'] . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $filename)) {
                $data['small']['src'] = $this->url . DIRECTORY_SEPARATOR . $data['slider_id'] . DIRECTORY_SEPARATOR . $filename;
                $temp = getimagesize($this->dir . DIRECTORY_SEPARATOR . $data['slider_id'] .DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $filename);
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
        if($photo){
            $config = $this->getSliderById($post['slider_id']);
            $oImage = new Image($this->dir . DIRECTORY_SEPARATOR . $post['slider_id']);
            $oImage->setFile($photo['name']);
            $oImage->Thumb($post['x'], $post['y'], $post['x2'], $post['y2'], $config['width'], $config['height'], '_s');

            return true;
        }
        return false;
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

    public static function getOtherAppends(){
        return [
            ['type' =>  'blur',         'append'    => '_blur',  'ext' => false],
            ['type' =>  'photo_webp',   'append'    => '',       'ext' => 'webp'],
            ['type' =>  'small',        'append'    => '_s',     'ext' => 'webp'],
            ['type' =>  'blur_webp',    'append'    => '_blur',  'ext' => 'webp'],

        ];
    }



}