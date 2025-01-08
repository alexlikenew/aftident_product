<?php

namespace Controllers;

class ModuleAdminController extends ModuleController
{
    public function create(array $data, array $files = null)
    {
        $data['title_url'] = make_url($data['name']);
        $response = $this->model->create($data);
        if($response)
            $this->setInfo($GLOBALS['_ADMIN_CREATE_SUCCESS']);
        else
            $this->setError($GLOBALS['_ADMIN_CREATE_ERROR']);;

        return $response;
    }

    public function update(array $post): bool
    {

        foreach($post['title'] as $key=>$title){
            $post['title'][$key] = prepareString($title, true);
        }

        $post['title_url'] = prepareString($post['title_url'], true);
        $post['table_name'] = prepareString($post['table_name'], true);
        $post['class_name'] = prepareString($post['class_name'], true);
        $post['class_file'] = prepareString($post['class_file'], true);
        $post['templates_dir_name'] = prepareString($post['templates_dir_name'], true);

        $frontend = isset($post['frontend']) ? $post['frontend'] : 0;
        $backend = isset($post['backend']) ? $post['backend'] : 0;
        $active = isset($post['active']) ? $post['active'] : 0;
        $auth = isset($post['auth']) ? $post['auth'] : 0;

        $post = array_merge($post, [
            'auth'          => $auth,
            'frontend'      => $frontend,
            'backend'       => $backend,
            'active'        => $active,
        ]);
        $post['title_url'] = make_url($post['name']);
        $result = $this->model->update($post, $post['id']);
        if ($result) {
            foreach ($post['page_title'] as $i => $title) {
                $post['page_title'][$i] = prepareString($post['page_title'][$i]);
                $post['page_keywords'][$i] = prepareString($post['page_keywords'][$i]);
                $post['page_description'][$i] = prepareString($post['page_description'][$i]);
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

    public function delete(int $id = null){
        return $this->model->delete($id);
    }

}