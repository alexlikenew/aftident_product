<?

header("content-type: text/xml");

if (!defined('SCRIPT_CHECK'))
    die('No-Hack here buddy.. ;)');

if (empty($oMenu))
    $oMenu = new Menu($root);

$menu_xml = $oMenu->LoadXml(0, Menu::GROUP_TOP);

echo $menu_xml;
?>