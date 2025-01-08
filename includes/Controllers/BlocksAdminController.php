<?php


namespace Controllers;


use Classes\Image;

class BlocksAdminController extends BlocksController
{
    public function __construct($table = 'blocks')
    {
        parent::__construct($table);
    }

    public function getPagesAdmin($pageId = false){

        return $this->getPages(false, true, $pageId);
    }

    public function getPages($onlyActive = false, $forAdmin = false, $category_str = null, $category_id = null){

        $count = $this->model->getPages($onlyActive, $pageId);

        if ($count < 1) {
            $count = 1;
        }
        if($forAdmin)
            return ceil($count / $this->limit_admin);
        else
            return ceil($count / $this->limit_page);
    }

    public function loadArticlesAdmin($pages, $page, $onlyUniversal = false, $typeId = null, $onlyParentOrAlone = false ){

        return $this->getItems($pages, $page, false, true, $onlyUniversal, $typeId, $onlyParentOrAlone);
    }

    public function move(int $id, int $order_new): bool{
        $item = $this->getArticleById($id);

        if($item)
            return  $this->model->updateItemsOrder($item, $order_new);

        return false;
    }

    public function loadTypes($custom_type = null){
        $data =  $this->model->getAllTypes($custom_type);
        $result = [];
        foreach($data as $item){
            if($item['photo'])
                $item['photo'] = $this->getTypePhotoUrl($item['photo'], $item['id']);
            $result[] = $item;
        }
        return $result;
    }

    public function getQuantityByType(){
        $data = $this->model->getQuantityByType();
        $result = [];
        foreach($data as $item)
            $result[$item['type_id']] = $item['quantity'];

        return $result;
    }

    public function createType(array $data, array $files = null){

        $id = $this->model->createType($data);
        if($files['photo'] && isset($files['photo']['name']) && $files['photo']['name']){
            $data['id'] = $id;
            $this->editTypePhoto($data, $files);
        }

        return $id;
    }

    public function editTypePhoto($post, $files){

        $article = $this->getTypeById($post['id']);

        if (!empty($files['photo']['name'])) {

            $this->deletePhoto($post['id']);
            $filename = $this->createPhotoName($article, $files, 'photo');

            if (empty($filename)) {
                $this->setError($GLOBALS['_ADMIN_PHOTO_ERROR']);
                return false;
            }

            $this->createTypeDir($post['id']);

            $oImage = new Image($this->typeDir.DIRECTORY_SEPARATOR.$post['id']);

            if ($oImage->UploadFile('photo', $filename)) {


                $oImage->ScaleImage($this->scale_width, $this->scale_height);
                $oImage->saveWebP($filename);
                $oImage->createBlurImage($filename);
                $oImage->ThumbFromCenter($this->list_width, $this->list_height, '_l');
                $oImage->ThumbFromCenter($this->detail_width, $this->detail_height, '_d');
                $oImage->ThumbFromCenter($this->main_width, $this->main_height, '_m');

                if($this->model->updateTypePhoto($post['id'], $filename))
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

    public function createTypeDir($id){

        if(!is_dir($this->typeDir)){
            mkdir($this->typeDir, 0755);
        }
        if(!is_dir($this->typeDir.DIRECTORY_SEPARATOR.$id)){
            return mkdir($this->typeDir.DIRECTORY_SEPARATOR.$id, 0755);
        }
        return true;
    }

    public function updateType(array $data, array $files = null){

        $result = $this->model->updateType($data);
        if($files['photo'])
            $this->editTypePhoto($data, $files);
        return $result;
    }

    public function deleteType(int $id){
        $this->deleteTypePhoto($id);
        return $this->model->deleteType($id);
    }

    public function typeHasItems($id){
        return $this->model->typeHasItems($id);
    }

    public function getTypeById($id){
        $data =  $this->model->getTypeById($id);
        if($data['photo'])
            $data['photo'] = $this->getTypePhotoUrl($data['photo'], $data['id']);
        return $data;
    }

    public function changeBlockType($id, $typeId){
        return $this->model->changeType($id, $typeId);
    }

    public function create(array $data, array $files = null){
        $id = parent::create($data, $files);
        if($id && $data['item_id'] && $data['module_id'])
            $this->assignBlocks([
                'item_id'   => $data['item_id'],
                'module_id' => $data['module_id'],
                'blocks'    => [$id],
            ]);
        return $id;
    }

    public function delete(int $id = null){
        parent::delete($id);
        return $this->model->deleteBlockAssigns($id);
    }

    public function deleteTypePhoto($id){
        $filename = $this->model->getTypePhoto($id);
        $other = Image::getOtherAppends();
        if (file_exists($this->typeDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename) and !empty($filename))
            unlink($this->typeDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $filename);
        foreach($other as $type){
            $otherName = nameThumb($filename, $type['append'], $type['ext']);

            if (file_exists($this->typeDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $otherName) and !empty($otherName))
                unlink($this->typeDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $otherName);
        }

        $this->model->deleteTypePhoto($id);

        return true;
    }


}
