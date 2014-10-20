<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/geburtstage/global/php/dbconfig.php'); 
require_once($_SERVER['DOCUMENT_ROOT'].'/geburtstage/global/php/sessionHelper.php');
$loginerror = '';

if (isset($_POST['login']))
{
    $userid = check_user($_POST['username'], $_POST['userpass'], $db);
    if ($userid!=false)
        login($userid, $db);
    else
        $loginerror = 'Login / Passwort falsch?';
} 

if (isset($_POST['logout']))
{
  logout($db); 
} 

$monthWords = array('01' => 'Januar','02' => 'Februar','03' => 'März','04' => 'April','05' => 'Mai','06' => 'Juni','07' => 'Juli','08' => 'August','09' => 'September','10' => 'Oktober','11' => 'November','12' => 'Dezember');
$currentMonth = date("m");   	     	

if(!isset ($_REQUEST['sent'])){$_REQUEST['sent'] = 0;} $sent = $_REQUEST['sent'];

if(!isset ($_REQUEST['day'])){$_REQUEST['day'] = 0;} $day = $_REQUEST['day'];
if(!isset ($_REQUEST['month'])){$_REQUEST['month'] = 0;} $month = $_REQUEST['month'];
if(!isset ($_REQUEST['year'])){$_REQUEST['year'] = "19??";} $year = $_REQUEST['year'];
if(!isset ($_REQUEST['lastname'])){$_REQUEST['lastname'] = 0;} $lastname = $_REQUEST['lastname'];

if($sent == 1) {
  
  $checked = 1;
  $error = "";

  if ($day == 0) {
  	$error.="Tag?, ";
  	$checked = 0;
  }	
  if ($month == 0) {
  	$error.="Monat?, ";
  	$checked = 0;
  }	
  if (!is_numeric($year)) {
  	$error.="Jahr?, ";
  	$checked = 0;
  }    
  if (strlen($lastname) == 0) {
  	$error.="Name?, ";
  	$checked = 0;
  }	 

  $ok = $checked;  
  
}

if($ok == 1) {

  $sql="INSERT INTO bday_list (name, geburtstag) VALUES ('".$lastname."', '".$year."-".$month."-".$day."')";
  $rs = $db->query($sql);	
  
  $day = 0;
  $month = 0;
  $year = "19??";
  $lastname = "";

}

?>  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Geburtstagsliste</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style type="text/css"><!--
/* <![CDATA[ */

/*  body und schrift deffinitionen */

body {
  background-color: #e1ddd9;
  font-size: 12px;
  font-family: Verdana, Arial, Helvetica, SunSans-Regular, Sans-Serif;
  color:#564b47;  
  padding:20px;
  margin:0px;
  text-align: center;
}


#inhalt { 	
  text-align: left;
  vertical-align: middle;	
  margin: 0px auto;
  padding: 10px;
  width: 747px;
  background-color: #ffffff;
  border: 1px dashed #564b47;
  margin-bottom: 10px;
}

.formular { 	
  text-align: left;
  vertical-align: middle;	
  margin: 0px auto;
  padding: 10px;
  width: 747px;
  background-color: #ffffff;
  border: 1px dashed #564b47;
  margin-bottom: 10px;
}

.month {
  width: 240px;
  float: left;
  border: 1px solid black;
  margin: 0 10px 10px 0;
}

.currentMonth {
  width: 240px;
  float: left;
  border: 1px solid black;
  margin: 0 10px 10px 0;
  background-color: #F9F1E1;
}
			
p, h1, pre {
  margin: 0px; 
  padding: 3px 5px; 
}

h1 {
  font-size: 11px;
  text-transform:uppercase;
  text-align: right;
  color: #FFFFFF;
  background-color: #90897a;
}

.name {
  font-size: 10px;
  font-weight: bold;
}

.datum, .tip, .errorText {
  font-size: 9px;
}

.selBox, .textbox {
  font-size: 10px;
}

a { 
color: #ff66cc;
font-size: 11px;
background-color:transparent;
text-decoration: none; 
}

a.email:link, a.email:visited {
  color: #90897a;
  font-size: 10px;
  background-color:transparent;
  text-decoration: none; 
}
a.email:hover, a.email:active {
  color: #000000;
  font-size: 10px;
  background-color:transparent;
  text-decoration: none; 
}

 /* ]]> */	
 --></style>
</head>
<body>
  
