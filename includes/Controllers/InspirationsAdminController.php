<?php

namespace Controllers;

class InspirationsAdminController extends InspirationsController
{
    public function __construct($table = 'inspirations')
    {
        parent::__construct($table);
    }
}