<?php

namespace Traits;
use Classes\Image as ImageHelper;

trait Image{

    public function editPhoto($post, $files){

        $article = $this->getArticleById($post['id']);

        if (!empty($files['photo']['name'])) {

            $this->deletePhoto($post['id']);
            $filename = $this->createPhotoName($article, $files, 'photo');

            if (empty($filename)) {
                $this->setError($GLOBALS['_ADMIN_PHOTO_ERROR']);
                return false;
            }

            $this->createDir($post['id']);

            $oImage = new ImageHelper($this->dir.DIRECTORY_SEPARATOR.$post['id']);

            if ($oImage->UploadFile('photo', $filename)) {


                $oImage->ScaleImage($this->scale_width, $this->scale_height);
                $oImage->saveWebP($filename);
                $oImage->createBlurImage($filename);
                $oImage->ThumbFromCenter($this->list_width, $this->list_height, '_l');
                $oImage->ThumbFromCenter($this->detail_width, $this->detail_height, '_d');
                $oImage->ThumbFromCenter($this->main_width, $this->main_height, '_m');

                if($this->model->updatePhoto($post['id'], $filename))
                {

                    $this->setInfo($GLOBALS['_ADMIN_PHOTO_SUCCESS']);
                    return true;
                }
                else{
                    $this->setError($GLOBALS['_ADMIN_PHOTO_ERROR']);
                    return false;
                }
            }
            return false;
        }
    }

    public function createPhotoName($article, $files, $key){

        return isset($article['title_url'])
            ? changeFilename($article['title_url'] . "-" . rand(0, 1000), '', $files[$key]['name'])
            : '';

    }

    public function getPhotoInfo($filename, $id, $key = 'photo'){

        $row = [];
        if (file_exists($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename) and !empty($filename)) {
            $row[$key]['path'] = $this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename;
            $temp = getimagesize($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename);
            $row[$key]['size']['width'] = $temp[0];
            $row[$key]['size']['height'] = $temp[1];
            $row['name'] = $filename;
            $types = Image::getOtherAppends();
            foreach($types as $type){
                $name = nameThumb($filename, $type['append'], $type['ext']);

                if (file_exists($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $name) and !empty($name)) {
                    $row[$type['type']]['path'] = $this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $name;
                    $temp = getimagesize($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $name);
                    $row[$type['type']]['size']['width'] = $temp[0];
                    $row[$type['type']]['size']['height'] = $temp[1];
                }
            }

        }
        return $row;
    }

    public function saveThumb($post) {
        $article = $this->getArticleById($post['id']);
        $oImage = new ImageHelper($this->dir . DIRECTORY_SEPARATOR . $post['id']);
        $oImage->SetFile($article['photo']['name']);
        switch ($post['type']) {
            case 'list':
                $oImage->Thumb($post['x'], $post['y'], $post['x2'], $post['y2'], $this->list_width, $this->list_height, '_l');
                break;
            case 'detail':
                $oImage->Thumb($post['x'], $post['y'], $post['x2'], $post['y2'], $this->detail_width, $this->detail_height, '_d');
                break;
            case 'main':
                $oImage->Thumb($post['x'], $post['y'], $post['x2'], $post['y2'], $this->main_width, $this->main_height, '_m');
                break;

            default:
                break;
        }
    }

    public function getThumbSize($type){
        $thumb = array();
        switch ($type) {
            case 'list':
                $thumb['width'] = $this->list_width;
                $thumb['height'] = $this->list_height;
                break;
            case 'detail':
                $thumb['width'] = $this->detail_width;
                $thumb['height'] = $this->detail_height;
                break;
            case 'main':
                $thumb['width'] = $this->main_width;
                $thumb['height'] = $this->main_height;
                break;
        }

        return $thumb;
    }

    public function createThumb($id, $type) {
        $article = $this->getArticleById($id);
        $oImage = new ImageHelper($this->dir . DIRECTORY_SEPARATOR . $id);
        $oImage->SetFile($article['photo']['name']);

        switch ($type) {
            case 'list':
                $oImage->ThumbFromCenter($this->list_width, $this->list_height, '_l');
                break;
            case 'detail':
                $oImage->ThumbFromCenter($this->detail_width, $this->detail_height, '_d');
                break;
            case 'main':
                $oImage->ThumbFromCenter($this->main_width, $this->main_height, '_m');
                break;

            default:
                break;
        }
    }

    public function getScaleSize(){
        return [
            'width' => $this->scale_width,
            'height' => $this->scale_height,
        ];
    }

