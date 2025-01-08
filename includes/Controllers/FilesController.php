<?php

namespace Controllers;
use Models\Files;
class FilesController extends Controller{
    protected $model;

    public function __construct($table = 'files'){
        $this->model = new Files($table);
        parent::__construct($this->model, $table);
    }

    public function getFileById($id){
        $file = $this->model->getById($id);

        if ($file) {
            $file['url'] = $this->url . '/' . $file['parent_type'] . '/' . $file['parent_id'] . '/' . $file['filename'];
            $descData = $this->model->getDescriptionsById($file['id']);
            foreach($descData as $desc){
                $file['opis'][$desc['language_id']] = $desc ;
            }

            $nameArray = explode('.', $file['filename']);
            $extension = end($nameArray);

            if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp']))
                $file['type'] = 'image';
            elseif(in_array($extension, ['mpg', 'avi', 'mp4' ]))
                $file['type'] = 'video';
            elseif($extension == 'pdf')
                $file['type'] = 'pdf';
            else
                $file['type'] = 'other';

            $file['extension'] = end($nameArray);
            return $file;
        }
        return false;
    }

    public function loadFiles($parent_id, $parent_type){
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
}
