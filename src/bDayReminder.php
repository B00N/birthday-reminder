<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/geburtstage/global/php/dbconfig.php'); 

$oneDay  = 86400;
$oneWeek = 604800;
$current = time();
$namen = array();

$bDayToday = date("m-d");
$bDayTomorow = date("m-d", $current + $oneDay);
$bDayInOneWeek = date("m-d", $current + $oneWeek);

//$bDayToday = '02-04';
//$bDayTomorow = '02-06';
//$bDayInOneWeek = '06-06';

$nl="\n";

$bDays0 = array(); // Heute
$bDays1 = array(); // Morgen
$bDays2 = array(); // in einer Woche

// Wer hat heute Geburtstag?
$sql = "SELECT id, name, DATE_FORMAT(geburtstag, '%d.%m.%Y') as datum, DATE_FORMAT(geburtstag, '%Y') as jahr FROM bday_list WHERE DATE_FORMAT(geburtstag, '%m-%d') = '".$bDayToday."' ORDER BY DATE_FORMAT(geburtstag, '%d')";
$rs = $db->query($sql); 
while ($row = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
  $bDays0[$row['id']] = array('name' => $row['name'], 'datum' => $row['datum'], 'alter' => date('Y') - $row['jahr'], 'wann' => 0);
}

// Wer hat morgen Geburtstag?
$sql = "SELECT id, name, DATE_FORMAT(geburtstag, '%d.%m.%Y') as datum, DATE_FORMAT(geburtstag, '%Y') as jahr FROM bday_list WHERE DATE_FORMAT(geburtstag, '%m-%d') = '".$bDayTomorow."' ORDER BY DATE_FORMAT(geburtstag, '%d')";
$rs = $db->query($sql); 
while ($row = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
  $bDays1[$row['id']] = array('name' => $row['name'], 'datum' => $row['datum'], 'alter' => date('Y') - $row['jahr'], 'wann' => 1);
}

// Wer hat in einer Woche Geburtstag?
$sql = "SELECT id, name, DATE_FORMAT(geburtstag, '%d.%m.%Y') as datum, DATE_FORMAT(geburtstag, '%Y') as jahr FROM bday_list WHERE DATE_FORMAT(geburtstag, '%m-%d') = '".$bDayInOneWeek."' ORDER BY DATE_FORMAT(geburtstag, '%d')";
$rs = $db->query($sql); 
while ($row = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
  $bDays2[$row['id']] = array('name' => $row['name'], 'datum' => $row['datum'], 'alter' => date('Y') - $row['jahr'], 'wann' => 2);
}

$hdlToday    = 'HEUTE hat bzw. haben Geburtstag:'.$nl;
$hdlTomoorow = 'MORGEN hat bzw. haben Geburtstag:'.$nl;
$hdlWeek     = 'IN EINER WOCHE hat bzw. haben Geburtstag:'.$nl;
$txtLine     = '----------------------------------------------------------------------'.$nl;

$sql = "SELECT * FROM bday_users";
$rs = $db->query($sql); 
while ($row = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
  
  $msgBoddy = '';
  $namen = array();
  $uRelation = array();
  $uID    = $row['UserID'];
  $uEmail = $row['UserMail'];
  
  $sql2 = "SELECT * FROM bday_relations WHERE userid = '".$uID."'";
  $rs2 = $db->query($sql2); 
  while ($row2 = $rs2->fetchRow(DB_FETCHMODE_ASSOC)) {
    $uRelation[] = $row2['listid'];
  }
  
  // Heute Arr gegenchecken
  $txtToday = '';
  foreach($bDays0 as $k => $v) {
    if(in_array($k, $uRelation)) {
      $txtToday .= ' - '.$v['name'].' ('.$v['datum'].') '.$v['alter'].' Jahre'.$nl;    
      $namen[] = $v['name'];
    }
  }
  if(strlen($txtToday) != 0)
    $msgBoddy .= $hdlToday.$txtToday.$txtLine.$nl;
  
  // Morgen Arr gegenchecken
  $txtTomoorow = '';
  foreach($bDays1 as $k => $v) {
    if(in_array($k, $uRelation)) {
      $txtTomoorow .= ' - '.$v['name'].' ('.$v['datum'].') '.$v['alter'].' Jahre'.$nl;
      $namen[] = $v['name'];
    }          
  }
  if(strlen($txtTomoorow) != 0)
    $msgBoddy .= $hdlTomoorow.$txtTomoorow.$txtLine.$nl;
    
  // Woche Arr gegenchecken
  $txtWeek = '';
  foreach($bDays2 as $k => $v) {
    if(in_array($k, $uRelation)) {
      $txtWeek .= ' - '.$v['name'].' ('.$v['datum'].') '.$v['alter'].' Jahre'.$nl; 
      $namen[] = $v['name'];
    }         
  }
  if(strlen($txtWeek) != 0)
    $msgBoddy .= $hdlWeek.$txtWeek.$txtLine.$nl;
    
  if(strlen($msgBoddy) != 0) {
    $msgBoddy .= 'Ein Service von http://www.meyerer.de/geburtstage/';
    
    /* Empf‰nger */
    $empfaenger = array($uEmail);
    /* Absender */
    $absender = 'Meyerer B-Day Reminder<frank@meyerer.de>';
    /* Rueckantwort */
    $reply = 'Frank Meyerer<frank@meyerer.de>';
    /* Betreff */
    $subjectNames = '';
    foreach($namen as $v) {
      $subjectNames .= $v.', ';
    }
    if(strlen($subjectNames) != 0) {
      $subjectNames .= '+';
      $subjectNames = str_replace(', +', '', $subjectNames);
    }
  
    $subject = 'Geburtstag-Erinnerung: '.$subjectNames;
    
    /* Baut Header der Mail zusammen */
    $headers .= 'From:' . $absender . "\n";
    $headers .= 'Reply-To:' . $reply . "\n"; 
    $headers .= 'X-Mailer: PHP/' . phpversion() . "\n"; 
    $headers .= 'X-Sender-IP: ' . $REMOTE_ADDR . "\n"; 
    $headers .= "Content-Type: text/plain\n";
    
    // Extrahiere Emailadressen
    $empfaengerString = implode(',', $empfaenger);
    
    /* Verschicken der Mail */
    mail($empfaengerString, $subject, $msgBoddy, $headers);
    
    // Log schreiben
    $sqlLog = "INSERT INTO bday_log (recipient, subject) VALUES ('".$empfaengerString."', '".$subject."')";
    $rsLog = $db->query($sqlLog);      
  
  }
    
}


?>