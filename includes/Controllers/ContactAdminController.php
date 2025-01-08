<?php

namespace Controllers;

class ContactAdminController extends ContactController
{
    public function __construct($table = 'contact_messages')
    {
        parent::__construct($table);
    }

}