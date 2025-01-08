<?php

namespace Controllers;

class NewsAdminController extends NewsController
{
    public function __construct($table = 'news')
    {
        parent::__construct($table);
    }

}