<?php

namespace Controllers;
use Models\Users;
use Classes\PasswordHelper;


class UsersController extends Controller{
    protected $model;

    public function __construct($table = 'users')
    {
        $this->model = new Users($table);
        parent::__construct($this->model);
    }

    public function isLogged(){
        if (isset($_SESSION['userid'])) {
            $userId = $_SESSION['userid'];
            $id = $this->model->getId($userId);

            if($id){
                $privileges = $this->getUserPrivileges($id);

                if ($_SESSION['privileges_hash'] == md5(serialize($privileges))) {

                    return true;
                }
            }
        }

        $this->setError($GLOBALS['_USER_NO_LOGIN']);
        return false;
    }


    public function getUserPrivileges($id){
        $data = $this->model->getUserPrivileges($id);

        $p = explode('|', $data);

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
            $response = $this->model->getPrivilege($w);
            $privileges = [];
            foreach($response as $row) {
                $privileges[$row['name']] = 1;
            }
            return $privileges;
        } else {
            return [];
        }
    }

    public function logIn($user, $admin = false) {
        $login = $user['login'];
        $pass = $user['pass'];

        $user['login'] = strtolower($user['login']);

        if (empty($user['login']) or empty($user['pass'])) {
            $this->setMessage('user_error', $GLOBALS['_USER_LOGIN']);
            return false;
        } else {

            $statement = $this->model->getUserByLogin($user['login']);

            if ($statement->num_rows() < 1) {

                if (!$this->userExists($user['login'])) {

                    $row = $this->model->getByAlternative($user);
                    if ($row['login']) {
                        $msg = $GLOBALS['_USER_NOT_REGISTER_ALTERNATE_LOGIN']." ".$row['login'];
                        $this->log($login, $pass, $GLOBALS['_USER_NO_USER']);
                    }
                    else
                    {
                        $msg = $GLOBALS['_USER_NOT_REGISTER'];
                        $this->log($login, $pass, $GLOBALS['_USER_NO_USER']);
                    }
                } else {
                    $msg = $GLOBALS['_USER_BOTH_PASS'];
                    $this->log($login, $pass, $GLOBALS['_USER_BAD_PASS']);
                }

                $this->setMessage('user_error', $msg);
                return false;
            } else {
                $u = $statement->fetch_assoc();
                if ($u['active'] != 1) {
                    $this->setMessage('user_error', $GLOBALS['_USER_NOT_ACTIVE']);
                    $this->log($login, $pass, $GLOBALS['_USER_NO_ACTIVE']);

                    return false;
                } elseif (($admin == true) and ($u['admin_login'] != 1)) {
                    $this->setMessage('user_error', $GLOBALS['_USER_NOT_PRIVILAGE']);
                    $this->log($login, $pass, $GLOBALS['_USER_NOT_PRIVILAGE']);
                    return false;
                } else {
                    // sprawdzenie password hash
                    try {

                        if (
                            (new PasswordHelper())
                                ->setUsers($this)
                                ->setUserId($u['id'])
                                ->check($user['pass'])
                            === false
                        ) {
                            $this->log($user['login'], $user['pass'], $GLOBALS['_USER_BAD_PASS']);
                            $this->setMessage('user_error', $GLOBALS['_USER_BAD_PASS']);

                            return false;
                        }
                    } catch (Exception $ex) {
                        $this->messages->setMessage('user_error', $ex->getMessage());

                        return false;
                    }

                    $u['privileges'] = $this->getUserPrivileges($u['id']);
                    // logujemy uzytkownika
                    $_SESSION['userid'] = md5($u['id'] . $u['login'] . $u['password']);
                    $_SESSION['user'] = $u;
                    $_SESSION['privileges_hash'] = md5(serialize($u['privileges']));

                    // statystyka logowań
                    $this->loggedIn($u['login'], $u['first_name'], $u['last_name'], $u['id'], $u['group_id']);


                    $this->setError('');
                    return true;
                }
            }
        }
    }

    public function loggedIn($login, $firstName, $lastName, $id, $groupId){
        $hostName = gethostbyaddr(CLIENT_IP) . ' IP:' . CLIENT_IP;
        $this->model->loggedIn($login, $firstName, $lastName, $id, $groupId, $hostName);
        return true;

    }


    public function log($login, $pass, $reason){
        if(!$reason)
            $reason = 'unknown';
        $this->model->log($login, $pass, $reason);
    }

    public function userExists($login){
        return $this->model->userExists($login);
    }

    public function load(int $id){
        $user = $this->model->load($id);

        if($user)
            return $user;

        $this->setError($GLOBALS['_USER_NO_USER']);
        return false;
    }

    public function logAdmin(){
        if ($_SESSION['user']['admin_login'] == 1)
            return true;

        return false;
    }

    public function CheckPrivileges($name, $exit = 1) {
        if ($_SESSION['user']['privileges'][$name] == 1) {
            return true;
        } else {
            if ($exit == 1) {
                $this->setError($GLOBALS['_USER_NO_SEE_PAGE']);
                $this->display('index.tpl');
                die;
            }
        }
    }

    public function logOut() {
        if ($this->isLogged()) {
            unset($_SESSION['userid']);
            unset($_SESSION['user']);
            unset($_SESSION['privileges_hash']);
            return true;
        } else {
            return false;
        }
    }

    public function getGroups(){
        return $this->model->getGroups();
    }

    public function checkLogin($login) {
        return preg_match('/[a-z0-9_]{3,}/i', $login);
    }

    function ChangePass($pass, $admin = false) {
        if (empty($pass['old']) || empty($pass['new1']) || empty($pass['new2'])) {
            $this->setMessage('pass_error', $GLOBALS['_USER_EMPTY']);
            return false;
        } elseif ($pass['new1'] != $pass['new2']) {
            $this->setMessage('pass_error', $GLOBALS['_USER_BAD_BOTH_PASS']);
            return false;
        } elseif (strlen($pass['new1']) < 5) {
            $this->setMessage('pass_error', $GLOBALS['_USER_SHORT_PASS']);
            return false;
        } else {
            $PasswordHelper = new PasswordHelper();
            $PasswordHelper->setUsers($this);
            $PasswordHelper->setUserId($_SESSION['user']['id']);

            try {
                $PasswordHelper->check($pass['old']);
            } catch (Exception $ex) {
                $this->setMessage('pass_error', $GLOBALS['_USER_BAD_OLD_PASS']);

                return false;
            }

            try {

                $PasswordHelper->setPassword($pass['new1']);
                $PasswordHelper->save();
            } catch (Exception $ex) {
                $this->setMessage('pass_error', $ex->getMessage());

                return false;
            }

            $this->setInfo($GLOBALS['_USER_CHANGE_PASS']);

            $user['login'] = $_SESSION['user']['login'];
            $user['pass'] = $pass['new1'];

            $this->logIn($user, $admin); /* przelogowujemy usera */

            return true;
        }
    }

    public function create(array $user, array $files = null):int{

        $user['login'] = strtolower($user['login']);

        foreach ($user as $k => $v) {
            $user[$k] = strip_tags($v);
        }

        if ( ! $this->checkLogin($user['login'])) {

            $this->setError($GLOBALS['_USER_BAD_LOGIN'] ?? 'Błędny login');

            return false;
        } elseif ($this->userExists($user['login'])) {

            $this->setError($GLOBALS['_USER_REGISTER'] ?? "Użytkownik o podanym loginie już istnieje!");

            return false;
        } elseif ((isset($user['password']) && strlen($user['password']) < 5) || (isset($user['pass1']) && strlen($user['pass1']) < 5)) {

            $this->setError( $GLOBALS['_USER_SHORT_PASS'] ?? 'Za krotkie hasło');

            return false;
        } elseif ($user['business'] == 1 && empty($user['firm_name']) && !$user['admin_login']) {

            $this->setError( $GLOBALS['_USER_EMPTY_FIRM_NAME'] ?? 'Nie podano nazwy firmy');
            return false;
        } elseif ($user['business'] == 1 && !$this->checkNIP($user['nip']) && !$user['admin_login']) {

            $this->setError($GLOBALS['_USER_EMPTY_NIP'] ?? 'nie podano numeru NIP');
            return false;
        } elseif ($user['business'] != 1 && empty($user['first_name']) && !$user['admin_login']) {

            $this->setError($GLOBALS['_USER_EMPTY_NAME'] ?? 'Błędne imię');

            return false;
        } elseif ($user['business'] != 1 && empty($user['last_name']) && !$user['admin_login']) {
            $this->setError($GLOBALS['_USER_EMPTY_LASTNAME'] ?? 'Błędne nazwisko');
            return false;
        } elseif (empty($user['street']) && !$user['admin_login']) {

            $this->setError($GLOBALS['_USER_EMPTY_STREET'] ?? 'Nie podano ulicy');
            return false;
        } elseif (empty($user['nr_bud']) && !$user['admin_login']) {

            $this->setError($GLOBALS['_USER_EMPTY_NR_BUD'] ?? 'Nie podano nr budynku');

            return false;
        } elseif (!$this->checkPostCode($user['post_code']) && !$user['admin_login']) {

            $this->setError($GLOBALS['_USER_EMPTY_POSTCODE'] ?? 'Nie podano kodu pocztowego');

            return false;
        } elseif (empty($user['city']) && !$user['admin_login']) {

            $this->setError($GLOBALS['_USER_EMPTY_CITY'] ?? 'Nie podano miasta');

            return false;
        } elseif ($this->emailExists($user['email'])) {

            $this->setError($GLOBALS['_USER_EMAIL_REGISTER'] ?? 'Email już istnieje w bazie');

            return false;
        } else {
            $user['discount'] = isset($user['discount']) ? $user['discount'] : 0.0;

            $id = $this->model->create($user);

            if ($id) {
                $user['id'] = $id;

                $this->model->addToNewsletter($user);

                $this->setInfo($GLOBALS['_USER_MAKE_USER']);
                return true;
            } else {

                $this->setError($GLOBALS['_USER_BAD_ACCOUNT']);
                return false;
            }
        }
    }

    public function emailExists($email) {
        return $this->model->emailExists($email);
    }

    function checkPostCode($code) {
        return preg_match('/[0-9]{2}-[0-9]{3}/i', $code);
    }

    public function delete(int $id = null){
        return $this->model->delete($id);
    }

    public function checkNIP($nip) {
        if (preg_match('/[0-9]{10}/i', $nip)) {
            $check = array(6, 5, 7, 2, 3, 4, 5, 6, 7); /* wagi kontrolne dla numeru nip */
            $sum = 0;
            for ($i = 0; $i < count($check); $i++) {
                $sum+= $check[$i] * $nip[$i];
            }

            $crc = $sum % 11; /* powinna wyjsc ostatnia cyfra */
            $crc = $crc % 10; /* to na wypadek gdyby wynik byl rowny 10 */
            if ($nip[9] == $crc) {
                return true;
            }
        }
        return false;
    }

    public function remindPass($email)
    {
        $data = $this->model->getUserByEmail($email);

        if ($data) {
            $bytes = openssl_random_pseudo_bytes(4);
            $pass = bin2hex($bytes);
            $url = '<a href="'.$_SERVER['SCRIPT_URI'].'?moduleName=haslo">Zmiana hasła</a>';
            $newPass = (new PasswordHelper())->fastHash($pass);
            $this->model->saveNewPass($data['id'], $newPass);
            $subject = 'Przypomnienie hasła w serwisie ' . $_SERVER['HTTP_HOST'];
            $content = ConfigController::getOptionStatic('mail_reminder_pass_admin', 'config_extra');
            $search     = ['#NEW_PASS#', '#LOGIN_URL#'];
            $replace    = [$pass, $url];
            $content    = str_replace($search, $replace, $content);
            $this->setSubject($subject);
            $this->setBody($content);

            if ($this->sendHTML($data['email'])) {
                $this->setInfo($GLOBALS['_USER_GEN_NEW_PASS']);

                return true;
            }
            else {
                    $this->setError($GLOBALS['_USER_BAD_GEN_PASS'] ?? 'Bląd przy wysyłaniu wiadomości');
                    return false;
                }

        }
        else{
            $this->setError('Podany adres email nie istnieje');
            return false;
        }

    }
}
