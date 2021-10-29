<?php

include_once("utils.php");
include_once("config.php");

session_start();
function archive_show() {
  global $db_prefix;
  global $iink;

  $r = my_query("select id, lot_id, amount, started, duration from " . $db_prefix . "archive order by started desc");
?>
  <div align="center">
  <h1>Archive</h1>
  <table width="100%" border="1" cellspacing="0" cellpadding="2" class="datatable">
  <tr>
  <th width="20%" bgcolor="#dddddd">ID</th>
  <th width="20%" bgcolor="#dddddd">Started</th>
  <th width="20%" bgcolor="#dddddd">Duration </th>
  <th width="20%" bgcolor="#dddddd">Available to be  Won</th>
  <th width="20%" bgcolor="#dddddd">Winners</th>
  </tr>
  <?php
    while (list($id, $lot_id, $amount, $started, $duration) = mysqli_fetch_row($r)) {
  ?>
  <tr>
  <td align="center"><?php echo $lot_id; ?></td>
  <td align="center"><?php echo date("m-d-Y", $started); ?></td>
  <td><?php echo $duration; ?> day(s) </td>
  <td>$<?php echo currency_display($amount); ?></td>
  <td align="center"><a href="index.php?go=winners&id=<?php echo $lot_id; ?>">Show</a></td>
  </tr>
  <?php
    }
  ?>
  </table>
  </div>
  <?php

  return(0);
}

