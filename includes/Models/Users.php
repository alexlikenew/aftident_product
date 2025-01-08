<?php

namespace Models;
use Controllers\DbStatementController;
use Classes\PasswordHelper;

class Users extends Model{
    protected $table;
    protected $tableGroups;
    protected $tableLog;
    protected $tablePrivileges;
    protected $tableLoggedIn;

    public function __construct($table = 'users'){
        $this->table = DB_PREFIX . $table;
        $this->tableGroups = '`groups`';
        $this->tableLog = $this->table.'_log';
        $this->tablePrivileges = 'privilege';
        $this->tableLoggedIn = $this->table.'_zalogowani';
        parent::__construct($table);
    }

    function getId($userId) {
        $q = "SELECT id FROM " . $this->table . " WHERE MD5(CONCAT(id,login,password))=? ";
        $params = [
            [DbStatementController::STRING, $userId]
        ];
        return $this->query($q, $params)->get_result();
    }

    public function getUserPrivileges($id){
        $q = "SELECT g.privileges FROM " . $this->tableGroups . " g, " . $this->table . " u ";
        $q.= "WHERE u.group_id=g.id AND u.id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->get_result();
    }

    public function getPrivilege($w){
        $q = "SELECT name FROM " . $this->tablePrivileges . " WHERE " . $w;

        return $this->query($q)->get_all_rows();
    }

    public function getUserByLogin($login){
        $q = "SELECT u.*,g.name as group_name FROM " . $this->table . " u ";
        $q .= "LEFT JOIN " . $this->tableGroups . " g ON g.id=u.group_id ";
        $q .= "WHERE LOWER(login)=? ";

        $params = [
            [DbStatementController::STRING, $login],
        ];

        return $this->query($q, $params );
    }

    public function userExists($login){
        $q = "SELECT COUNT(id) FROM " . $this->table . " ";
        $q .= "WHERE login=? ";
        $params = [
            [DbStatementController::STRING, $login]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_result() > 0 ? true : false;
    }

    public function getByAlternative($data){
        $q = "select login from ".$this->table." where alternate_login like CONCAT('%;', ?, ';%')  and password=? ";
        $params = [
            [DbStatementController::STRING, $data['login']],
            [DbStatementController::STRING, $data['pass']]
        ];
        return  $this->query($q, $params)->fetch_assoc();
    }

    public function log($login, $pass, $reason = 'unknown'): bool{
        $hostname = gethostbyaddr(CLIENT_IP) . ' IP:' . CLIENT_IP;

        $q = "INSERT INTO " . $this->tableLog . " SET ";
        $q .= "login=?, ";
        $q .= "pass=?, ";
        $q .= "reason=?, ";
        $q .= "date_add=NOW(), ";
        $q .= "host=? ";
        $params = [
            [DbStatementController::STRING, $login],
            [DbStatementController::STRING, '******'],
            [DbStatementController::STRING, $reason],
            [DbStatementController::STRING, $hostname],
        ];

        $this->query($q, $params);
        return true;
    }

    public function load($id){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];

        return  $this->query($q, $params)->fetch_assoc();
    }

    public function loggedIn($login, $firstName, $lastName, $id, $groupId, $hostName){
        $q = "INSERT INTO " . $this->tableLoggedIn . " SET ";
        $q .= "login=?, ";
        $q .= "first_name=?, ";
        $q .= "last_name=?, ";
        $q .= "user_id=?, ";
        $q .= "group_id=?, ";
        $q .= "date_add=NOW(), ";
        $q .= "host=? ";
        $params = [
            [DbStatementController::STRING, $login],
            [DbStatementController::STRING, $firstName],
            [DbStatementController::STRING, $lastName],
            [DbStatementController::INTEGER, $id],
            [DbStatementController::INTEGER, $groupId],
            [DbStatementController::STRING, $hostName],
        ];
        $this->query($q, $params);
    }

