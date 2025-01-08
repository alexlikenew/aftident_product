<?php

namespace Controllers;

use Classes\Upload\UploadException;
use Classes\Upload\UploadManager;

class FilesAdminController extends FilesController{
    public function __construct($table = 'files')
    {
        parent::__construct($table);
    }

    public function loadFilesAdmin($parent_id, $parent_type){
        $data = $this->model->getFilesAdmin($parent_id, $parent_type);

        $files = [];
        foreach($data as $file) {
            if(file_exists($this->dir . DIRECTORY_SEPARATOR . $file['parent_type'] . DIRECTORY_SEPARATOR . $file['parent_id'] . DIRECTORY_SEPARATOR . $file['filename']))
            $file['url'] = $this->url . '/' . $file['parent_type'] . '/' . $file['parent_id'] . '/' . $file['filename'];
            $file['opis'] = $this->loadDescriptionById($file['id']);
            $files[] = $file;
        }

        return $files;
    }

    public function createDir( $name){
        if(!is_dir($this->dir)){
            mkdir($this->dir, 0755);
        }
        if(!is_dir($this->dir.DIRECTORY_SEPARATOR.$name)){
            return mkdir($this->dir.DIRECTORY_SEPARATOR.$name, 0755);
        }
        return true;
    }

    public function duplicateItemFiles($parent_id, $type, $files){

        $dir = $this->dir . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $parent_id;
        $this->createDir($type);
        $this->createDir($type . DIRECTORY_SEPARATOR . $parent_id);

        foreach($files as $file){

            copy(ROOT_PATH.$file['url'], $dir.DIRECTORY_SEPARATOR.$file['filename']);

            $order = $this->getMaxOrder($type, $parent_id) + 1;

            $id = $this->model->add([
                'parent_id'     => $parent_id,
                'parent_type'   => $type
            ], $order);

            if(copy(ROOT_PATH.$file['url'], $dir.DIRECTORY_SEPARATOR.$file['filename'])) {
                $this->model->updateFileName($id, $file['filename']);
                foreach ($file['opis'] as $i => $name) {
                    $this->model->addFileDescription($id, $i, $name['name']);
                }

                $this->setInfo($GLOBALS['_FILE_CREATE_SUCCESS']);
                return true;
            }
            else{
                $this->setError($GLOBALS['_FILE_UPLOAD_ERROR']);
                return false;
            }
        }
    }

    public function add($post, $files) {

        $dir = $this->dir . DIRECTORY_SEPARATOR . $post['parent_type'] . DIRECTORY_SEPARATOR . $post['parent_id'];
        $this->createDir($post['parent_type'] );
        $this->createDir($post['parent_type'] . DIRECTORY_SEPARATOR . $post['parent_id']);

        $order = $this->getMaxOrder($post['parent_type'], $post['parent_id']) + 1;
        $id = $this->model->add($post, $order);

        if ($id) {
            $this->article_id = $id;
            if (!empty($files['plik']['name'])) {
                $oFile = UploadManager::get('plik');
                if (!$oFile->isOk()){
                    $this->setError($oFile->getErrorAsString());
                    return false;
                }
                $file_types = explode(',', ConfigController::getOptionStatic('files_types'));

                foreach ($file_types as $key => $value) {
                    $file_types[$key] = trim($value);
                }

                if (!call_user_func_array(array($oFile, 'isValidExt'), $file_types)) {
                    $this->setError($GLOBALS['_FILE_WRONG_EXTENSION']);
                    $this->deleteFromDB($id);
                    return false;
                }

                $file_size = ConfigController::getOptionStatic('files_max_size');

                if (!$oFile->isValidSize($file_size . " KB")) {
                    $this->setError($GLOBALS['_FILE_TO_BIG']);
                    return false;
                }

                $name = changeFilename($post['name'][LANG_MAIN], '', $files['plik']['name']);
                $filename = $this->uniqueFilename($name, $post['parent_id'], $post['parent_type']);
                if(move_uploaded_file($files['plik']['tmp_name'], $dir . DIRECTORY_SEPARATOR . $filename)) {
                    $this->model->updateFileName($id, $filename);
                    foreach ($post['name'] as $i => $name) {
                        $this->model->addFileDescription($id, $i, $name, $post['lang_active'][$i] ? 1 : 0);
                    }

                    $this->setInfo($GLOBALS['_FILE_CREATE_SUCCESS']);
                    return true;
                }
                else{
                    $this->setError($GLOBALS['_FILE_UPLOAD_ERROR']);
                    return false;
                }
            }
        } else {
            $this->setError($GLOBALS['_FILE_CREATE_ERROR']);
            return false;
        }
        return false;
    }

