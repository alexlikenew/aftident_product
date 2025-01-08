<?php

namespace Controllers;


class UsersAdminController extends UsersController{

    public function __construct($table = 'users'){
        parent::__construct($table);
    }

    public function getUsers($filter, $page = 0, $limit = 50){
        if (empty($limit)) {
            $limit = $this->limit_admin;
        }
        $where = $this->getUsersFilter($filter);
        $order = $this->getUsersOrder($filter);

        $users = $this->model->getUsers($where, $order, $page, $limit);

        return $users;
    }

    public function getUsersFilter($filter) {
        $where = array();
        $where['query'] = ' 1=1 ';
        $where['params'] = array();
        if (!empty($filter['login'])) {
            $where['query'] .= " AND LOWER(login) LIKE CONCAT('%', ?, '%') ";
            $where['params'][] = array(dbStatement::STRING, strtolower($filter['login']));
        }
        if (!empty($filter['firm_name'])) {
            $where['query'] .= " AND LOWER(firm_name) LIKE CONCAT('%', ?, '%') ";
            $where['params'][] = array(dbStatement::STRING, strtolower($filter['firm_name']));
        }
        if (!empty($filter['first_name'])) {
            $where['query'] .= " AND LOWER(first_name) LIKE CONCAT('%', ?, '%') ";
            $where['params'][] = array(dbStatement::STRING, strtolower($filter['first_name']));
        }
        if (!empty($filter['last_name'])) {
            $where['query'] .= " AND LOWER(last_name) LIKE CONCAT('%', ?, '%') ";
            $where['params'][] = array(dbStatement::STRING, strtolower($filter['last_name']));
        }
        if (!empty($filter['email'])) {
            $where['query'] .= " AND LOWER(email) LIKE CONCAT('%', ?, '%') ";
            $where['params'][] = array(dbStatement::STRING, strtolower($filter['email']));
        }
        if (isset($filter['business'])) {
            $where['query'] .= " AND business=? ";
            $where['params'][] = array(dbStatement::INTEGER, $filter['business']);
        }
        if (isset($filter['active']) && $filter['active'] != '%') {
            $where['query'] .= " AND active=? ";
            $where['params'][] = array(dbStatement::INTEGER, $filter['active']);
        }
        if (isset($filter['admin_login']) && $filter['admin_login'] != '%') {
            $where['query'] .= " AND admin_login=? ";
            $where['params'][] = array(dbStatement::INTEGER, $filter['admin_login']);
        }
        if (isset($filter['group_id']) && $filter['group_id'] != '%') {
            $where['query'] .= " AND group_id=? ";
            $where['params'][] = array(dbStatement::INTEGER, $filter['group_id']);
        }

        return $where;
    }

    /* funkcja zwraca klauzule ORDER dla zapytania SQL na podstawie filtru */

    public function getUsersOrder($filter) {
        if (empty($filter['order_type']))
            $filter['order_type'] = 'ASC';
        if (empty($filter['order_field']))
            $filter['order_field'] = 'last_name, firm_name';
        return $filter['order_field'] . ' ' . $filter['order_type'];
    }

    function getUsersPages($filter, $limit) {
        if (empty($limit)) {
            $limit = $this->limit_admin;
        }
        $where = $this->getUsersFilter($filter);
        $count = $this->model->getUsersPages($where);
        if ($count < 1) {
            $count = 1;
        }
        return ceil($count / $limit);
    }

    public function getUserById($id){
        return $this->model->getUserById($id);
    }

    public function getGroups(){
        return $this->model->getGroups();
    }

    public function update($user):bool{
        $search = array('-', ' ', '/', '(', ')');
        $replace = array('', '', '', '', '');

        $user['nip'] = str_replace($search, $replace, $user['nip']);
        $user['phone'] = str_replace($search, $replace, $user['phone']);

        foreach ($user as $k => $v) {
            $user[$k] = strip_tags($v);
        }

        if ($user['business'] == 2 and empty($user['firm_name'])) {
            $this->setError($GLOBALS['_USER_EMPTY_FIRM_NAME']);
            return false;
        } elseif ($user['business'] == 2 and !$this->checkNIP($user['nip'])) {
            $this->setError($GLOBALS['_USER_EMPTY_NIP']);
            return false;
        } else {
            $result = $this->model->update($user);

            if ($result) {
                $this->setInfo($GLOBALS['_USER_CHANGE_SAVE']);
                return true;
            }
        }
        return false;
    }