    public function getUsers($where, $order, $page, $limit){
        $q = "SELECT u.*,g.name as group_name ";
        $q.= "FROM " . $this->table . " u ";
        $q.= "LEFT JOIN " . $this->tableGroups . " g ON u.group_id=g.id ";
        $q.= "WHERE " . $where['query'] ;
        $q.= " ORDER BY " . $order . " LIMIT " . (($page - 1) * $limit) . ", " . $limit;

        $params = array_merge([], $where['params']);

        return  $this->query($q, $params)->get_all_rows();
    }

    public function getUsersPages($where){
        $q = "SELECT COUNT(id) FROM " . $this->table . " WHERE " . $where['query'] . " ";
        $params = array();
        $params = array_merge($params, $where['params']);

        $statement = $this->query($q, $params);
        return $statement->get_result();
    }

    public function getGroups(){
        $q = "SELECT id, name FROM " . $this->tableGroups . " ORDER BY name ASC";
        $statement = $this->query($q);
        return $statement->get_all_rows();
    }

    public function getUserById($id){
        $q = "SELECT * FROM " . $this->table . " ";
        $q .= "WHERE id=? ";
        //$q .= "AND (`admin`!=1 OR " . $_SESSION['user']['admin'] . "=1)";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        //dump($q); dd($params);
        return $this->query($q, $params)->fetch_assoc();
    }

