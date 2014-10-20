<?php
function check_user($name, $pass, $db)
{
   $sql="SELECT UserId
          FROM bday_users
          WHERE UserName='".$name."' AND UserPass=MD5('".$pass."')
          LIMIT 1";
    $rs = $db->query($sql);

    if ($rs->numRows() > 0) {
      while ($user = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
        return $user['UserId'];
      }      
    }
    else
        return false;
}

function login($userid, $db)
{
    $sql="UPDATE bday_users
          SET UserSession='".session_id()."'
          WHERE UserId=".$userid;
    $rs = $db->query($sql);
}

function logged_in($db)
{
    $sql="SELECT UserId
          FROM bday_users
          WHERE UserSession='".session_id()."'
          LIMIT 1";
    $rs = $db->query($sql);
    return ($rs->numRows());
}

function getUserId($db)
{
    $sql="SELECT UserId
          FROM bday_users
          WHERE UserSession='".session_id()."'
          LIMIT 1";
    $rs = $db->query($sql);
    if ($rs->numRows() > 0) {
      while ($user = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
        return $user['UserId'];
      }      
    }
    else
        return false;    
}

function logout($db)
{
    $sql="UPDATE bday_users
          SET UserSession=NULL
          WHERE UserSession='".session_id()."'";
    $rs = $db->query($sql);
} 
?>