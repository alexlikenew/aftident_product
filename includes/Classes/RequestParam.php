<?php

namespace Classes;

class RequestParam {

    private $data;

    function __construct($data) {
        $this->data = array();
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->data[$key] = $value;
            }
        }
    }

   public function getAll() {
        return $this->data;
    }

    function get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return false;
        }
    }

    public function set($name, $value) {
        $this->data[$name] = $value;
        return true;
    }

    function has($name) {
        if (isset($this->data[$name])) {
            return true;
        } else {
            return false;
        }
    }


}