    public function AddWatermark($sourcefile, $watermarkfile, $start_x = 0, $start_y = 0, $pozycja = 1) {
        //Get the resource ids of the pictures
        $watermarkfile_id = imagecreatefrompng($watermarkfile);

        imageAlphaBlending($watermarkfile_id, false);
        imageSaveAlpha($watermarkfile_id, true);

        $fileType = strtolower(substr($sourcefile, strlen($sourcefile) - 3));

        switch ($fileType) {
            case('gif'):
                $sourcefile_id = imagecreatefromgif($sourcefile);
                break;

            case('png'):
                $sourcefile_id = imagecreatefrompng($sourcefile);
                break;

            default:
                $sourcefile_id = imagecreatefromjpeg($sourcefile);
        }

        //Get the sizes of both pix
        $sourcefile_width = imageSX($sourcefile_id);
        $sourcefile_height = imageSY($sourcefile_id);
        $watermarkfile_width = imageSX($watermarkfile_id);
        $watermarkfile_height = imageSY($watermarkfile_id);


        if ($pozycja == 1) {
            $dest_x = $start_x;
            $dest_y = $start_y;
        } elseif ($pozycja == 2) {
            $dest_x = $sourcefile_width - $watermarkfile_width - $start_x;
            $dest_y = $start_y;
        } elseif ($pozycja == 3) {
            $dest_x = $start_x;
            $dest_y = $sourcefile_height - $watermarkfile_height - $start_y;
        } elseif ($pozycja == 4) {
            $dest_x = $sourcefile_width - $watermarkfile_width - $start_x;
            $dest_y = $sourcefile_height - $watermarkfile_height - $start_y;
        }

        // if a gif, we have to upsample it to a truecolor image
        if ($fileType == 'gif') {
            // create an empty truecolor container
            $tempimage = imagecreatetruecolor($sourcefile_width, $sourcefile_height);

            // copy the 8-bit gif into the truecolor image
            imagecopy($tempimage, $sourcefile_id, 0, 0, 0, 0, $sourcefile_width, $sourcefile_height);

            // copy the source_id int
            $sourcefile_id = $tempimage;
        }

        imagecopy($sourcefile_id, $watermarkfile_id, $dest_x, $dest_y, 0, 0, $watermarkfile_width, $watermarkfile_height);

        //Create a jpeg out of the modified picture

        switch ($fileType) {

            // remember we don't need gif any more, so we use only png or jpeg.
            // See the upsaple code immediately above to see how we handle gifs
            case('png'):
                imagepng($sourcefile_id, $sourcefile);
                break;

            default:
                imagejpeg($sourcefile_id, $sourcefile, 100);
        }


        imagedestroy($sourcefile_id);
        imagedestroy($watermarkfile_id);
    }

    public function countArticle($id) {
        return $this->model->countArticle($id);
    }

    public function getImageParams($path){
        $params = getimagesize($path);
        $nameArray = explode('.', $path);
        $extension = array_pop($nameArray);

        return [
            'width'     => $params[0],
            'height'    => $params[1],
            'type'      => $params['mime'],
            'extension' => $extension
        ];
    }

    public static function getOtherAppends(){
        return ImageHelper::getOtherAppends();
    }

    public function getTypePhotoUrl(string $filename = null, int $id = null): ?array
    {

        if (!empty($filename) && file_exists($this->dirBlocks.DIRECTORY_SEPARATOR.'types'. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename)) {
            $types = Image::getOtherAppends();

            $row = [];
            $row['name'] = $filename;
            $row['source']['photo'] =  $this->urlBlocks  .'types/' . $id . '/' . $filename;
            $row['params'] = $this->getImageParams($this->dirBlocks.DIRECTORY_SEPARATOR.'types'. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename);

            foreach($types as $type){
                $name = nameThumb($filename, $type['append'], $type['ext']);

                if(file_exists($this->dir . '/' . $id . '/' . $name))
                    $row['source'][$type['type']] =  $this->urlBlocks  . 'types/' . $id . '/' . $name;
            }

            return $row;
        } else {
            return null;
        }
    }

    public function deletePhoto($id){
        $filename = $this->model->getPhoto($id);
        $other = Image::getOtherAppends();
        if (file_exists($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename) and !empty($filename))
            unlink($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename);
        foreach($other as $type){
            $otherName = nameThumb($filename, $type['append'], $type['ext']);

            if (file_exists($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $otherName) and !empty($otherName))
                unlink($this->dir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $otherName);
        }

        $this->model->deletePhoto($id);


        return true;
    }

    public function duplicatePhotos($id, $photos){
        $this->createDir($id);

        foreach($photos['source'] as $photo){
            $photoArray = explode('/', $photo);
            $name = array_pop($photoArray);
            array_pop($photoArray);

            $destination = implode('/', $photoArray);
            $destination .= DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $name;

            copy(ROOT_PATH.$photo, ROOT_PATH.$destination);
        }
        return $this->model->updatePhoto($id, $photos['name']);
    }

}
