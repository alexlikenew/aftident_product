<?php

namespace Controllers\Inputs;

class PasswordInputController extends FormInputController
{
    public $type = FormInputController::TYPE_PASSWORD;

    protected $config = [
        'size'          => false,
        'pattern'       => false,
        'placeholder'   => false,
        'required'      => false,
        'autocomplete'  => false,
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}