<?php

namespace Controllers;
use Models\Gallery;

class GalleryController extends Controller
{
    protected $model;
    protected $module;
    protected $dir;
    protected $url;
    protected $thumb_width;
    protected $thumb_height;
    protected $watermark;
    protected $watermark_file;
    protected $watermark_x;
    protected $watermark_y;
    protected $watermark_position;
    protected $scale_width;
    protected $scale_height;

    public function __construct($table = 'gallery'){
        $this->model = new Gallery($table);
        $this->module = 'galeria';
        parent::__construct($this->model, $table);

        $this->url = BASE_URL.'/upload/'.$table;
        $this->dir = ROOT_PATH . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . $table;
        $this->scale_width = 1024;
        $this->scale_height = 728;

    }

    public function loadGalleries(){

        $data = $this->model->loadGaleries();

        foreach($data as $key=>$gallery) {
            $data[$key]['photo'] = $this->loadRandomPhoto($gallery['id']);
            $data[$key]['url'] = BASE_URL . '/' . $this->module . '/' . $gallery['title_url'];
            $data[$key]['content_short'] = strip_tags($gallery['content_short']);
        }

        return $data;
    }


    public function getUrl(){
        return $this->url;
    }

    public function loadRandomPhoto($id){
        $data = $this->model->getRandomPhoto($id);

        if ($data) {

            $data['src'] = $this->getPhotoUrl($data['name'], $id);

            return $data;
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

    public function loadGallery(string $name = null, $id = null): ?array{
        $data = $this->model->loadGallery($name, $id);

        if($data){
            $data = $this->prepare($data, $this->module);
            $data['url'] =  $this->url . '/' . $data['title_url'];

            return $data;
        }
        else{
            $this->setError($GLOBALS['_PAGE_NOT_EXIST']);
            return null;
        }
    }

    public function loadGalleriesNames($langId = null): ?array {
        return $this->model->loadGalleriesName($langId);
    }

    public function getMaxOrder($type = null, $id = null): ?int{
        return $this->model->getMaoxOrder($type, $id);
    }

    public function getGalleryById(int $id){

       return $this->model->getGalleryById($id);
    }

    public function getGalleryDescription($id){
        $descriptions = $this->model->getGalleryDescription($id);

        $result = [];

        foreach($descriptions as $desc){
            $result[$desc['language_id']] = $desc;

        }

        return $result;

    }

    public function getGalleryConfig($id){
        return $this->model->getGalleryConfig($id);
    }

    public function getThumbSize($id) {
        $thumb = [];

        $config = $this->getGalleryConfig($id);
        $thumb['width'] = $config['width'];
        $thumb['height'] = $config['height'];

        return $thumb;
    }

    public function getPhotos($gallery_id = 0, $page_keywords = '') {
        if (!empty($page_keywords)) {
            $anchor = explode(',', $page_keywords);
            $liczba = count($anchor);
        }

        $result = $this->model->getPhotos($gallery_id);

        $items = [];
        foreach($result as $row) {
            if (!empty($page_keywords)) {
                $row['anchor'] = trim($anchor[rand(0, $liczba - 1)]);
            }
            $row['src'] = $this->getPhotoUrl($row['name'], $gallery_id);
            if ($row['src']) {
                $items[] = $row;
            }
        }
        return $items;
    }

    public function searchItems(string $keywords): array{
        $result = $this->model->searchItems($keywords);
        foreach($result as $item){
            $item['photo'] = $this->getPhotoUrl($item['photo'], $item['id']);
        }

        $items = [];
        return $items;
    }

}