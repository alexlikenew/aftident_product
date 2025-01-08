<?php

namespace Traits;
use Classes\Image as ImageHelper;


trait File{
    protected $dir;

    public function createDir($id){

        if(!is_dir($this->dir)){
            mkdir($this->dir, 0755);
        }
        if(!is_dir($this->dir.DIRECTORY_SEPARATOR.$id)){
            return mkdir($this->dir.DIRECTORY_SEPARATOR.$id, 0755);
        }
        return true;
    }

    public function getItemFiles($id){
        $data = $this->model->getItemFiles($id, $this->module_id);
        $files = [];

        if($data){
            foreach($data as $file){
                $path = $this->dirFiles . DIRECTORY_SEPARATOR . $this->module_id . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $file['filename'];
                if(file_exists($path))
                    $info = pathinfo($path);
                $files[] = [
                    'url'       => $this->urlFiles.$this->module_id . '/' . $id . '/' . $file['filename'],
                    'name'      => $file['name'],
                    'size'      => (int)(filesize($path)/1024),
                    'extension' => $info['extension'],

                ];
            }
        }

        return $files;
    }
}