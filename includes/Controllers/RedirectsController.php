<?php

namespace Controllers;

use Models\Redirects;

class RedirectsController extends Controller{
    protected $model;

    public function __construct($table = 'redirects'){
        $this->model = new Redirects($table);
        parent::__construct($this->model, $table);
    }

    public function checkRedirect(string $url): ?string
    {
        return $this->model->checkRedirect($url);
    }
}