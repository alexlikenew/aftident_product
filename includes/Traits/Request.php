<?php

namespace Traits;
use Classes\RequestParam;
trait Request{

    private $type;

    public $get;

    public $post;

    public $files;

    public function __construct($get, $post, $files) {
        $this->get = new RequestParam($get);
        $this->post = new RequestParam($post);
        $this->files = new RequestParam($files);
    }

    public function setGet($data){
        $this->get = new RequestParam($data);
    }

    public function setPost($data){
        $this->post = new RequestParam($data);
    }

    public function setFiles($data){
        $this->files = new RequestParam($data);
    }

    public function getRequestParameter($name) {
        if ($this->post->has($name)) {
            return $this->post->get($name);
        } else if ($this->get->has($name)) {
            return $this->get->get($name);
        } else {
            return false;
        }
    }

    public function hasParameter($name) {
        if ($this->get->has($name)) {
            return true;
        } elseif ($this->post->has($name)) {
            return true;
        } else {
            return false;
        }
    }

}

