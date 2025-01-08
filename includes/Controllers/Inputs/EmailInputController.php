<?php

namespace Controllers\Inputs;

class EmailInputController extends FormInputController
{
    public $type = FormInputController::TYPE_EMAIL;

    protected $config = [
        'size'          => false,
        'multiple'      => false,
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