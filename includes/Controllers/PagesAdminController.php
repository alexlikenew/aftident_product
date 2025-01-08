<?php

namespace Controllers;

class PagesAdminController extends PagesController
{
    public function __construct($table = 'pages')
    {
        parent::__construct($table);
    }

    public function getMainPage(){
        $data = $this->model->getMainPage();
        if ($data) {
            if($data['photo'])
                $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
            if($data['video'])
                $data['video'] = $this->getVideoUrl($data['video'], $data['id'], $data['video_title']);
            return $data;
        } else {
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return false;
        }
    }

}