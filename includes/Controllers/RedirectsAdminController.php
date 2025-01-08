<?php


namespace Controllers;
use Models\Redirects;


class RedirectsAdminController extends RedirectsController
{
    protected $table;
    protected $model;

    public function __construct($table = 'redirects'){
        $this->table = $table;
        $this->model = new Redirects($table);
        parent::__construct();

    }

    public function import()
    {
        $oXml = simplexml_load_file( ROOT_PATH . "/sitemap_import.xml");
        foreach ($oXml->url as $val) {
            $str = (string)$val->loc;
            $str = str_replace("www.technetium.pl", "localhost/technetium3", $str);
            $this->model->import($str);

        }
    }

    /* funkcja laduje wszystkie artykuly w panelu admina */
    public function loadArticlesAdmin($pages, $page)
    {
        $start = ($pages - $page) * $this->limit_page;
        return $this->model->loadArticles($start, $this->limit_page, false);
    }

    /* funkcja zwraca liczbe podstron w panelu admin */
    public function getArticlesAdmin()
    {
        $amount = $this->model->getAmount();

        if ($amount < 1) {
            $amount = 1;
        }

        return ceil($amount / $this->limit_admin);
    }

    public function saveAll($data)
    {
        // TODO nie wiem na obecną chwilę jaką alternatywę tu dać.
        return $this->model->saveAll($data);

    }

    public function create(array $data,array $files = null): ?int
    {
        if ($data['src_url']) {
            if (!$this->validateLink($data['src_url'])) {
                $this->setError("Nieprawidłowy url w polu Źródło");
                return false;
            }

            $result = $this->model->create($data);
            if ($result) {
                $this->setInfo("Przekierowanie zostało dodane");
                return $result;
            } else {
                return false;
            }
        } else {
            $this->setError("Pola Źródło i Cel nie mogą być puste");
        }

        return false;
    }

    public function delete(int $id = null): bool
    {
        return $this->model->delete($id);
    }

    function search($keyword)
    {
        return $this->model->search($keyword);
    }
}