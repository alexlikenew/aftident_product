<?php

namespace Controllers;

class OfferAdminController extends OfferController
{
    public function __construct($table = 'offers')
    {
        parent::__construct($table);
    }


    public function create(array $post, array $files = null) {

        $post['order'] = $this->getMaxOrder(id:$post['category_id']) + 1;
        $post['path_id'] = $this->getPathById($post['category_id']);
        $post['depth'] = (empty($post['path_id']) ? 0 : substr_count($post['path_id'], '.')) + 1;

        $id = parent::create($post, $files);

        if($id){
            $this->model->updatePath($id, $post['parent_id']);
        }
        return $id;
    }

}