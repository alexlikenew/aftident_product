<?php


namespace Controllers;
use Classes\Image;
use Models\Blocks;

class BlocksController extends Controller
{
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
    protected $typeDir;


    public function __construct($table = 'blocks'){
        $this->model = new Blocks($table);
        $this->module = 'bloki';
        parent::__construct($this->model, $table);
        $this->typeDir = $this->dir . DIRECTORY_SEPARATOR . 'types';
    }


    public function getItems($pages, $page = 1, $onlyActive = true, $forAdmin = null, $onlyUniversal = false, $typeId = false, $onlyParentOrAlone = false){

        if($forAdmin){
            $start = ($page - 1) * $this->limit_admin;

            $articles = $this->model->loadArticles($start, $this->limit_admin, $onlyActive, $onlyUniversal, $typeId, $onlyParentOrAlone);
        }
        else{
            $start = ($pages - $page) * $this->limit_page;
            $articles = $this->model->loadArticles($start, $this->limit_page, $onlyActive, $onlyUniversal, $typeId);
        }


        foreach($articles as $key=>$article) {
            $articles[$key]['content_short'] = strip_tags($articles[$key]['content_short']);

            $articles[$key]['url'] = BASE_URL . '/' . $this->module . '/' . $articles[$key]['title_url'];
            if ($articles[$key]['photo'])
                $articles[$key]['photo'] = $this->getPhotoUrl($articles[$key]['photo'], $articles[$key]['id']);
        }
        return $articles;
    }

    public function getUniversalBlocks($onlyActive = true, $toSkip = []){
        global $SEOCONF;
        $data = $this->model->getUniversalBlocks($onlyActive, $toSkip);

        $blocks = [];
        $galleryController = new GalleryController();
        $children = [];
        foreach($data as $block){
            if($block['photo'])
                $block['photo'] = $this->getPhotoUrl($block['photo'], $block['id']);
            if($block['video'])
                $block['video'] = $this->getVideoUrl($block['video'], $block['id'], $block['video_title']);

            if (!empty($block['gallery_id'])) {
                $gallery = $galleryController->getGalleryById($block['gallery_id']);


                if ($gallery['active'] == 1) {
                    $photos = $galleryController->getPhotos($block['gallery_id'], $SEOCONF['page_keywords']);

                    $gallery['photos'] = $photos;
                    $block['gallery'] = $gallery;
                } else {
                    unset($gallery);
                }
            }
            if($block['url_type'])
                $block['url'] = $this->getBlockUrl($block);
            if($block['parent_id'])
                $children[$block['parent_id']][] = $block;
            else
                $blocks[$block['template_name']][] = $block;
        }

        foreach($blocks as $key=>$type){
            if($type[0]['is_parent'])
                $blocks[$key][0]['items'] = $children[$type[0]['id']];
        }

        return $blocks;
    }

    public function getParentsByIds($moduleId, $itemId){
        $data = $this->model->getItemParentsBlocks($moduleId, $itemId);
        $blocks = [];
        foreach($data as $id)
            $blocks[] = $this->getBlockById($id['id']);
        return $blocks;
    }

    public function getLinkTypes() {
        $types = [];
        $types['url'] = Blocks::TYPE_URL;
        $types['page'] = Blocks::TYPE_PAGE;
        $types['module'] = Blocks::TYPE_MODULE;
        $types['offer'] = Blocks::TYPE_OFFER;
        return $types;
    }

    public function getChildrenById($id){
        $data = $this->model->getChildrensById($id);
        $blocks = [];
        foreach($data as $id)
            $blocks[] = $this->getArticleById($id['id']);

        return $blocks;

    }

    public function getTypePhotoUrl(string $filename = null, int $id = null): ?array
    {

        if (!empty($filename) && file_exists($this->typeDir. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename)) {
            $types = Image::getOtherAppends();

            $row = [];
            $row['name'] = $filename;
            $row['source']['photo'] =  $this->url .'/types/' . $id . '/' . $filename;
            $row['params'] = $this->getImageParams($this->typeDir. DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename);

            foreach($types as $type){
                $name = nameThumb($filename, $type['append'], $type['ext']);

                if(file_exists($this->typeDir . '/' . $id . '/' . $name))
                    $row['source'][$type['type']] =  $this->url . '/types/' . $id . '/' . $name;
            }

            return $row;
        } else {
            return null;
        }
    }
}