function lotteries_show() {
  global $db_prefix;

/*  if ((isset($_GET['id'])) && (!is_numeric($_GET['id']))) {
    error_report(9);
    return(9);
  }
*/
  $r = my_query("select sum(amount) from " . $db_prefix . "messages");
  list($total_amount) = mysqli_fetch_row($r);
  ?>
  <div align="right">
  <i>Total won using our site : $ <?php echo currency_display($total_amount); ?></i>
  </div>
  <?php
  $r = my_query("select l.id, l.started, l.duration, l.ticket_price, l.win_percentage, l.description, l.available
  from " . $db_prefix . "lotteries l where l.ended = '0' order by l.started desc");
  ?>
  <div align="center">
  <h1>Open Raffles </h1>
  <table width="100%" border="1" cellspacing="0" cellpadding="2" class="datatable">
  <tr>
  <th width="10%" bgcolor="#dddddd">
  
  
  <div id="dhtmlfloatie" ></div>

  <a href="#" onMouseover="showfloatie(floattext[0], event, '#007AC9', 250, 20)" onMouseout="hidefloatie()" class="info"> End Date</a>
  </th>
  <th width="20%" bgcolor="#dddddd">
  <a href="#" onMouseover="showfloatie(floattext[1], event, '#007AC9', 250, 25)" onMouseout="hidefloatie()" class="info">Ticket Price</a>
  </th>
  <th width="20%" bgcolor="#dddddd">
   <a href="#" onMouseover="showfloatie(floattext[2], event, '#007AC9', 250, 60)" onMouseout="hidefloatie()" class="info">Percentage of Winners</a>
  </th>
  <th width="20%" bgcolor="#dddddd">
  <a href="#" onMouseover="showfloatie(floattext[3], event, '#007AC9', 250, 40)" onMouseout="hidefloatie()" class="info">Available to be Won</a></th>
  <th width="30%" bgcolor="#dddddd" colspan="2">
  <a href="#" onMouseover="showfloatie(floattext[4], event, '#007AC9', 250, 60)" onMouseout="hidefloatie()" class="info">Options</a>
  </th>
  </tr>
  <?php
    while (list($id, $started, $duration, $ticket_price, $win_percentage, $description, $available) = mysqli_fetch_row($r)) {
    $r1 = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
    $tickets_qty = mysqli_num_rows($r1);

    // Check if such user already have a ticket

    if (isset($_SESSION['lt_user_id'])) {
      $r2 = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id' and user_id='" . $_SESSION['lt_user_id'] . "'");
    }
  ?>
  <tr>
  <td align="center" width="10%"><?php echo date("m-d-Y", $started + $duration*3600*24); ?></td>
  <td width="20%">$<?php echo currency_display($ticket_price); ?></td>
  <td width="20%"><?php echo $win_percentage; ?> % </td>
  <td width="20%">$<?php echo currency_display($ticket_price * $tickets_qty * $available / 100); ?></td>
  <td align="center" width="15%">
  <?php
    if (check_12hours_limit($id)):
    echo "Raffle Processing";
  ?>
  <?php
    elseif (isset($_SESSION['lt_user_id']) && (!mysqli_num_rows($r2))):  ?>
  <a href="javascript:confirmTicketBuy(<?php echo $id; ?>)">
  Buy ticket
  </a>
  <?php
    else: ?>
  Buy ticket
  <?php
    endif;
  ?>
  </td>
  <td align="center" width="15%"><a href="index.php?id=<?php echo $id; ?>">Details</a></td>
  </tr>
  <?php
    if (isset($_GET['id']) && ($_GET['id'] == $id) && (!isset($_GET['go']))) {
  ?>
  </table>
  <table width="102%" border="1" align="center" cellpadding="0" cellspacing="0" class="datatable">
  <tr>
  <th width="10%">
    <a href="#" onMouseover="showfloatie(floattext[5], event, '#007AC9', 250, 25)" onMouseout="hidefloatie()" class="info">ID</a>
  </th>
  <th width="10%">
   <a href="#" onMouseover="showfloatie(floattext[6], event, '#007AC9', 250, 25)" onMouseout="hidefloatie()" class="info">Started</a>
 </th>
  <th width="10%">
  <a href="#" onMouseover="showfloatie(floattext[7], event, '#007AC9', 250, 25)" onMouseout="hidefloatie()" class="info">Duration</a></th>
  <th width="10%">
  <a href="#" onMouseover="showfloatie(floattext[8], event, '#007AC9', 250, 140)" onMouseout="hidefloatie()" class="info">Available to be Won (%)</a>
  </th>
  <th width="40%"> Description</th>
  <th width="10%">
  <a href="#" onMouseover="showfloatie(floattext[9], event, '#007AC9', 250, 25)" onMouseout="hidefloatie()" class="info">Tickets Purchased </a>
 </th>
  <th width="10%">
  <a href="#" onMouseover="showfloatie(floattext[10], event, '#007AC9', 250, 60)" onMouseout="hidefloatie()" class="info"> Total Amount</a>
 </th>
  </tr>
  <tr>
  <td width="10%"><?php echo $id; ?></td>
  <td align="center" width="10%"><?php echo date("m-d-Y", $started); ?></td>
  <td width="10%"><?php echo $duration; ?> day(s) </td>
  <td width="10%"><?php echo $available; ?> % </td>
  <td width="40%"><?php echo nl2br($description); ?></td>
  <td width="10%"><?php echo $tickets_qty; ?> ticket(s) </td>
  <td width="10%">$<?php echo ($ticket_price * $tickets_qty); ?></td>
  </tr>
  </table>
  <table width="100%" border="1" cellspacing="0" cellpadding="0">
  <?php
    }
  ?>
  <?php
    }
  ?>
  </table>
</div>
<?php

  finished_show();

  return(0);
}

function finished_show() {
  global $db_prefix;

/*  if ((isset($_GET['id'])) && (!is_numeric($_GET['id']))) {
    error_report(9);
    return(9);
  }
*/
  $r = my_query("select sum(amount) from " . $db_prefix . "messages");
  list($total_amount) = mysqli_fetch_row($r);

  $r = my_query("select l.id, l.started, l.duration, l.ticket_price, l.win_percentage, l.description, l.available, l.ended
  from " . $db_prefix . "lotteries l where l.ended > '0' order by l.started desc");
  ?>
  <div align="center">
  <h1>Closed Raffles </h1>
  <table width="100%" border="1" cellspacing="0" cellpadding="2" class="datatable">
  <tr>
  <th width="10%" bgcolor="#dddddd">End Date</th>
  <th width="20%" bgcolor="#dddddd">Ticket Price</th>
  <th width="20%" bgcolor="#dddddd">Percentage of Winners</th>
  <th width="20%" bgcolor="#dddddd">Available to be Won</th>
  <th width="30%" bgcolor="#dddddd" colspan="2">Options</th>
  </tr>
  <?php
    while (list($id, $started, $duration, $ticket_price, $win_percentage, $description, $available, $ended) = mysqli_fetch_row($r)) {
    $r1 = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
    $tickets_qty = mysqli_num_rows($r1);

  ?>
  <tr>
  <td align="center" width="10%"><?php echo date("m-d-Y", $ended); ?></td>
  <td width="20%">$<?php echo currency_display($ticket_price); ?></td>
  <td width="20%"><?php echo $win_percentage; ?> % </td>
  <td width="20%">$<?php echo currency_display($ticket_price * $tickets_qty * $available / 100); ?></td>
  <td align="center"><a href="index.php?go=winners&id=<?php echo $id; ?>">Show</a></td>
  <td align="center" width="15%"><a href="index.php?id=<?php echo $id; ?>">Details</a></td>
  </tr>
  <?php
    if (isset($_GET['id']) && ($_GET['id'] == $id)) {
  ?>
  </table>
  <table width="100%" border="1" cellspacing="0" cellpadding="2" class="datatable">
  <tr>
  <th width="10%">ID</th>
  <th width="10%">Started</th>
  <th width="10%">Duration</th>
  <th width="10%">Available to be Won</th>
  <th width="40%">Description</th>
  <th width="10%">Tickets Purchased </th>
  <th width="10%">Total Amount</th>
  </tr>
  <tr>
  <td width="10%"><?php echo $id; ?></font></td>
  <td align="center" width="10%"><?php echo date("m-d-Y", $started); ?></td>
  <td width="10%"><?php echo $duration; ?> day(s) </td>
  <td width="10%"><?php echo $available; ?> % </td>
  <td width="40%"><?php echo nl2br($description); ?></td>
  <td width="10%"><?php echo $tickets_qty; ?> ticket(s) </td>
  <td width="10%">$<?php echo ($ticket_price * $tickets_qty); ?></td>
  </tr>
  </table>
  <table width="100%" border="1" cellspacing="0" cellpadding="0">
  <?php
    }
  ?>
  <?php
    }
  ?>
  </table>
</div>
<?php

  return(0);
}

function forget_password() {
  global $db_prefix;

?>
<div align="center">
<h1>Enter Your Username Here:</h1>
<form action="index.php?go=pass2" method="post">
<table width="100%">
<tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Username:<br>
  </td>
  <td>
   <input type="text" name="username" size="30" <?php if (isset($_POST['username'])) echo "value=\"" . $_POST['username'] . "\"" ?>>
  </td>
</tr>
</table>
<?php
  $r = my_query("select * from " . $db_prefix . "image_verifications where pagename='forget password' and status='y'");
  if (mysqli_num_rows($r)) {
?>
 <table width="100%">
 <tr> <td width="10%">
  </td>
  <td width="30%" align="right" valign="top">
   Please enter number which you see:<br>
  </td>
  <td valign="top" width="200">
   <input type="text" name="checker" size="30">
  </td>
  <td valign="top" align="left">
   <img src="jpeg.php" border="0">
  </td>
 </tr>
 </table>
<?php
  }
?>
<br>
<input type="submit" value="  Send my password by email  ">
</form>
</div>
<?php


  return(0);
}

function forget_password2() {
  global $db_prefix;
  global $password_request_limit;

  while (list($key,$value) = each($_POST)) {
    if ("" == $value) {
      error_report(11);
      forget_password();
      exit;
    }

    if (strlen($value) > 35) {
      error_report(25);
      forget_password();
      return(25);
    }

    if (!preg_match("/^[a-zA-Z0-9\s]*$/", $value)) {
      error_report(7);
      echo "<br>$value";
      forget_password();
      return(7);
    }
  }

  $r = my_query("select * from " . $db_prefix . "image_verifications where pagename='forget password' and status='y'");
  if (mysqli_num_rows($r)) {
    if (md5(md5($_POST['checker'])) != $_SESSION['jpeg_code']) {
      error_report(28);
      forget_password();
      return(28);
    }
  }

  $r = my_query("select email, password, last_password_request from " . $db_prefix . "users where username='" . $_POST['username'] . "'");

  if (!mysqli_num_rows($r)) {
    error_report(19);
    forget_password();
    return(19);
  }

  list($email, $password, $last_password_request) = mysqli_fetch_row($r);

  if ($last_password_request > (time() - $password_request_limit*60)) {
    error_report(24);
    forget_password();
    return(24);
  }

  mail($email, "Password Reminder", "Hello, " . $_POST['username'] . "!\n\n" . "Your password is: $password");

  $r = my_query("update " . $db_prefix . "users set last_password_request = '" . time() . "' where username='" . $_POST['username'] . "'");

  ?>
  <div align="center">
  Password has been successfully sent to your e-mail!</div>
  <?php

  return(0);
}


function login() {

  global $db_prefix;
?>
<div align="center">
<h1>Login</h1>
<form action="index.php?go=login2" method="post">
<a href="index.php?go=pass">Forget password?</a>
<table width="100%">
<tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Username:<br>
  </td>
  <td>
    <div align="left">
      <input type="text" name="username" size="30" <?php if (isset($_POST['username'])) echo "value=\"" . $_POST['username'] . "\"" ?>>
    </div></td>
</tr>
<tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Password:<br>
  </td>
  <td>
    <div align="left">
      <input type="password" name="password" size="30">
    </div></td>
</tr>
</table>
<?php
   $r = my_query("select * from " . $db_prefix . "image_verifications where pagename='login' and status='y'");
   if (mysqli_num_rows($r)) {
?>
 <table width="100%">
 <tr>
  <td width="10%"></td>
  <td width="30%" align="right" valign="top">Enter the number exactly how you see it.  <br></td>
  <td valign="top"  align="left">
    
      <input type="text" name="checker" size="6">
      <img src="jpeg.php" border="0" style="border:#000000; border-style:solid; border-width:1px">  </td>
  </tr>
 </table>
<?php
  }
?>
 <input type="checkbox" name="remember" size="30">
 Remember My Password
 <br>
 <br>
<input type="submit" value="  Login  ">
</form>
</div>
  <?php


  return(0);
}

function login2() {
  global $db_prefix;
  global $link;

  while (list($key,$value) = each($_POST)) {
    if ("" == $value) {
      include_once("header.php");
      error_report(11);
      login();
      include_once("footer.php");
      exit;
    }

    if (strlen($value) > 35) {
      include_once("header.php");
      error_report(25);
      login();
      include_once("footer.php");
      return(25);
    }

    if (!preg_match("/^[a-zA-Z0-9\s]*$/", $value)) {
      include_once("header.php");
      error_report(7);
      echo "<br>$value";
      login();
      include_once("footer.php");
      return(7);
    }
  }

$r = my_query("select * from " . $db_prefix . "image_verifications where pagename='login' and status='y'");
if (mysqli_num_rows($r)) {
  if (md5(md5($_POST['checker'])) != $_SESSION['jpeg_code']) {
    $r = my_query("insert into " . $db_prefix . "failed_logins(username,date)
    values('" . $_POST['username'] . "' ,'" . time() . "')");
    error_report(28);
    login();
    return(28);
  }
}

  $username = $_POST['username'];
  $password = $_POST['password'];

  $r = my_query("select id from " . $db_prefix . "users where
  username='" . mysqli_real_escape_string($link, $username) . "'
  and password='" . mysqli_real_escape_string($link, $password) . "'");

  if (!mysqli_num_rows($r)) {
    include_once("header.php");

    $r = my_query("insert into " . $db_prefix . "failed_logins(username,date)
    values('" . $_POST['username'] . "','" . time() . "')");

    error_report(14);
    login();
    include_once("footer.php");
    exit;
  }

  list($id) = mysqli_fetch_row($r);

  $r = my_query("select until from " . $db_prefix . "suspended where user_id='$id'
  and (until ='0' or until>'" . time() . "')");
  if (mysqli_num_rows($r)) {
    list($until) = mysqli_fetch_row($r);
    if (!$until) {
      echo "Account suspended forever";
    }
    else {
      setcookie("lt_user_id","$id",$until);
      echo "Account suspended until " . date("m-d-Y H:i:s", $until);
    }
    exit;
  }

  if (isset($_COOKIE['lt_user_id'])) {
    setcookie("lt_user_id", "0", "1");
  }

  $r = my_query("delete from " . $db_prefix . "suspended where user_id='$id'");

  $r = my_query("select * from " . $db_prefix . "closed_accounts where user_id='$id'");
  if (mysqli_num_rows($r)) {
    echo "Account closed";
    exit;
  }

  $_SESSION['lt_user_id'] = $id;
  $_SESSION['lt_user_pass'] = $password;

  if (isset($_POST['remember'])) {
    setcookie("lt_user_login", md5(md5($username)),time()+100000000);
    setcookie("lt_user_password", md5(md5($password)),time()+100000000);
  }

  $_SESSION['lt_login'] = "1";

  header("Location: index.php");

  return(0);
}

function logout() {
  session_unset();

  setcookie("lt_user_login", "0","1");
  setcookie("lt_user_password", "0","1");

  header("Location: index.php");

  return(0);
}

function winners_show() {
  global $db_prefix;

  if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
//    error_report(9);
    lotteries_show();
    return(9);
  }

  $id = $_GET['id'];

  $r = my_query("select u.username from " . $db_prefix . "users u, " . $db_prefix . "tickets t where t.lottery_id='$id' and t.user_id=u.id and t.won>'0'");
  if (!mysqli_num_rows($r)) {
    lotteries_show();
    return(9);
  }
  ?>
  <div align="center">
  <h1>Winner List</h1>
  <?php
    while (list($username) = mysqli_fetch_row($r)) {
      echo $username . "<br>";
    }
  ?>
 
</div>
<?php

  return(0);
}

function show_messages() {
  global $db_prefix;
  global $link;

  if (!isset($_SESSION['lt_user_id'])) return(1);

  if (isset($_SESSION['lt_login']) && ("1" == $_SESSION['lt_login'])) {
    $_SESSION['lt_login'] = "0";
    $r = my_query("select username, last_login from " . $db_prefix . "users
    where id='" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "'");
    list($username, $last_login) = mysqli_fetch_row($r);
    $all_time = time() - $last_login;
    $hours = floor($all_time / 3600);
    $minutes = floor(($all_time - $hours * 3600) / 60);

    if (!$hours) {
      $hours = "";
    }
    else {
      $hours .= " hours ";
    }
    if (!$minutes) {
      $minutes = "";
    }
    else {
      $minutes .= " min ";
    }
    if (("" == $hours) && ("" == $minutes)) {
      $minutes = "less than 1 min ";
    }
    ?>
    <div align="center">
    Hello <?php echo $username; ?>, your last login was <?php echo $hours . $minutes . "ago"; ?>.
    </div>
    <?php
    $r = my_query("update " . $db_prefix . "users set last_login = '" . time() . "' where username='" . mysqli_real_escape_string($link, $username) . "'");
  }

  $r = my_query("select mestype, amount from " . $db_prefix . "messages where user_id='" . $_SESSION['lt_user_id'] . "' and displayed='n'");

  ?>
  <div align="center">
  <?php
  while(list($mestype, $amount) = mysqli_fetch_row($r)) {
    if ('w' == $mestype) {
       ?>
       <b>Congratulations! You won $<?php echo currency_display($amount); ?>!</b><br>
       <?php
    }
    else {
       ?>
       <b>Congratulations! One of your referrals has won. Your cut is $<?php echo currency_display($amount); ?></b>!<br>
       <?php
    }
  }
  ?>
  </div>
  <?php

  $r = my_query("update " . $db_prefix . "messages set displayed='y' where user_id='" . $_SESSION['lt_user_id'] . "'");

  return(0);
}

function ticket_buy() {
  global $siteurl;
  global $db_prefix;
  global $link;

  if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
//    error_report(9);
    lotteries_show();
    return(9);
  }

  $lottery_id = $_GET['id'];

  if (check_12hours_limit($lottery_id)) {
    error_report(20);
    lotteries_show();
    return(20);
  }

  $r = my_query("select ticket_price from " . $db_prefix . "lotteries where id='$lottery_id'");
  list($ticket_price) = mysqli_fetch_row($r);

  $r = my_query("select balance from " . $db_prefix . "users where id='" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "'");
  list($balance) = mysqli_fetch_row($r);

  if ($ticket_price > $balance) {
    error_report(21);
    lotteries_show();
    return(21);
  }

  $r = my_query("insert into " . $db_prefix ."tickets(user_id, lottery_id, price,date) values(
  '" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "',
  '" . $lottery_id . "','$ticket_price','" . time() . "')");

  $r = my_query("update " . $db_prefix . "users set balance=balance-'$ticket_price' where id='" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "'");

  lotteries_show();

  return(0);
}

function support_center() {
  global $db_prefix;

  if (!isset($_SESSION['lt_user_id'])) {
//    error_report(9);
    lotteries_show();
    return(9);
  }

  $r = my_query("select s.id,s.date, s.status, s.subject, c.name
  from " . $db_prefix . "support s, " . $db_prefix . "support_categories c
  where s.user_id = '" . $_SESSION['lt_user_id'] . "' and c.id=s.category
  order by date desc");
  ?>
  <div align="center">
  <h1>Support Center</h1>
  <table width="100%" border="1" cellspacing="0" cellpadding="2" class="datatable">
  <tr>
  <th width="10%" bgcolor="#dddddd">Date</th>
  <th width="20%" bgcolor="#dddddd">Category</th>
  <th width="40%" bgcolor="#dddddd">Subject</th>
  <th width="10%" bgcolor="#dddddd">Status</th>
  <th width="20%" bgcolor="#dddddd">Options</th>
  </tr>
  <?php
    while (list($id, $date, $status, $subject, $category) = mysqli_fetch_row($r)) {
  ?>
  <tr>
  <td align="center"><?php echo date("m-d-Y", $date); ?></td>
  <td>
  <?php
    echo $category;
  ?>
  </td>
  <td><?php echo $subject; ?></td>
  <td align="center">
  <?php
  if ('r' == $status) {
    echo "Resolved";
  }
  if ('o' == $status) {
    echo "Open";
  }
  ?>
  </td>
  <td align="center"><a href="index.php?go=support3&id=<?php echo $id; ?>">View</a></td>
  </tr>
  <?php
    }
  ?>
  </table>
<h1>Ask a Question</h1>
<form action="index.php?go=support2" method="post">
<table width="100%">
 <tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Category:<br>
  </td>
  <td align="left">
   <select name="category">
<?php
  $r = my_query("select id,name from " . $db_prefix . "support_categories");
  while (list($categ_id,$categ_name) = mysqli_fetch_row($r)) {
?>
   <option value="<?php echo $categ_id; ?>"><?php echo $categ_name; ?>
<?php
  }
?>
   </select>
  </td>
 </tr>
 <tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
  Subject:<br>
  </td>
  <td align="left">
   <input type="text" name="subject" size="30" maxlength="100">
  </td>
 </tr>
 <tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Question:<br>
  </td>
  <td align="left">
   <textarea name="message" rows="10" cols="50"></textarea>
  </td>
 </tr>

<?php
  $r = my_query("select * from " . $db_prefix . "image_verifications where pagename='support tickets' and status='y'");
  if (mysqli_num_rows($r)) {
?>

 <tr>
 <td width="10%"></td>
  <td width="30%" align="right" >Enter the number.<br>  </td>
  <td valign="top" align="left">
   <input type="text" name="checker" size="6">
   <img src="jpeg.php" border="0" style="border:#000000; border-style:solid; border-width:1px">  </td>
  </tr>
 
<?php
  }
?>
</table>
<center><br><input type="submit" value="  Add  "></center>
</form>
</div>
  <?php

  return(0);
}

function support_center2() {
  global $db_prefix;
  global $link;
  if (!isset($_SESSION['lt_user_id'])) {
//    error_report(9);
    lotteries_show();
    return(9);
  }

  $r = my_query("select * from " . $db_prefix . "image_verifications where pagename='support tickets' and status='y'");
  if (mysqli_num_rows($r)) {
    if (md5(md5($_POST['checker'])) != $_SESSION['jpeg_code']) {
      error_report(28);
      support_center();
      return(28);
    }
  }

  while (list($key,$value) = each($_POST)) {
    if ("" == $value) {
      error_report(11);
      support_center();
      return(11);
    }

    if (!preg_match("/^[a-zA-Z0-9\s]*$/", $value)) {
      error_report(7);
      echo "<br>$value";
      support_center();
      return(7);
    }
  }

  if (!isset($_POST['category'])) {
    lotteries_show();
    return(1);
  }

  $message = $_POST['message'];
  $message = substr($message, 0, 2000);
  $message = wordwrap($message, 150, "\n", 1);

  $r = my_query("insert into " . $db_prefix . "support(user_id,date, category, subject, message) values(
  '" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "',
  '" . time() . "',
  '" . mysqli_real_escape_string($link, $_POST['category']) . "',
  '" . mysqli_real_escape_string($link, $_POST['subject']) . "',
  '" . mysqli_real_escape_string($link, $message) . "'
  )");

  support_center();

  return(0);
}

function support_center3() {
  global $db_prefix;
  global $link;

  if (!isset($_SESSION['lt_user_id'])) {
//    error_report(9);
    lotteries_show();
    return(9);
  }

  if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
//    error_report(9);
    lotteries_show();
    return(9);
  }

  $r = my_query("select status,subject,message,answer from " . $db_prefix . "support
  where id='" . $_GET['id'] . "' and user_id='" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "'");

  if (!(list($status,$subject,$message,$answer) = mysqli_fetch_row($r))) {
    return(1);
  }

  ?>
  <div align="center">
  <table width="80%">
  <tr>
  <td width="40%" align="right" valign="top">
  <b>Subject:</b>
  </td>
  <td width="60%" align="left" valign="top">
  <?php echo $subject; ?>
  </td>
  </tr>
  <tr>
  <td width="40%" align="right" valign="top">
  <b>Question:</b>
  </td>
  <td width="60%" align="left" valign="top">
  <?php echo nl2br($message); ?>
  </td>
  </tr>
<?php
  if ("r" == $status) {
?>
  <tr>
  <td width="40%" align="right" valign="top">
  <b>Answer:</b>
  </td>
  <td width="60%" align="left" valign="top">
  <?php echo nl2br($answer); ?>
  </td>
  </tr>
<?php
  }
?>
  </table>
  <br>
  <input type="button" value="  Go Back  " onclick="location.href='index.php?go=support';">
  </div>
  <?php

  return(0);
}

function faq_show() {
  global $db_prefix;
  global $page_limit;
  global $link;

  $error_flag = false;

  if (isset($_POST['search_line'])) {
    if (strlen($_POST['search_line']) > 35) {
      error_report(26);
      $error_flag = true;
    }
    if (!preg_match("/^[a-zA-Z0-9\s]*$/", $_POST['search_line'])) {
      error_report(27);
      $error_flag = true;
    }
    if (!strlen($_POST['search_line'])) {
      $error_flag = true;
    }

    $where_clause = " where search like '" . mysqli_real_escape_string($link, $_POST['search_line']) . "%'
    or search like '%" . mysqli_real_escape_string($link, $_POST['search_line']) . "'
    or search like '%" . mysqli_real_escape_string($link, $_POST['search_line']) . "%'";
  }
  else {
    $where_clause = "";
  }
  if ($error_flag) {
    $where_clause = "";
  }

  if (isset($_GET['page'])) {
    $page = $_GET['page'];
  }
  else {
    $page = 1;
  }

  $r = my_query("select count(*) from " . $db_prefix . "faq" . $where_clause);
  list($pages_qty) = mysqli_fetch_row($r);

  if ($page == ceil($pages_qty / $page_limit)) {
    $limit = $pages_qty % $page_limit;
  }

  if ((!isset($limit)) || (!$limit)) {
    $limit = $page_limit;
  }

    $r = my_query("select question,answer from " . $db_prefix . "faq " . $where_clause . "order by date desc limit " . (($page - 1) * $page_limit) . ", $limit");

  ?>
  <div align="center">
  <h1>Frequently Asked Questions</h1>
  <?php
    if (isset($_POST['search_line'])) {
  ?>
  <h2>Search Results:</h2>
  <?php
    }
  ?>
  </div>
  <div align="left">
  <?php
    while (list($question,$answer) = mysqli_fetch_row($r)) {
      ?>
      <b>Question:</b><?php echo nl2br($question); ?><br>
      <b>Answer:</b><?php echo nl2br($answer); ?><br><br>
      <?php
    }
  ?>
    <br>
    </div>
    <hr>
  <div align="center">
  <?php
    for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
      <a href="index.php?go=faq&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
    }

  ?>
<form action="index.php?go=faq" method="post">
  Search FAQ Database: 
    <input type="text" name="search_line">
  <input type="submit" value="  Start Search  ">
  </form></div>
  <?php

  return(0);
}

if (isset($_GET['go']) && ('login2' == $_GET['go'])) {
  login2();
  exit;
}
if (isset($_GET['go']) && ('logout' == $_GET['go'])) {
  logout();
  exit;
}

function parse_get_params() {
if (isset($_GET['go'])) {
  $go = $_GET['go'];
  if ('login' == $go) {
  //if user logged in already, make sure he doesn't go to login
  if (isset($_SESSION['lt_user_id'])) {
exit("You are already logged in!");
}else{
  
    login();
    return(1);
	}
  }
    if ('archive' == $go) {
    archive_show();
    return(1);
  }
  if ('winners' == $go) {
    winners_show();
    return(1);
  }
  if ('buyticket' == $go) {
    ticket_buy();
    return(1);
  }
  if ('pass' == $go) {
    forget_password();
    return(1);
  }
  if ('pass2' == $go) {
    forget_password2();
    return(1);
  }
  if ('support' == $go) {
    support_center();
    return(1);
  }
  if ('support2' == $go) {
    support_center2();
    return(1);
  }
  if ('support3' == $go) {
    support_center3();
    return(1);
  }
  if ('faq' == $go) {
    faq_show();
    return(1);
  }
}
  lotteries_show();
//  finished_show();

  return(0);
}

include_once("header.php");

show_messages();

parse_get_params();

include_once("footer.php");

?>