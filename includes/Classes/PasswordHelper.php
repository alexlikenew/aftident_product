<?php

namespace Classes;

use Controllers\DbStatementController;
use Controllers\UsersController;

class PasswordHelper{
    /**
     *
     * @var Root
     */
    protected $Root;

    /**
     *
     * @var Users
     */
    protected $Users;

    /**
     *
     * @var UserPasswordGenerator
     */
    protected $Generator;

    /**
     *
     * @var PasswordStorage
     */
    protected $PasswordStorage;

    /**
     *
     * @var string
     */
    protected $password = '';

    /**
     *
     * @var integer
     */
    protected $userId = null;

    /**
     *
     * @param Root $root
     */
    public function __construct()
    {
        $this->Generator = new UserPasswordGenerator();
        $this->PasswordStorage = new PasswordStorage();
    }

    /**
     *
     * @param Users $Users
     *
     * @return passwordHelper
     */
    public function setUsers(UsersController $Users)
    {
        $this->Users = $Users;

        return $this;
    }

    /**
     *
     * @param integer $userId
     *
     * @return passwordHelper
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     *
     * @param string $password
     *
     * @return passwordHelper
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @return passwordHelper
     */
    public function createRandomPassword()
    {
        $this->password = $this->Generator->generate();

        return $this;
    }

    /**
     *
     * @param string $password
     *
     * @return string
     */
    public function fastHash($password)
    {
        return $this->PasswordStorage->create_hash($password);
    }

    /**
     *
     * @throws Exception
     */
    public function save()
    {
        if (empty($this->userId) || empty($this->password)) {
            throw new Exception(__METHOD__ . ' empty save data on line: ' . __LINE__);
        }

        $this->update();
    }

    /**
     *
     * @param string $typedPassword
     *
     * @return boolean
     */
    public function check($typedPassword)
    {
        $user = $this->Users->load($this->userId);

        if (empty($user)) {
            throw new Exception(__METHOD__ . ' no user found on line: ' . __LINE__);
        }

        $hash = $user['password'];

        return $this->PasswordStorage->verify_password($typedPassword, $hash);
    }

    /**
     * @return passwordHelper
     */
    protected function update()
    {
        $this->validate();

        $hash = $this->PasswordStorage->create_hash($this->password);
        $result = $this->Users->getModel()->changePassword($this->userId, $hash);


        if (!$result) {
            throw new Exception(__METHOD__ . ' update error on line: ' . __LINE__);
        }

        return $this;
    }

    /**
     *
     * @return passwordHelper
     */
    protected function validate()
    {
        if (is_null($this->userId)) {
            throw new Exception(__METHOD__ . ' bad UserId error on line:' . __LINE__);
        }

        if (empty($this->password)) {
            throw new Exception(__METHOD__ . ' password not generated error on line: ' . __LINE__);
        }

        return $this;
    }

    /**
     * @deprecated since version 1.0.5
     *
     * @param string $input
     *
     * @return string
     *
     * @throws Exception
     */
    protected function extractHash($input)
    {
        if (empty($input)) {
            throw new Exception(__METHOD__ . ' error on line: ' . __LINE__);
        }

        $explode = explode(':', $input);

        return (is_array($explode)) ? end($explode) : '';
    }

}
