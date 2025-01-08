<?php

namespace Traits;

use Classes\Templates;

trait Template{

    private $smarty = null;
    private $_error;
    private $_info;

    public function getTemplates():Templates
    {
        if($this->smarty)
            return $this->smarty;

        $this->setSmarty();
        return $this->smarty;
    }

    public function setSmarty()
    {
        $this->smarty = new Templates();
    }

    public function assign($var, $value = null, $noCache = false):bool
    {
        $this->getTemplates()->assign($var, $value, $noCache);
        return true;
    }

    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null):bool
    {
        $this->getTemplates()->fetch($template, $cache_id, $compile_id, $parent);
        return true;
    }

    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null):bool
    {
        $this->getTemplates()->display($template, $cache_id, $compile_id, $parent);
        return true;
    }

    public function initVars():bool
    {
        $this->getTemplates()->initVars();
        return true;
    }

    public function setTemplatePath(string $path):bool
    {
        return $this->getTemplates()->setTemplatePath($path);
    }

    public function getTemplatePath(){
        return $this->getTemplates()->getTemplatePath();
    }

    public function setPageTitle(string $title)
    {
        return $this->getTemplates()->setPageTitle($title);
    }

    public function setPageKeywords(string $keywords){
        return $this->getTemplates()->setPageKeywords($keywords);
    }

    public function setPageDescription(string $description){
        return $this->getTemplates()->setPageDescription($description);
    }

    public function setTopImage($image)
    {
        return $this->getTemplates()->setTopImage($image);
    }

    public function displayPage($page)
    {
        return $this->getTemplates()->displayPage($page);
    }

    public function showError($error = '', $pageError = 'misc/error.html')
    {
        return $this->getTemplates()->displayError($error, $pageError);
    }

    public function displayInfo($info = '', $pageInfo = 'misc/info.html')
    {
        return $this->getTemplates()->displayInfo($info, $pageInfo);
    }

    public function setError($errorMsg, $systemHalt = false) {
        $this->_error = $errorMsg;
        $this->getTemplates()->assign('error', $errorMsg);

        if ($systemHalt === true) {
            // natychmiastowe zatrzymanie skryptu, blad krytyczny!
            $this->getTemplates()->display('critical-error.html');
            exit;
        }
        return true;
    }

    /* funkcja ustawia komunikat systemowy */

    public function setInfo($infoMsg) {
        $this->_info = $infoMsg;
        $this->getTemplates()->assign('info', $infoMsg);

        return true;
    }

    /* funkcja ustawia informacje systemowa o podanej nazwie */

    public function setMessage($msgType, $msg) {
        $this->getTemplates()->assign($msgType, $msg);
        return true;
    }

    /* funkcja zwraca blad systemowy */

    public function getError() {
        return $this->_error;
    }

    /* funkcja zwraca komunikat systemowy */

    public function getInfo() {
        return $this->_info;
    }

    /* funkcja wyswietla blad */

    public function displayErrorMessage($errorMsg = '') {
        if (empty($errorMsg)) {
            $errorMsg = $this->getError();
        }
        $this->showError($errorMsg);
        return true;
    }

    /* funkcja wyswietla komunikat systemowy */

    public function displayInfoMessage($infoMsg = '') {
        if (empty($infoMsg)) {
            $infoMsg = $this->getInfo();
        }
        $this->displayInfo($infoMsg);
        return true;
    }
}
