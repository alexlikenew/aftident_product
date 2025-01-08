<?php

/*
 *      plik:       generowanie-sitemap-e499a4e79165e53e0367fd52eb37012f.php
 *      autor:      Technetium [Tc]
 *                  Marek Kleszyk
 *                  08 czerwiec 2009
 *      system:     T.CMS-4.0-SEO
 */

define('SCRIPT_CHECK', 1); // dostep do innych plików php

require_once 'config.inc.php';
require_once ROOT_PATH . '/includes/root.class.php';

// ustawiamy domyslne wartosci dla zmiennych
//	$_SERVER['PHP_SELF'] = $_SERVER['REQUEST_URI'];
$_SERVER['PHP_SELF'] = $_SERVER['REDIRECT_URL'];

// inicjujemy glowna klase
$root = new Root();

// ladujemy podstawowa konfiguracje
$CONF = $root->conf->Load();
$CONF['base_url'] = BASE_URL;
if (empty($_SERVER['HTTP_HOST']))
    $_SERVER['HTTP_HOST'] = 'kamil/cms'; // bez http://

define('ADMIN_EMAIL', $CONF['admin_email']);
define('BIURO_EMAIL', $CONF['biuro_email']);
define('FIRM_NAME', $CONF['firm_name']);

define('NEWSLETTER', 1); // wlanczamy generowanie sitemap
// sprawdzarka
require_once ROOT_PATH . '/includes/_aktualizacjaSerwisu.inc.php';

// konczymy prace strony
$root->db->close();
?>