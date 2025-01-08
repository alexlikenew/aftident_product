<?php

namespace Controllers;

use Classes\Upload\UploadManager;
use Models\Contact;

class ContactController extends Controller
{
    const CONTACT_MESSAGE = 1;
    const JOB_MESSAGE = 2;
    const OFFER_MESSAGE = 3;
    protected $model;
    protected $module;
    protected $scale_width;
    protected $scale_height;
    protected $list_height;
    protected $list_width;
    protected $detail_width;
    protected $detail_height;
    protected $main_width;
    protected $main_height;


    public function __construct($table = 'contact_messages'){
        $this->model = new Contact($table);
        $this->module = 'kontakt';
        parent::__construct($this->model, $table);
    }

    public function saveContact(array $data, $type = 1, $files = null): int{

        if($data[1])
            $data['name'] = $data[1];
        if($data[2])
            $data['email'] = $data[2];
        $data['type'] = $type;
        $id = $this->model->create($data);
        if($files['file']){
            $this->saveFile($id, $files['file']);
        }
        return $id;
    }

    public function saveFile($id, $file){
        $dir = $this->dir . DIRECTORY_SEPARATOR . $id;
        $this->createDir($id);
        $fileObj = UploadManager::get('file');
        if (!$fileObj->isOk()){
            $this->setError($fileObj->getErrorAsString());
            return false;
        }
        $file_types = explode(',', ConfigController::getOptionStatic('files_types'));

        foreach ($file_types as $key => $value) {
            $file_types[$key] = trim($value);
        }

        if (!call_user_func_array(array($fileObj, 'isValidExt'), $file_types)) {
            $this->setError($GLOBALS['_FILE_WRONG_EXTENSION']);
            return false;
        }

        $file_size = ConfigController::getOptionStatic('files_max_size');

        if (!$fileObj->isValidSize($file_size . " KB")) {
            $this->setError($GLOBALS['_FILE_TO_BIG']);
            return false;
        }

        $name = changeFilename($id, '', $file['name']);

        if(move_uploaded_file($file['tmp_name'], $dir . DIRECTORY_SEPARATOR . $name)) {
            $this->model->updateFileName($id, $name);

            $this->setInfo($GLOBALS['_FILE_CREATE_SUCCESS']);
            return true;
        }
        else{
            $this->setError($GLOBALS['_FILE_UPLOAD_ERROR']);
            return false;
        }
    }

}