    public function update(array $user, int $id = null):bool{
        $q = "UPDATE " . $this->table . " SET ";
        $q .= "business=?, ";
        $q .= "alternate_login=?, ";
        $q .= "firm_name=?, ";
        $q .= "first_name=?, ";
        $q .= "last_name=?, ";
        $q .= "street=?, ";
        $q .= "nr_bud=?, ";
        $q .= "nr_lok=?, ";
        $q .= "city=?, ";
        $q .= "post_code=?, ";
        $q .= "nip=?, ";
        $q .= "email=?, ";
        $q .= "phone=?, ";
        $q .= "active=?, ";
        $q .= "admin_login=?, ";
        $q .= "group_id=? ";
        $params = [
            [DbStatementController::INTEGER, $user['business'] ?? 0],
            [DbStatementController::STRING, $user['alternate_login'] ?? ''],
            [DbStatementController::STRING, $user['firm_name'] ?? ''],
            [DbStatementController::STRING, $user['first_name']],
            [DbStatementController::STRING, $user['last_name']],
            [DbStatementController::STRING, $user['street']],
            [DbStatementController::STRING, $user['nr_bud']],
            [DbStatementController::STRING, $user['nr_lok'] ?? ''],
            [DbStatementController::STRING, $user['city']],
            [DbStatementController::STRING, $user['post_code']],
            [DbStatementController::STRING, $user['nip'] ?? ''],
            [DbStatementController::STRING, $user['email']],
            [DbStatementController::STRING, $user['phone']],
            [DbStatementController::INTEGER, $user['active']],
            [DbStatementController::INTEGER, $user['admin_login']],
            [DbStatementController::INTEGER, $user['group_id']],
        ];
        if ($user['haslo']) {
            $passwordHash = (new PasswordHelper())->fastHash($user['haslo']);
            $q .= ", password=? ";
            $params[] = [DbStatementController::STRING, $passwordHash];
        }

        $q .= "WHERE id=? AND login!='admin'";
        $params[] = [DbStatementController::INTEGER, $user['id']];
        //dump($q); dd($params);
        $this->saveEventInfo(json_encode($user), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function delete(int $id = null, $background = false):bool{
        $q = "DELETE FROM ".$this->table ." WHERE id = ?";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode($id), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function create(array $user):int{
        $passwordHash = (new PasswordHelper())->fastHash($user['pass1']);

        $q = "INSERT INTO " . $this->table . " SET ";
        $q .= "login=?, ";
        $q .= "password=?, ";
        $q .= "first_name=?, ";
        $q .= "last_name=?, ";
        $q .= "street=?, ";
        $q .= "nr_bud=?, ";
        $q .= "nr_lok=?, ";
        $q .= "city=?, ";
        $q .= "post_code=?, ";
        $q .= "email=?, ";
        $q .= "phone=?, ";
        $q .= "admin_login=?, ";
        $q .= "group_id=?, ";
        $q .= "active=?, ";
        $q .= "discount=? ";

        $params = [
            [DbStatementController::STRING, strtolower($user['login'])],
            [DbStatementController::STRING, $passwordHash],
            [DbStatementController::STRING, $user['first_name']],
            [DbStatementController::STRING, $user['last_name']],
            [DbStatementController::STRING, $user['street']],
            [DbStatementController::STRING, $user['nr_bud']],
            [DbStatementController::STRING, $user['nr_lok']],
            [DbStatementController::STRING, $user['city']],
            [DbStatementController::STRING, $user['post_code']],
            [DbStatementController::STRING, $user['email']],
            [DbStatementController::STRING, $user['phone']],
            [DbStatementController::INTEGER, $user['admin_login']],
            [DbStatementController::INTEGER, $user['group_id']],
            [DbStatementController::INTEGER, $user['active']],
            [DbStatementController::DOUBLE, $user['discount']]
        ];
        $this->saveEventInfo(json_encode($user), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function addToNewsletter($user){
        $q = "INSERT INTO " . DB_PREFIX . "newsletter SET ";
        $q .= "first_name=?, ";
        $q .= "last_name=?, ";
        $q .= "email=?, ";
        $q .= "active='1'";

        $params = [
            [DbStatementController::STRING, $user['first_name']],
            [DbStatementController::STRING, $user['last_name']],
            [DbStatementController::STRING, $user['email']]
        ];
        $this->saveEventInfo(json_encode($user), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function getPrivileges(){
        $q = "SELECT * FROM " . $this->tablePrivileges . " ORDER BY name ASC";
        $statement = $this->query($q);
        return $statement->get_all_rows();
    }

    public function getGroupPrivileges($id){
        $q = "SELECT LEFT(`privileges`, LENGTH(`privileges`)-1) as `privileges` FROM " . $this->tableGroups;
        $q .= " WHERE id=? ";

        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->get_result();
    }

    public function getGroupName($id){
        $q = "SELECT name FROM " . $this->tableGroups . " ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        return $this->query($q, $params)->get_result();
    }

    public function updateGroup($id, $groups){
        $q = "UPDATE " . $this->tableGroups . " SET ";
        $q .= "`privileges`=? ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $groups . '|'],
            [DbStatementController::STRING, $id]
        ];
        $this->saveEventInfo(json_encode($id), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function loadUserPrivileges($id){
        $q = "SELECT g.privileges FROM " . $this->tableGroups . " g, " . $this->table . " u ";
        $q.= "WHERE u.group_id=g.id AND u.id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];


        $statement = $this->query($q, $params);
        $privileges = $statement->get_result();

        $p = explode('|', $privileges);

        $w = "";
        for ($i = 0; $i < count($p); $i++) {
            if (!empty($p[$i])) {
                if ($i > 0) {
                    $w.= " OR ";
                }
                $w.= " id='" . $p[$i] . "' ";
            }
        }
        unset($privileges);
        if (!empty($w)) {
            $q = "SELECT name FROM " . $this->tablePrivileges . " WHERE " . $w;

            $statement = $this->query($q);
            $privileges = array();
            while ($row = $statement->fetch_assoc()) {
                $privileges[$row['name']] = 1;
            }
            return $privileges;
        } else {
            return array();
        }
    }

    public function groupExists($name){
        $q = "SELECT COUNT(id) FROM `" . $this->tableGroups . "` ";
        $q .= "WHERE `name`=? ";
        $params    = [
            [DbStatementController::STRING, $name],
        ];
        return $this->query($q, $params)->get_result();
    }

    public function addGroup($group, $groups){
        $q = "INSERT INTO " . $this->tableGroups . " SET ";
        $q .= "name=?, ";
        $q .= "`privileges`=?";
        $params = [
            [DbStatementController::STRING, $group['name']],
            [DbStatementController::STRING, $groups . '|']
        ];
        $this->saveEventInfo(json_encode($group), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function deleteGroup($id){
        $q = "DELETE FROM " . $this->tableGroups . " WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateUsersGroup($id){
        $q = "UPDATE " . $this->table . " SET group_id='0' WHERE group_id=? ";
        $params = [
            [DbStatementController::STRING, $id]
        ];

        $this->saveEventInfo(json_encode(['id' => $id]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function privilegeExists($name){
        $q = "SELECT COUNT(id) FROM `" . $this->tablePrivileges . "` ";
        $q .= "WHERE `name`=? ";
        $params    = [
            [DbStatementController::STRING, $name],
        ];

        return $this->query($q, $params)->get_result();

    }

    public function addPrivilege($data){
        $q = "INSERT INTO " . $this->tablePrivileges . " SET ";
        $q .= "name=?, ";
        $q .= "description=? ";

        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['description']]
        ];
        $this->saveEventInfo(json_encode($data), '', 0, __METHOD__, $this->table);
        return $this->query($q, $params)->insert_id();
    }

    public function updatePrivilege($data){
        $q = "UPDATE " . $this->tablePrivileges . " SET ";
        $q .= "name=?, ";
        $q .= "description=? ";
        $q .= "WHERE id=? ";
        $params = [
            [DbStatementController::STRING, $data['name']],
            [DbStatementController::STRING, $data['description']],
            [DbStatementController::INTEGER, $data['id']]
        ];
        $this->saveEventInfo(json_encode($data), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function deletePrivilege($id){
        $q = "DELETE FROM " . $this->tablePrivileges . " WHERE id=? ";
        $params = [
            [DbStatementController::INTEGER, $id]
        ];
        $this->saveEventInfo(json_encode(['id' => $id]), '', 2, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function updateGroups($privilegeId){
        $q = "UPDATE " . $this->tableGroups . " SET ";
        $q .= "`privileges`=REPLACE(`privileges`, ?, '') ";
        $q .= "WHERE `privileges` LIKE CONCAT(?, '%') ";
        $q .= "OR `privileges` LIKE CONCAT('%', ?, '%')";
        $params = [
            [DbStatementController::STRING, $privilegeId . '|'],
            [DbStatementController::STRING, $privilegeId . '|'],
            [DbStatementController::STRING, '|' . $privilegeId . '|']
        ];
        $this->saveEventInfo(json_encode(['id' => $privilegeId]), '', 1, __METHOD__, $this->table);
        return $this->query($q, $params)->is_success();
    }

    public function emailExists($email){
        $q = "SELECT COUNT(id) FROM " . $this->table . " ";
        $q .= "WHERE email=? ";
        $params = [
            [DbStatementController::STRING, $email]
        ];
        $statement = $this->query($q, $params);
        return $statement->get_result() > 0 ? true : false;
    }

    public function changePassword($id, $hash){
        $q = "UPDATE `" . $this->table . "` SET ";
        $q .= "`password` = ? ";
        $q .= "WHERE `id` = ? ";

        $params = [
            [DbStatementController::STRING, $hash],
            [DbStatementController::INTEGER, $id],
        ];

        return $this->query($q, $params)->is_success();
    }

    public function getUserByEmail($email){
        $q = "SELECT id, login, password, email FROM " . $this->table . " ";
        $q .= "WHERE email=? ";
        $params = [
            [DbStatementController::STRING, $email]
        ];

        return $this->query($q, $params)->fetch_assoc();
    }

    public function saveNewPass($id, $pass){
        $q = "UPDATE " . $this->table . " SET password=? WHERE id=?";
        $params = [
            [DbStatementController::STRING, $pass],
            [DbStatementController::INTEGER, $id]
        ];

        return $this->query($q, $params)->is_success();
    }

}
