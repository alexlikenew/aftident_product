<?php

namespace Controllers;

class MailTemplateAdminController extends MailTemplateController
{
    public function loadArticlesAdmin($pages, $page){
        return $this->getItems($pages, $page, false, true);
    }

    public function create($data, $files = null){
        $id = $this->model->create($data);

        if($id){
            $data['id'] = $id;
            foreach ($data['title'] as $i => $title) {

                $data['title'][$i] = prepareString($title, true);

                $this->model->createDescription($data, $i);
            }
        }
        return $id;
    }

    public function delete(int $id = null)
    {
        return $this->model->delete($id);
    }

    public function update(array $post): bool
    {

        $auth = isset($post['auth']) ? $post['auth'] : 0;
        $show_title = isset($post['show_title']) ? $post['show_title'] : 0;

        $comments = isset($post['comments']) ? $post['comments'] : 0;

        $post = array_merge($post, [
            'auth'          => $auth,
            'show_article'  => $show_title,
            'comments'      => $comments,
        ]);

        $result = $this->model->update($post, $post['id']);

        if ($result) {
            foreach ($post['title'] as $i => $title) {

                $post['title'][$i] = prepareString($title, true);

                $desc = $this->model->loadDescriptionById($post['id'], $i);

                if ($desc) {
                    $this->model->updateDescription($post, $i);

                } else {
                    $this->model->createDescription($post, $i);
                }
            }

            $this->setInfo($GLOBALS['_ADMIN_UPDATE_SUCCESS']);
            return true;
        } else {
            $this->setError($GLOBALS['_ADMIN_UPDATE_ERROR']);
            return false;
        }
    }

}