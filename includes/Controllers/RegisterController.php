<?php


namespace Controllers;


use Models\Register;

class RegisterController extends Controller
{
    protected $model;

    public function __construct($table = 'rejestr'){
        $this->model = new Register($table);
        parent::__construct($this->model, $table);
    }




}