<?php

namespace Controllers;
use DivineOmega\CliProgressBar\ProgressBar;

class TaskController extends Controller
{
    protected $arguments;
    protected $progressBar;

    public function __construct($argv){


        if (!(substr(php_sapi_name(), 0, 3) == 'cli')) {
            echo 'Access denied';
            die;
        }

        if (empty($argv)) {
            echo 'Invalid command';
            die();
        }
        if (!isset($argv[1]) || !$argv[1]) {
            echo 'Empty command';
            die();
        }
        $data = $argv;
        array_shift($data);
        $this->arguments = $data;
        $this->progressBar = new ProgressBar;
        parent::__construct();

    }

    public function make()
    {
        $action = $this->getAction();

        if (method_exists($this, $action))
            $this->$action();
        else
            echo 'Invalid command';

        die;
    }

    public function getAction()
    {
        return $this->arguments[0];
    }

    public function checkForNewFlyers(){
        $shopsController = new PromotialsAdminController();
        $shops = $shopsController->getAll(true);

        $this->progressBar->setMaxProgress(count($shops));

        foreach($shops as $shop){
            $shopsController->updateItems($shop['id']);
            $this->progressBar->advance()->display();
        }
        $this->progressBar->complete();
        echo PHP_EOL;
    }

}