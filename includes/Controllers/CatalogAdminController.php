<?php

namespace Controllers;

class CatalogAdminController extends CatalogController
{
    public function __construct($table = 'catalogs')
    {
        parent::__construct($table);
    }

    public function loadArticlesAdmin($pages, $page, $categoryId = null){

        return $this->getItems($pages, $page, false, true, $categoryId);
    }
}