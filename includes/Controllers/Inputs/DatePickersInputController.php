<?php

namespace Controllers\Inputs;

class DatePickersInputController extends FormInputController
{
    public $type = FormInputController::TYPE_DATEPICKER;

    protected $config = [
        'required'      => false,
        'autocomplete'  => false,
    ];

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}