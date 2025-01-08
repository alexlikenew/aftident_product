<?php

namespace Controllers;

class BlogAdminController extends BlogController
{
    public function __construct($table = 'blog')
    {
        parent::__construct($table);
    }

}