<div class="formular">
<form id="form1" name="form1" method="post" action="<?php print basename($_SERVER['PHP_SELF']) ?>">
  <input type="hidden" name="sent" value="1" />
  <table border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="tip">Tag:  </td>
      <td class="tip">Monat:</td>
      <td class="tip">Jahr</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">Name:</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>
    </tr>
    <tr>
      <td valign="top">
	  <select name="day" class="selBox">
        <option value="0">- Tag -</option>
        <?php
        for($i = 1; $i <= 31; $i++) {
          if(sprintf("%02d", $i) == $day) $selected = "selected=\"selected\""; else $selected = "";
          print("<option ".$selected." label=\"".sprintf("%02d", $i)."\" value=\"".sprintf("%02d", $i)."\">".sprintf("%02d", $i)."</option>\n");  
        }
        ?>
      </select>&nbsp;</td>
      <td valign="top">
	  <select name="month" class="selBox">
          <option value="0">- Monat -</option>
          <?php foreach($monthWords as $k => $v) {
            if(sprintf("%02d", $i) == $month) $selected = "selected=\"selected\""; else $selected = "";
            print("<option ".$selected." label=\"".$v."\" value=\"".$k."\">".$v."</option>\n");  
          } ?>
        </select>&nbsp;</td>
      <td valign="top"><input class="textbox" name="year" value="<?php print $year; ?>" size="4" maxlength="4" type="text" /></td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><input class="textbox" type="text" name="lastname" /></td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><input class="textbox" type="submit" name="Submit" value="Eintragen" /></td>
      <td valign="top">&nbsp;</td>
      <td class="errorText" style="color: red;"><?php if(strlen($error) != 0) {print "&nbsp;&nbsp;&nbsp;&nbsp;<b>Eingabefehler: </b> ".$error;} ?></td>
    </tr>
  </table>
</form>
</div>  
  
<div id="inhalt">
  
  <?php
  foreach($monthWords as $k => $v) {
    
    if($k == $currentMonth)
      $style = "currentMonth";
    else 
      $style = "month";
    
    if($k == '03' || $k == '06' || $k == '09')
      print("<div class=\"".$style."\" style=\"margin: 0 0 10px 0;\">\n");
    elseif($k == '10' || $k == '11')
      print("<div class=\"".$style."\" style=\"margin: 0 10px 0 0;\">\n");
    elseif($k == '12')
      print("<div class=\"".$style."\" style=\"margin: 0;\">\n");      
    else  
      print("<div class=\"".$style."\">\n");
      
      print("<h1>".strtoupper($v)."</h1>\n");
      
      $sql = "SELECT *, DATE_FORMAT(geburtstag, '%d.%m.%Y') as datum FROM bday_list WHERE DATE_FORMAT(geburtstag, '%m') = '".$k."' ORDER BY DATE_FORMAT(geburtstag, '%d')";
      $rs = $db->query($sql); 
      
      $marked = array();
      if (logged_in($db)) {
        $sqlm = "SELECT * FROM bday_relations WHERE userid = '".getUserId($db)."'";
        $rsm = $db->query($sqlm); 
        while ($rowm = $rsm->fetchRow(DB_FETCHMODE_ASSOC)) {
          $marked[] = $rowm['listid'];
        }          
      }
    
      while ($row = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
        
      	print("<p>");
      	  if (logged_in($db)) {     	    
      	    print("<a href=\"marker.php?p=1&amp;id=".$row['id']."\"><img src=\"images/");
      	    if(in_array($row['id'], $marked)) {
      	      print "marker1";
      	    } else {
      	      print "marker0";
      	    }
      	    print(".png\" width=\"17\" height=\"11\" border=\"0\" alt=\"Marker\" style=\"margin-bottom: -2px;\"/></a>");
      	  }
      	    print("<span class=\"name\"> ".$row['name']."</span> <span class=\"datum\">(".$row['datum'].")</span></p>\n");

      }
   
    print("</div>\n");  
    
    if($k == '03' || $k == '06' || $k == '09' || $k == '12')
      print("<div style=\"clear:both;\"></div>\n");
  
  }
  ?>
    
</div>

<div class="formular">
<form id="form2" name="form2" method="post" action="<?php print basename($_SERVER['PHP_SELF']) ?>">
<?php if (!logged_in($db)) { ?>
  <table border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="tip">E-Mail:  </td>
      <td class="tip">&nbsp;</td>
      <td class="tip">Passwort:</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>      
    </tr>
    <tr>    
      <td valign="top"><input name="username" type="text" class="selBox" /></td>
      <td valign="top">&nbsp;</td>
      <td valign="top"><input name="userpass" type="password" id="userpass" class="selBox" /></td>
      <td valign="top">&nbsp;</td>      
      <td valign="top"><input name="login" type="submit" id="login" value="Einloggen" class="textbox" /></td>
      <td valign="top">&nbsp;</td>
      <td class="errorText" style="color: red;"><?php if(strlen($loginerror) != 0) {print "&nbsp;&nbsp;&nbsp;&nbsp;<b>Fehler: </b> ".$loginerror;} ?></td>      
    </tr>
    <tr>
      <td class="tip">&nbsp;</td>
      <td class="tip">&nbsp;</td>      
      <td colspan="5" valign="top"><a href="mailto:frank@meyerer.de?subject=Bday Passwort vergessen" class="email">Passwort vergessen?</a></td>      
    </tr>    
  </table>
<?php } else { ?>  
  <table border="0" cellpadding="0" cellspacing="0">
    <tr>    
      <td valign="top"><input name="logout" type="submit" id="login" value="Ausloggen" class="textbox" /></td>
    </tr>
  </table>
<?php } ?>
</form>
</div>  

</body>

</html>
