<?php

namespace Controllers;

use Models\Blog;

class BlogController extends Controller
{
    protected $model;
    protected $module;
    protected $scale_width;
    protected $scale_height;
    protected $list_height;
    protected $list_width;
    protected $detail_width;
    protected $detail_height;
    protected $main_width;
    protected $main_height;


    public function __construct($table = 'blog')
    {
        $this->model = new Blog($table);
        $this->module = $table;
        parent::__construct($this->model, $table);
    }

    public function getLastItems($limit, int $toSkip = 0, ?int $from = null)
    {
        $articles = $this->model->getLastItems($limit, $toSkip, $from);

        foreach ($articles as $key => $article) {
            $articles[$key]['content_short'] = strip_tags($articles[$key]['content_short']);

            $articles[$key]['url'] = BASE_URL.'/'.$this->module.'/'.$articles[$key]['title_url'];
            if ($articles[$key]['photo']) {
                $articles[$key]['photo'] = $this->getPhotoUrl($articles[$key]['photo'], $articles[$key]['id']);
            }
            if ($articles[$key]['video']) {
                $articles[$key]['video'] = $this->getVideoUrl(
                    $articles[$key]['video'],
                    $articles[$key]['id'],
                    $articles[$key]['video_title']
                );
            }
            if ($articles[$key]['date_add']) {
                $articles[$key]['date_add'] = date('d.m.Y', strtotime($articles[$key]['date_add']));
            }
        }

        return $articles;
    }

}