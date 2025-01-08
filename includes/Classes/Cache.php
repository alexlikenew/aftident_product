<?php

namespace Classes;
use \Cache_Lite;

class Cache{
    private $USE_CACHE;
    private $USE_CACHE_ON_LOGGED;
    private $cache_options;

    /**
     *
     * @var Cache_Lite
     */
    var $cl;

    function __construct($on = false, $loggedOn = false, $lifetime = 2592000) {
        $this->USE_CACHE = $on;
        $this->USE_CACHE_ON_LOGGED = $loggedOn;

        $this->cache_options = array(
            'cacheDir' => ROOT_PATH . '/_cache/',
            'lifeTime' => $lifetime,
            'automaticSerialization' => TRUE
        );

        $this->cl = new Cache_Lite($this->cache_options);
    }

    /**
     * funkcja sprawdza czy ma uzywac cache, zwraca true jesli tak
     */
    function checkUseCache() {
        if (!$this->USE_CACHE)
            return false;

        if ((!$this->USE_CACHE_ON_LOGGED) && ($_SESSION['user']['id']))
            return false;

        return true;
    }

    /**
     * metoda cachuje zmienna/tablice
     * nazwa cache generowana na podstawie id oraz zmiennych z get
     * @param <type> $var zmienna
     * @param <type> $id nazwa cache, np. 'produkty'
     * @param <type> $lifetime zmiana lifetime
     * @param <type> $ignore_url czy ignorowac request_url przy budowie nazwy - tak jesli zmienna jest taka sama na wszystkich podstronach
     * @return <type>
     */
    function saveVariable($var, $id, $lifetime = false, $ignore_url = false) {
        if (!$this->checkUseCache())
            return;

        $old_id = $id;
        if ($ignore_url)
            $id = md5($id);
        else
            $id = md5($id . serialize($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

        if ($lifetime)
            $this->cl->setLifeTime($lifetime);

        if ($this->cl->save($var, $id)) {

            return true;
        } else {

        }
    }

    function getVariable($id, $lifetime = false, $ignore_url = false) {
        if (!$this->checkUseCache())
            return;

        $old_id = $id;
        if ($ignore_url)
            $id = md5($id);
        else
            $id = md5($id . serialize($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

        if ($lifetime)
            $this->cl->setLifeTime($lifetime);

        return $this->cl->get($id);
    }

    function savePartial($content, $id, $lifetime = false, $ignore_url = false) {
        if (!$this->checkUseCache())
            return;

        $old_id = $id;
        if ($ignore_url)
            $id = md5($id);
        else
            $id = md5($id . serialize($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

        if ($lifetime)
            $this->cl->setLifeTime($lifetime);

        if ($this->cl->save($content, $id)) {

            return true;
        } else {

        }
    }

    function getPartial($id, $lifetime = false, $ignore_url = false) {
        if (!$this->checkUseCache())
            return;

        $old_id = $id;
        if ($ignore_url)
            $id = md5($id);
        else
            $id = md5($id . serialize($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

        if ($lifetime)
            $this->cl->setLifeTime($lifetime);

        //echo "<br>id:".$old_id.":".$id;

        return $this->cl->get($id);
    }
}