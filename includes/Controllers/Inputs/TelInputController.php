<?php

namespace Controllers\Inputs;

class TelInputController extends FormInputController
{
    public $type = FormInputController::TYPE_TEL;

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