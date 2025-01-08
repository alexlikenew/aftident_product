<?php

namespace Controllers\Inputs;

class DateTimeInputController extends FormInputController
{
    public $type = FormInputController::TYPE_DATE_TIME;

    public function __construct(){
        $this->config = array_merge($this->defaultConfig, $this->config);
        parent::__construct($this->type);
    }

}