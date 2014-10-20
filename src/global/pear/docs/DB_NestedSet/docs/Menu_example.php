<?php /** $Id: Menu_example.php,v 1.5 2004/08/05 19:28:28 datenpunk Exp $ */ ?>
<html>
  <title>DB_NestedSet using PEAR::HTML_Menu Output class</title>
<body>
<div style="font-weight: bold;">DB_NestedSet using PEAR::HTML_Menu Output class</div>
<div>
<?php
/**
 * Tests the DB_NestedSet class using the Menu renderer
 * Requires that you have HTML_Menu installed
 *
 * @author Daniel Khan <dk@webcluster.at>
 */
// {{{ mysql dump

/**
 * Dump of the example mysql table and data:
#
# Table structure for table `nested_set`
#


CREATE TABLE `tb_nodes` (
  `STRID` int(11) NOT NULL auto_increment,
  `ROOTID` int(11) NOT NULL default '0',
  `l` int(11) NOT NULL default '0',
  `r` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `STREH` int(11) NOT NULL default '0',
  `LEVEL` int(11) NOT NULL default '0',
  `STRNA` char(128) NOT NULL default '',
  PRIMARY KEY  (`STRID`),
  KEY `ROOTID` (`ROOTID`),
  KEY `STREH` (`STREH`),
  KEY `l` (`l`),
  KEY `r` (`r`),
  KEY `LEVEL` (`LEVEL`),
  KEY `SRLR` (`ROOTID`,`l`,`r`),
  KEY `parent` (`parent`)
) TYPE=MyISAM ;


#
# Dumping data for table `nested_set`
#

INSERT INTO `nested_set` VALUES (5, 5, 1, 1, 1, 10, 'Root A');
INSERT INTO `nested_set` VALUES (7, 7, 1, 1, 1, 4, 'Root B');
INSERT INTO `nested_set` VALUES (6, 5, 1, 2, 2, 5, 'Sub1 of A');
INSERT INTO `nested_set` VALUES (1, 5, 2, 2, 6, 9, 'Sub2 of A');
INSERT INTO `nested_set` VALUES (2, 5, 1, 3, 3, 4, 'Child of Sub1');
INSERT INTO `nested_set` VALUES (3, 5, 1, 3, 7, 8, 'Child of Sub2');
INSERT INTO `nested_set` VALUES (4, 7, 1, 2, 2, 3, 'Sub of B');
# --------------------------------------------------------

#
# Table structure for table `nested_set_locks`
#

CREATE TABLE `nested_set_locks` (
  `lockID` char(32) NOT NULL default '',
  `lockTable` char(32) NOT NULL default '',
  `lockStamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`lockID`,`lockTable`)
) TYPE=MyISAM COMMENT='Table locks for comments';

*/

// }}}
// {{{ set up variables
include('../../../../../cms_admin/inc/cms_config.inc.php');
include('../../../../../cms_configs/website_conf.inc.php');
include('../../../../../cms_admin/inc/cms_dbconfig.inc.php');

require_once('HTML/Menu.php');
require_once 'HTML/Menu/ArrayRenderer.php';
//require_once(dirname(__FILE__).'/../NestedSet.php');
//require_once(dirname(__FILE__).'/../NestedSet/Output.php');
require_once('DB/NestedSet.php');
require_once('DB/NestedSet/Output.php');
//$dsn = 'mysql://user:password@localhost/test';

$params = array(
    'id'        => 'id',
    'parent_id' => 'rootid',
    'left_id'   => 'l',
    'right_id'  => 'r',
    'order_num' => 'norder',
    'level'     => 'level',
    'name'      => 'name',
);



