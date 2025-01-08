<?php

namespace Controllers;

class NewsletterAdminController extends NewsletterController
{
    public function __construct($table = 'newsletter')
    {
        parent::__construct($table);
    }

    function loadUsers($filter, $page = 0, $limit = 50) {
        $where = $this->getUsersFilter($filter);
        $order = $this->getUsersOrder($filter);
        if($page > 0)
            $page--;

        $data = $this->model->loadUsers($page, $limit, $where, $order);

        $users = [];
        foreach($data as $user) {
            $user['group_name'] = $this->loadGroup($user['grupa']);
            $users[] = $user;
        }

        return $users;
    }

    public function getUsersFilter($filter) {
        $where = ' 1=1 ';

        if (!empty($filter['first_name']))
            $where.= " AND LOWER(first_name) LIKE '" . strtolower($filter['first_name']) . "' ";
        if (!empty($filter['last_name']))
            $where.= " AND LOWER(last_name) LIKE '" . strtolower($filter['last_name']) . "' ";
        if (!empty($filter['email']))
            $where.= " AND LOWER(email) LIKE '" . strtolower($filter['email']) . "' ";
        if (isset($filter['active']) && $filter['active']){
            if($filter['active'] == 'active')
                $active = 1;
            else
                $active = 0;
            $where.= " AND active LIKE '" . $active . "' ";
        }

        return $where;
    }

    public function getUsersOrder($filter) {
        if (empty($filter['order_type']))
            $filter['order_type'] = 'ASC';
        if (empty($filter['order_field']))
            $filter['order_field'] = 'last_name ';
        return $filter['order_field'] . ' ' . $filter['order_type'];
    }

    public function addByAdmin($email, $name){
        return $this->model->addNewUser($email, $name);
    }

    public function loadTemplateAll() {

        $items = $this->model->getAllTemplates();

        if(is_array($items))
        {
            foreach($items as $key=>$val)
            {
                $items[$key]['queue_count'] = $this->getInQueueCount($val['id']);
            }
        }

        return $items;
    }

    public function addTemplate($post) {
        $result = $this->model->addTemplate($post);
        if ($result) {
            $this->art_id = $result;
            $this->setInfo($GLOBALS['_ADMIN_CREATE_SUCCESS']);
            return $this->art_id;
        } else {
            $this->setError($GLOBALS['_ADMIN_CREATE_ERROR']);
            return false;
        }
    }

    public function updateTemplate($data) {
        $result = $this->model->updateTemplate($data);
        if ($result) {
            $this->setInfo($GLOBALS['_USER_CHANGE_SAVE']);
            return true;
        } else {
            $this->setError($GLOBALS['_ADMIN_UPDATE_ERROR']);
            return false;
        }
    }
}