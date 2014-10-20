<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/geburtstage/global/php/dbconfig.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/geburtstage/global/php/sessionHelper.php');

if (logged_in($db)) {
  
  $sql = "SELECT * FROM bday_relations WHERE userid = '".getUserId($db)."' AND listid = '".$_REQUEST['id']."'";
  $rs = $db->query($sql);
  $cnt = $rs->numRows();
  
  if($cnt > 0) {
    $sql = "DELETE FROM bday_relations WHERE userid = '".getUserId($db)."' AND listid = '".$_REQUEST['id']."'";
    $rs = $db->query($sql);
  } else {
    $sql = "INSERT INTO bday_relations (userid, listid) VALUES('".getUserId($db)."', '".$_REQUEST['id']."')";
    $rs = $db->query($sql);    
  }
  
}

header("location: index.php");
?>