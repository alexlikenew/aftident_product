<?php


namespace Controllers;


class FaqAdminController extends FaqController
{
    public function __construct($table = 'faq')
    {
        parent::__construct($table);
    }

    public function setActive($id, $active)
    {
        return $this->model->setActive($id, $active);
    }

}