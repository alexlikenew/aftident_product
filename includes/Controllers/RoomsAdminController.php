<?php

namespace Controllers;

class RoomsAdminController extends RoomsController
{
    public function __construct($table = 'rooms')
    {
        parent::__construct($table);
    }
}