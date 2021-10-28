<?php

/*
        (c) Kaavren
*/

include_once("config.php");
include_once("error.php");

function error_report($errcode) {
  global $err;
  global $link;
  if ($errcode==10) {                 // mysql error
    $r=mysqli_error($link);
  }
  else {

    $r=$err[$errcode];
  }

    echo "<b>Error: </b>"."$r";

  return 0;
}

function my_query($sql) {
  global $link;
  $result = mysqli_query($link, $sql);
  if ($result) return $result;
  //  error handling here
  if (DEBUG_MODE) { error_report(10); };
//  error_report(10);
  die;

  return false;
}

function currency_display($amount) {
  $currency = floor($amount*100) % 100;

  if ($currency < 10) {
    $currency = "0" . $currency;
  }

  $currency = floor($amount) . "." . $currency;

  return($currency);
}

function valid_email($email) {
  $arr = explode("@",$email);
  $arr2 = explode(".", $email);

  if ((sizeof($arr) != 2) || (sizeof($arr2) != 2)) {
    return(false);
  }

  if (strpos($arr[0], ".") === false) {
    return(true);
  }

  return(false);
}

function check_12hours_limit($lottery_id, $date = 0) {
  global $db_prefix;

  if (!$date) {
    $date = time();
  }
  $r = my_query("select started, duration from " . $db_prefix . "lotteries
  where id='" . mysqli_real_escape_string($lottery_id) . "'");

  list($started, $duration) = mysqli_fetch_row($r);

  if ($date < ($started + ($duration *24 - 12) * 3600)) return(false); else return(true);
}

// update user IP ad last visit time

$r = my_query("delete from " . $db_prefix . "visits where date < '" . (time() - 24*60*60) ."'");
$r = my_query("insert into " . $db_prefix . "visits(ip,date) values ('" . ip2long($_SERVER['REMOTE_ADDR']) . "','" . time() . "')");

if (isset($_COOKIE['lt_user_login']) && isset($_COOKIE['lt_user_password'])) {
  $r = my_query("select id, password from " . $db_prefix . "users where
  username_hash='" . mysqli_real_escape_string($_COOKIE['lt_user_login']) . "' and password_hash='" . mysqli_real_escape_string($_COOKIE['lt_user_password']) . "'");
  if (mysqli_num_rows($r)) {
    list($user_id, $password) = mysqli_fetch_row($r);
    $_SESSION['lt_user_id'] = $user_id;
    $_SESSION['lt_user_pass'] = $password;
    $logged = true;
  }
}


if (isset($_SESSION['lt_user_id'])) {
  $r = my_query("select password from " . $db_prefix . "users where id='" . $_SESSION['lt_user_id'] . "'");
  list($pass) = mysqli_fetch_row($r);
  if ($pass != $_SESSION['lt_user_pass']) {
    session_unset();
    exit;
  }


  if (!is_numeric($_SESSION['lt_user_id'])) {
    session_unset();
    exit;
  }

  $r = my_query("update " . $db_prefix . "users set last_visit='" . time() . "',
                 ip='" . ip2long($_SERVER['REMOTE_ADDR']) . "'
                 where id='" . $_SESSION['lt_user_id'] . "'");
  $logged = true;
}
else {
  $logged = false;
}

// save referrer ID

if (isset($_GET['r'])) {
  $_SESSION['lt_referrer_id'] = $_GET['r'];
}

?>