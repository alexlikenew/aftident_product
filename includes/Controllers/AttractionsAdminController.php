<?php

namespace Controllers;

class AttractionsAdminController extends AttractionsController
{
    public function __construct($table = 'attractions')
    {
        parent::__construct($table);
    }
}