$nestedSet =& DB_NestedSet::factory('DB', $dsn, $params); 
// we want the nodes to be displayed ordered by name, so we add the secondarySort attribute
$nestedSet->setAttr(array(
        'node_table' => 'nested_set', 
        'lock_table' => 'nested_set_locks', 
        'secondarySort' => 'name',
    )
);

    //$parent = $nestedSet->createRootNode(array('STRNA' =>'Testberichte'), false, true);
    //$nestedSet->createSubNode($parent, array('STRNA' => 'Pads,Sattelunterlagen'));
    //$nestedSet->createSubNode($parent, array('STRNA' =>'Kartentaschen'));
    //$nestedSet->createSubNode($parent, array('STRNA' =>'Kartenmesser'));
    //$nestedSet->createSubNode($parent, array('STRNA' => 'Erste Hilfe Sets'));
    //$nestedSet->createSubNode($parent, array('STRNA' => 'OutdoorJacken'));
    //$nestedSet->createSubNode($parent, array('STRNA' =>'flexible Sattel'));



// get data (important to fetch it as an array, using the true flag)
$data = $nestedSet->getAllNodes(true);

// }}}
// {{{ manipulate data

// add links to each item
foreach ($data as $id => $node) {
     $data[$id]['url'] = $_SERVER['PHP_SELF'].'?nodeID=' . $node['id'];
}

echo "<pre style=\"font-family:arial;font-size:10px;border-bottom: 1px solid black;\">";
echo "<h1 style=\"color:red;font-weight:bold;margin:0;border-bottom: 1px solid black;\">nach getAllNodes</h1>";
print_r ($data);
echo "</pre>"; 

// }}}
// {{{ render output
$params = array(
    'structure' => $data,
    'titleField' => 'name',
    'nodeposition' => 'position',
    'urlField' => 'url',
    'level' => 'level'
);

// Create the output driver object	
$output =& DB_NestedSet_Output::factory($params, 'Menu');

echo "<pre style=\"font-family:arial;font-size:10px;border-bottom: 1px solid black;\">";
echo "<h1 style=\"color:red;font-weight:bold;margin:0;border-bottom: 1px solid black;\">output</h1>";
print_r ($output);
echo "</pre>"; 

// Fetch the menu array
$structure = $output->returnStructure();

echo "<pre style=\"font-family:arial;font-size:10px;border-bottom: 1px solid black;\">";
echo "<h1 style=\"color:red;font-weight:bold;margin:0;border-bottom: 1px solid black;\">structure</h1>";
print_r ($structure);
echo "</pre>"; 

// Instantiate the menu object, we presume that $data contains menu structure
$currentUrl = $_SERVER['PHP_SELF'].'?nodeID=' . $_GET['nodeID'];

#Erstelle Navi-Baum
$menu = & new HTML_Menu($structure, 'tree');
$menu->forceCurrentUrl($currentUrl);
//soweit bleibt alles gleich 

//erzeuge ArrayRenderer um Baum in Array einzulesen
$renderer = & new HTML_Menu_ArrayRenderer;
//hole Daten aus Navi-Baum in Renderer
$menu->render(&$renderer);
//gebe Baum als Array aus
$nav_smarty = $renderer->toArray(); 

echo "<pre>";
print_r ($nav_smarty);
echo "</pre>"; 

echo "Menu type 'sitemap'<br>";
$menu = & new HTML_Menu($structure, 'sitemap');

// Force menu to understand the nodeID passed with the request
$menu->forceCurrentUrl($currentUrl);
// Output the menu
$menu->show();
echo "<hr>";

echo "Menu type 'tree'<br>";
// Set another type
$menu->setMenuType('tree');
// Output the menu
$menu->show();
echo "<hr>";

echo "Menu type 'rows'<br>";
// Set another type
$menu->setMenuType('rows');
// Output the menu
$menu->show();
echo "<hr>";

echo "Menu type 'urhere'<br>";
// Set another type
$menu->setMenuType('urhere');
// Output the menu
$menu->show();
echo "<hr>";

echo "Menu type 'prevnext'<br>";
// Set another type
$menu->setMenuType('prevnext');
// Output the menu
$menu->show();
echo "<hr>";

// }}}
?>
</div>
</body>
</html>