    public function getPrivileges(){

        return $this->model->getPrivileges();
    }

    public function getGroupPrivileges($id) {
        $result = $this->model->getGroupPrivileges($id);
        if ($result) {
            $group_privileges = explode('|', $result);
            $privileges = $this->getPrivileges();
            for ($i = 0; $i < count($privileges); $i++) {
                $all_privileges[$i] = $privileges[$i];
                $all_privileges[$i]['status'] = in_array($privileges[$i]['id'], $group_privileges) ? 1 : 0;
            }
            return $all_privileges;
        }
    }

    public function getGroupName($id){
        return stripslashes($this->model->getGroupName($id));
    }

    public function updateGroup($id, $privileges){
        if(!$privileges)
            $privileges = [];
        sort($privileges);
        $groups = implode('|', $privileges);

        $result = $this->model->updateGroup($id, $groups);

        if ($result) {
            $this->setInfo($GLOBALS['_USER_CHANGE_SAVE']);
            return true;
        } else {
            $this->setError($GLOBALS['_USER_NOT_SAVED']);
            return false;
        }
    }

    public function reloadPrivileges(){

        $privileges = $this->model->loadUserPrivileges($_SESSION['user']['id']);
        $_SESSION['privileges_hash'] = md5(serialize($privileges));
        $_SESSION['user']['privileges'] = $privileges;
        return true;
    }

    public function addGroup($group){
        if (!$this->model->groupExists($group['name'])) {

            sort($group['privileges']);
            $groups = implode('|', $group['privileges']);

            $result = $this->model->addGroup($group, $groups);

            if ($result) {
                $this->setInfo($GLOBALS['_USER_GROUP_ADD']);
                return true;
            } else {
                $this->setError($GLOBALS['_USER_GROUP_ADD_PRIV']);
                return false;
            }
        } else {
            $this->setError($GLOBALS['_USER_GROUP_EXIST']);
            return false;
        }
    }

    public function deleteGroup($id){
        $result = $this->model->deleteGroup($id);
        if ($result) {
            $updateResult = $this->model->updateUsersGroup($id);
            if ($updateResult) {
                $this->setInfo($GLOBALS['_USER_GROUP_DEL']);
                return true;
            } else {
                $this->setError($GLOBALS['_USER_GROUP_DEL_USERS']);
                return false;
            }
        } else {
            $this->setError($GLOBALS['_USER_GROUP_NO_DEL']);
            return false;
        }
    }

    public function addPrivilege($data){
        if (!$this->model->privilegeExists($data['name'])) {
            $result = $this->model->addPrivilege($data);

            if ($result) {
                $this->setInfo($GLOBALS['_USER_PRIV_ADD']);
                return true;
            } else {
                $this->setError($GLOBALS['_USER_PRIV_NO_ADD']);
                return false;
            }
        } else {
            $this->setError($GLOBALS['_USER_PRIV_EXIST']);
            return false;
        }
    }

    public function updatePrivilege($data){
        $result = $this->model->updatePrivilege($data);
        if ($result) {
            $this->setInfo($GLOBALS['_USER_CHANGE_SAVE']);
            return true;
        } else {
            $this->setError($GLOBALS['_USER_NOT_SEVE']);
            return false;
        }
    }

    public function deletePrivilege($id){
        $result = $this->model->deletePrivilege($id);
        if ($result) {
            $groupsResult = $this->model->updateGroups($id);
            if ($groupsResult) {
                $this->setInfo($GLOBALS['_USER_PRIV_DEL']);
                return true;
            } else {
                $this->setError($GLOBALS['_USER_PRIV_DEL_GROUP']);
                return false;
            }
        } else {
            $this->setError($GLOBALS['_USER_PRIV_NO_DEL']);
            return false;
        }
    }
}
