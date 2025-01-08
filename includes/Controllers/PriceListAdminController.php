<?php

namespace Controllers;

class PriceListAdminController extends PriceListController
{
    public function __construct($table = 'price_list')
    {
        parent::__construct($table);
    }
}