<?php


namespace Controllers;

class ProducerAdminController extends ProducerController
{
    public function __construct($table = 'producers')
    {
        parent::__construct($table);
    }

}