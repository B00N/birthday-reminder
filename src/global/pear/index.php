<?php
/**
 * Installation of PEAR_Frontend_Web:
 *
 * 'pear install PEAR_Frontend_Web'
 * Create a __secure__ directory accessable by your webserver
 * put a file like this one in there.
 * Create a directory for PEAR to be installed in and add it to
 * the include path. (Yes. you can use your standard PEAR dir,
 * but the Webserver needs writing access, so I think for this
 * beta software a new directory is more safe)
 * Specify a file for your PEAR config.
 * Have fun ...
 *
 * by Christian Dickmann <dickmann@php.net>
 */
if ($env=getenv('PHP_PEAR_INSTALL_DIR')) {
    define("PHP_PEAR_INSTALL_DIR",$env);
} else {
    putenv('PHP_PEAR_INSTALL_DIR=/home/httpd/vhosts/got2.de/subdomains/cms/httpdocs/cms_inculdes/pear/PEAR');
}

if ($env=getenv('PHP_PEAR_BIN_DIR')) {
    define("PHP_PEAR_BIN_DIR",$env);
} else {
    putenv('PHP_PEAR_BIN_DIR=/home/httpd/vhosts/got2.de/subdomains/cms/httpdocs/cms_inculdes/pear/bin');
}
if ($env=getenv('PHP_PEAR_PHP_BIN')) {
    define("PHP_PEAR_PHP_BIN",$env);
} else {
    putenv('PHP_PEAR_PHP_BIN=');
}
$env=getenv('PHP_PEAR_INSTALL_DIR');
require_once($env.'/PEAR.php');
if (OS_WINDOWS) {
    $seperator = ';';
} else {
    $seperator = ':';
};

ini_set('include_path', '/home/httpd/vhosts/got2.de/subdomains/cms/httpdocs/cms_inculdes/pear/PEAR');
$useDHTML         = true;
// Include WebInstaller
require_once("PEAR/WebInstaller.php");
?>
