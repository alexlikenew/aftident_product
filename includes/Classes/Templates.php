<?php
namespace Classes;

use Smarty;

class Templates
{
    /**
     *
     * @var Smarty
     */
    public $smarty;
    public $templatePath;

    public function __construct()
    {
        $this->smarty = new Smarty();
        $this->initVars();

        return true;
    }

    public function getSmarty()
    {
        return $this->smarty;
    }

    /**
     * assigns a Smarty variable
     *
     * @param  array|string $tpl_var the template variable name(s)
     * @param  mixed        $value   the value to assign
     * @param  boolean      $nocache if true any output of this variable will be not cached
     *
     * @return Smarty_Internal_Data current Smarty_Internal_Data (or Smarty or Smarty_Internal_Template) instance for
     *                              chaining
     */
    public function assign($tpl_var, $value = null, $nocache = false)
    {
        $this->getSmarty()->assign($tpl_var, $value, $nocache);
    }

    /**
     * fetches a rendered Smarty template
     *
     * @param  string $template   the resource handle of the template file or template object
     * @param  mixed  $cache_id   cache id to be used with this template
     * @param  mixed  $compile_id compile id to be used with this template
     * @param  object $parent     next higher level of Smarty variables
     *
     * @throws Exception
     * @throws SmartyException
     * @return string rendered template output
     */
    public function fetch($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        $this->getSmarty()->fetch($template, $cache_id, $compile_id, $parent);
    }

    /**
     * displays a Smarty template
     *
     * @param string $template   the resource handle of the template file or template object
     * @param mixed  $cache_id   cache id to be used with this template
     * @param mixed  $compile_id compile id to be used with this template
     * @param object $parent     next higher level of Smarty variables
     */
    public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        $this->getSmarty()->display($template, $cache_id, $compile_id, $parent);
    }

    // funkcja inicjuje zmienne
    function initVars()
    {
        // ustawiamy sciezki
//        $this->smarty->setDebugging(true);
        $this->smarty->setTemplateDir(ROOT_PATH . '/templates/');
        $this->smarty->setCacheDir(ROOT_PATH . '/templates/_cache/');
        $this->smarty->setCompileDir(ROOT_PATH . '/templates/_compile/');
        $this->smarty->setConfigDir(ROOT_PATH . '/templates/_configs/');

        // wlaczamy(wylaczamy) cach'owanie
        if (SMARTY_CACHE == 1) {
            $this->smarty->caching = true;
        } else {
            $this->smarty->caching = false;
        }
    }

    // funkcja ustawia sciezke do szablonow
    function setTemplatePath($path)
    {
        $this->initVars();
        $this->smarty->setTemplateDir($this->smarty->getTemplateDir()[0].$path);
        $this->smarty->setCacheDir($this->smarty->getCacheDir().$path);
        $this->smarty->setCompileDir($this->smarty->getCompileDir().$path);
        $this->templatePath = $this->smarty->getTemplateDir()[0].$path;
        return true;
    }

    public function getTemplatePath(){
        return $this->templatePath;
    }

    // funkcja ustawia tytul strony
    function setPageTitle($title)
    {
        if (!empty($title)) {
            return $this->smarty->assign('pageTitle', $title);
        } else {
            return false;
        }
    }

    // funkcja ustawia slowa kluczowe dla strony
    function setPageKeywords($keywords)
    {
        if (!empty($keywords)) {
            return $this->smarty->assign('pageKeywords', $keywords);
        } else {
            return false;
        }
    }

    // funkcja ustawia opis dla strony
    function setPageDescription($description)
    {
        if (!empty($description)) {
            return $this->smarty->assign('pageDescription', $description);
        } else {
            return false;
        }
    }

    // funkcja podmienia winiete w topie
    function setTopImage($img)
    {
        $this->smarty->assign('top_image', $img);

        return true;
    }

    // funkcja wyswietla podana strone
    public function displayPage($page)
    {
        $this->getSmarty()->assign('body', $page);
        if (SMARTY_CACHE == 1) {
            $this->getSmarty()->display('index.tpl', str_replace(BASE_URL, '', $_SERVER['REQUEST_URI']));
        } else {
#            $this->createTemplate('index.tpl');
#            $this->compileAllTemplates();
            $this->getSmarty()->display('index.tpl');
        }
        return true;
    }

    // funkcja wyswietla informacje o bledzie
    public function displayError($error = '', $pageError = 'misc/error.html')
    {
        if (!empty($error)) {
            $this->smarty->assign('error', $error);
        }

        $this->displayPage($pageError);
    }

    // funkcja wyswietla informacje systemowa
    function displayInfo($info = '', $pageInfo = 'misc/info.html')
    {
        if (!empty($info)) {
            $this->smarty->assign('info', $info);
        }

        $this->displayPage($pageInfo);
    }

}
