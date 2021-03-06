<?php
/**
 * Usage example for HTML_Menu, DirectTree renderer
 * 
 * $Id: directtree.php,v 1.1 2004/01/17 12:34:14 avb Exp $
 * 
 * @package HTML_Menu
 * @author Alexey Borzov <avb@php.net>
 */

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/DirectTreeRenderer.php';
require_once './data/menu.php';

$types = array('tree', 'sitemap');

$menu =& new HTML_Menu($data);
$menu->forceCurrentUrl('/item1.2.2.php');

foreach ($types as $type) {
    echo "\n<h1>Trying menu type &quot;{$type}&quot;</h1>\n";
    $renderer =& new HTML_Menu_DirectTreeRenderer();
    $menu->render($renderer, $type);
    echo $renderer->toHtml();
}
?>