<?php
namespace Traits;
use Controllers\GalleryController;
use Controllers\ModuleController;
use Controllers\OfferController;
use Controllers\PagesController;
use Models\Blocks;

trait Block{

    public function getBlockDescription($id, $langId = null){
        $data = $this->model->getBlockDescription($id);
        if($langId)
            return $data;
        $descriptions = [];
        foreach($data as $desc){
            $descriptions[$desc['language_id']] = $desc;
        }
        return $descriptions;
    }

    public function getBlocksById($id, $typeId, $onlyActive = true, $onlyIds = false, $byType = false){

        global $SEOCONF;
        $data =  $this->model->getBlocksById($id, $typeId, $onlyActive, $onlyIds);
        $result = [];
        $galleryController = new GalleryController();
        $moduleController = new ModuleController();

        foreach($data as $key=>$block){

            if($block['photo'])
                $block['photo'] = $this->getPhotoUrl($block['photo'], $block['id'], true);
            if($block['video'])
                $block['video'] = $this->getVideoUrl($block['video'], $block['id'], $block['video_title'], true);

            if($block['url_type'])
                $block['url'] = $this->getBlockUrl($block);

            if($block['typePhoto'])
                $block['typePhoto'] = $this->getTypePhotoUrl($block['typePhoto'], $block['type_id']);

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
            /**
             * TODO blok modulu
             */

             if($block['module_id'] && $block['items_type'] && $block['quantity_to_load']){
                 $module = $moduleController->getArticleById($block['module_id']);

                 if($module['class_name']){
                     $className = '\Controllers\\'.$module['class_name'].'Controller';
                     $itemsController = new $className();
                     $module['items'] = $itemsController->getRandomItems($block['quantity_to_load']);

                 }
                 $block['module'] = $module;
             }
            /**
             *
             */
            if($byType)
                $result[$block['template_file']][] = $block;
            elseif($block['parent_id']){
                $result[$block['parent_id']]['items'][$block['id']] = $block;
            }
            elseif($block['is_agregated']){
                $result[$block['type_id']]['items'][] = $block;
                $result[$block['type_id']]['is_agregated'] = true;
                $result[$block['type_id']]['template_file'] = $block['template_file'];
            }
            elseif(isset($result[$block['id']]) && $result[$block['id']])
                $result[$block['id']] = array_merge($block, $result[$block['id']]);
            else
                $result[$block['id']] = $block;
        }

        return $result;
    }

    public function getBlockById($id){
        $data =  $this->model->getBlockById($id);
        if($data['photo'])
            $data['photo'] = $this->getPhotoUrl($data['photo'], $data['id']);
        if($data['video'])
            $data['video'] = $this->getVideoUrl($data['video'], $data['id'], $data['video_title']);
        return $data;
    }


    public function createBlock(array $data){

        $id = $this->model->createBlock($data);

        if($id){
            foreach($data['blockTitle'] AS $langId => $title){

                $this->model->saveBlockDescription([
                    'parent_id'     => $id,
                    'active'        => $data['lang_active'][$langId] ? 1 : 0,
                    'title'         => $title,
                    'content'       => $data['content'][$langId],
                    'language_id'   => $langId
                ]);
            }

            return $id;
        }
    }
    public function updateBlock(array $data){
        $item = $this->model->getBlockById($data['id'], false);

        if($item){
            foreach($data['blockTitle'] as $langId=>$title ){
                $desc = $this->model->getBlockDescription($data['id'], $langId);
                $descData = [
                    'parent_id'     => $data['id'],
                    'active'        => $data['lang_active'][$langId] ? 1 : 0,
                    'title'         => $title,
                    'content'       => $data['content'][$langId],
                    'language_id'   => $langId
                ];
                if($desc)
                    $this->model->updateBlockDescription($descData);
                else
                    $this->model->saveBlockDescription($descData);
            }

        }
    }

    public function setBlockActive($id, $isActive = true){
        return $this->model->setBlockActive($id, $isActive);
    }


    public function deleteBlock($id){
        $data = $this->model->getBlockById($id, false);

        if ($data){
            $this->model->deleteBlockDescriptions($data['id']);

            return $this->model->deleteBlock($id);
        }
    }

    public function assignBlocks($data){

        foreach($data['blocks'] AS $blockId){
            $id = $this->model->assignBlock($blockId, $data['item_id'], $data['module_id']);
            $order++;
        }

        return $id;
    }

    public function moveBlock($id, $order){

        $item = $this->model->getBlockAssignedData($id);

        $info = '';
        if ($item) {
            if ($item['order'] < $order) {
                $info = $this->model->moveBlockDown($item, $order);
            } else {
                $info = $this->model->moveBlockUp($item, $order);
            }

            $result = $this->model->updateBlockOrder($item['id'], $order);

            if ($result) {
                $this->setInfo($info);
                return true;
            }
        }
        return false;
    }

    public function deleteBlockAssign($data){
        return $this->model->deleteBlockAssign($data);
    }

    public function getBlockUrl($block){
        $result = null;

        switch($block['url_type']){
            case Blocks::TYPE_URL:
                $result = [
                    'title' => $block['url_title'],
                    'url'   => $block['url']
                ];
                break;
            case Blocks::TYPE_PAGE:
                $controller = new PagesController();

                $item = $controller->getArticleById($block['url_target1_id']);
                $result = [
                    'title' => $block['url_title'] ?? $item['title'],
                    'url' => $item['url'],
                ];

                break;
            case Blocks::TYPE_MODULE:
                $controller = new ModuleController();
                $item = $controller->getArticleById($block['url_target1_id']);

                $result = [
                    'title' => $item['title'],
                    'url' => $item['url'],
                ];
                break;
            case Blocks::TYPE_OFFER:
                $controller = new OfferController();
                $item = $controller->getArticleById($block['url_target1_id']);
                $result = [
                    'title' => $item['title'],
                    'url' => $item['url'],
                ];
                break;
        }
        return $result;
    }
}