    public function uniqueFilename($filename, $parent_id, $parent_type) {
        $new_filename = $filename;
        $dir = $this->dir . DIRECTORY_SEPARATOR . $parent_type . DIRECTORY_SEPARATOR . $parent_id;

        $index = 1;
        while (file_exists($dir . DIRECTORY_SEPARATOR . $new_filename)) {
            $lastDot = strrpos($filename, '.');
            $ext = substr($filename, $lastDot, strlen($filename));
            $front = substr($filename, 0, $lastDot);
            $name = str_replace(' ', '_', strip_pl($front));
            $new_filename = strtolower($name . '(' . $index . ')' . $ext);
            $index++;
        }

        return $new_filename;
    }

    public function deleteFromDB($id){
        $this->model->delete($id);
    }

    public function delete(int $id = null){
        $file = $this->model->getById($id);

        if ($file) {
            $dir = $this->dir . DIRECTORY_SEPARATOR . $file['parent_type'] . DIRECTORY_SEPARATOR . $file['parent_id'];

            if (!empty($file['filename'])) {
                if (file_exists($dir . DIRECTORY_SEPARATOR . $file['filename']))
                    unlink($dir . DIRECTORY_SEPARATOR . $file['filename']);

                $this->deleteFromDB($id);
            }
            return true;
        }
        $this->setError('File don\'t exists!' );
        return false;

    }

    public function getMaxOrder($type = null, $id = null ){

        return $this->model->getMaxOrder($type, $id);
    }

    public function move(int $id, int $order):bool{
        $file = $this->model->getById($id);
        if($file)
            return $this->model->move($file, $order);
        return false;
    }

    public function updateFile(array $post, array $files): bool{
        $success = true;
        $dir = $this->dir . DIRECTORY_SEPARATOR . $post['parent_type'] . DIRECTORY_SEPARATOR . $post['parent_id'];
        $this->createDir($post['parent_type'] );
        $this->createDir($post['parent_type'] . DIRECTORY_SEPARATOR . $post['parent_id']);

        if (!empty($files['plik']['name'])) {
            $oFile = UploadManager::get('plik');
            if (!$oFile->isOk()){
                $this->setError($oFile->getErrorAsString());
                return false;
            }
            $file_types = explode(',', ConfigController::getOptionStatic('files_types'));

            foreach ($file_types as $key => $value) {
                $file_types[$key] = trim($value);
            }

            if (!call_user_func_array(array($oFile, 'isValidExt'), $file_types)) {
                $this->setError($GLOBALS['_FILE_WRONG_EXTENSION']);
                return false;
            }

            $file_size = ConfigController::getOptionStatic('files_max_size');

            if (!$oFile->isValidSize($file_size . " KB")) {
                $this->setError($GLOBALS['_FILE_TO_BIG']);
                return false;
            }

            $name = changeFilename($post['name'][LANG_MAIN], '', $files['plik']['name']);
            $filename = $this->uniqueFilename($name, $post['parent_id'], $post['parent_type']);
            if(move_uploaded_file($files['plik']['tmp_name'], $dir . DIRECTORY_SEPARATOR . $filename))
                $this->model->updateFileName($post['id'], $filename);
            else
                $success = false;
        }

        foreach ($post['name'] as $i => $name) {
            $this->model->updateFileDescription($post, $i);
        }
        if($success)
            $this->setInfo($GLOBALS['_FILE_UPDATE_SUCCESS']);
        else
            $this->setError($GLOBALS['_FILE_UPDATE_ERROR']);

        return $success;

    }

}
