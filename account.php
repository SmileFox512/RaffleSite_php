<?php

include_once("utils.php");
include_once("config.php");



if (!isset($_SESSION['lt_user_id'])) {
  header("Location: index.php?go=login");
}


include_once("header.php");

?>
<div align="right">
<a href="account.php">Options</a>&nbsp;
-&nbsp;<a href="account.php?go=balance">Add Funds </a>
-&nbsp;<a href="account.php?go=history">History</a>&nbsp;
-&nbsp;<a href="account.php?go=referrals">Referrals</a>&nbsp;
</div>
<?php

function options_edit() {
  global $db_prefix;
  global $siteurl;
  global $link;
  
  $r = my_query( "select username, name, email, city, paypal from " . $db_prefix . "users where id='" . $_SESSION['lt_user_id'] . "'");
  list($username, $name, $email, $city, $paypal) = mysqli_fetch_row($r);
?>
<center><h1>Account Options</h1></center>
<form action="account.php?go=editopts" method="post">
<table width="100%">
<tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Username:<br>
  </td>
  <td>
   <input type="text" name="username" value="<?php echo "$username"; ?>" size="30" disabled>
  </td>
</tr>
<tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Name:<br>
  </td>
  <td>
   <input type="text" name="name" value="<?php echo "$name"; ?>" size="30">
  </td>
</tr>
<tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   E-Mail:<br>
  </td>
  <td>
   <input type="text" name="email" value="<?php echo "$email"; ?>" size="30">
  </td>
</tr>
 <tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   City:<br>
  </td>
  <td>
   <input type="text" name="city" value="<?php echo "$city"; ?>" size="30">
  </td>
 </tr>
 <tr>
  <td width="10%">
  </td>
  <td width="30%" valign="top" align="right">
   Paypal:<br>
  </td>
  <td>
   <input type="text" name="paypal" value="<?php echo "$paypal"; ?>" size="30">
  </td>
 </tr>
</table>
<center><br><input type="submit" value="  Update  "></center>
</form>
<div align="center">
<h2>Your Referral Link:<br />
</h2>
<p><strong><?php echo $siteurl; ?>index.php?r=<?php echo $_SESSION['lt_user_id']; ?></strong>
  
    <?php
  $r = my_query( "select balance from " . $db_prefix . "users where id='" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "'");
  list($balance) = mysqli_fetch_row($r);

?>
</p>
<p><strong><u>(You will make 5% of what your referrals win!)</u></strong></p>
<h2>Account Balance:</h2>
  $<?php echo currency_display($balance); ?>
<h2>Total Winnings:</h2>
<?php
  $r = my_query( "select date, mestype, amount from " . $db_prefix . "messages
  where user_id='" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "'");
  ?>
  <table width="400" border="1" cellspacing="0" cellpadding="2">
  <tr>
  <th width="100" bgcolor="#BCBCBC">Date</th>
  <th width="200" bgcolor="#BCBCBC">Type</th>
  <th width="100" bgcolor="#BCBCBC">Amount</th>
  </tr>
  <?php
   $total_amount = 0;
    while (list($date, $mestype, $amount) = mysqli_fetch_row($r)) {
      if ("w" == $mestype) {
        $reason = "You won!";
      }
      else {
        $reason = "Your referral won!";
      }
      $amount = floor($amount*100)/100;
      $total_amount += $amount;
  ?>
  <tr>
  <td><?php echo date("m-d-Y",$date); ?></td>
  <td><?php echo $reason; ?></td>
  <td align="center"><?php echo currency_display($amount); ?></td>
  </tr>
  <?php
    }
  ?>
  <tr>
  <td colspan="2">
  <b>TOTAL AMOUNT:</b>
  </td>
  <td align="center">
  <b><?php echo currency_display($total_amount); ?></b>
  </td>
  </tr>
  </table>
  <h2>Failed Logins During Last 24 Hours:</h2>
  <table width="400" border="1" cellspacing="0" cellpadding="2">
  <tr>
  <th width="400" bgcolor="#BCBCBC">Failed login time</th>
  </tr>
<?php
  $r = my_query( "select date from " . $db_prefix . "failed_logins
  where username='" . mysqli_real_escape_string($link, $username) . "' and date > '" .  (time() - 24*60*60) . "' order by date desc");
  while (list($date) = mysqli_fetch_row($r)) {
    $all_time = time() - $date;
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
  <tr>
  <td align="center"><?php echo $hours . $minutes . "ago"; ?></td>
  </tr>
  <?php
    }
  ?>
  </table>
</div>
<?php

  return(0);
}

function options_edit2() {
  global $db_prefix;

  while (list($key,$value) = each($_POST)) {
    if (("" == $value)) {
      error_report(11);
      options_edit();
      return(11);
    }

    if (strlen($value) > 35) {
      error_report(25);
      options_edit();
      return(25);
    }

    if (("email" == $key) || ("paypal" == $key)) {
      if (valid_email($value)) {
        $value = str_replace("@","",$value);
        $value = str_replace(".","",$value);
      }
      else {
        error_report(1);
        options_edit();
        return(1);
      }
    }

    if (!preg_match("/^[a-zA-Z0-9\s]*$/", $value)) {
      error_report(7);
      echo "<br>$value";
      options_edit();
      return(7);
    }
  }

  $r = my_query( "update " . $db_prefix . "users set
  name='". mysqli_escape_string($_POST['name']) ."',
  email='". mysqli_escape_string($_POST['email']) ."',
  city='". mysqli_escape_string($_POST['city']) ."',
  paypal='". mysqli_escape_string($_POST['paypal']) ."'
  where id='" . $_SESSION['lt_user_id'] . "'");

  options_edit();

  return(0);
  
}

function history_show() {
  global $db_prefix;

  $r = my_query("select id, lottery_id, price, won from " . $db_prefix . "tickets
                 where user_id='" . $_SESSION['lt_user_id'] . "' order by id");
  ?>
  <div align="center">
  <h1>Ticket History</h1>
  <table width="100%" border="1" cellspacing="0" cellpadding="2">
  <tr>
  <th width="33%" bgcolor="#BCBCBC">Raffle ID</th>
  <th width="34%" bgcolor="#BCBCBC">Price</th>
  <th width="33%" bgcolor="#BCBCBC">Won</th>
  </tr>
  <?php
    while (list($id, $lottery_id, $price, $won) = mysqli_fetch_row($r)) {
  ?>
  <tr>
  <td>#<?php echo $lottery_id; ?></td>
  <td>$<?php echo $price; ?></td>
  <td>$<?php echo $won; ?></td>
  </tr>
  <?php
    }
  ?>
  </table>
</div>
<?php

  return(0);
}

function balance_show() {
  global $db_prefix;
  global $paypal;
  global $siteurl;
  global $link;

  $r = my_query( "select balance from " . $db_prefix . "users where id='" . mysqli_real_escape_string($link, $_SESSION['lt_user_id']) . "'");
  list($balance) = mysqli_fetch_row($r);

  $return_url = $siteurl . "account.php?go=deposit";
  ?>
  <div align="center">
  <h1>Add Funds </h1>
  Your current account balance: $<?php echo currency_display($balance); ?><p>
  <?php
    echo "<center><form name=\"paypal form\" method=\"post\" action= \"https://www.paypal.com/cgi-bin/webscr\">\n";
    echo "<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\n";
    echo "<input type=\"hidden\" name=\"business\" value=\"$paypal\">\n";
    echo "<input type=\"hidden\" name=\"item_name\" value=\"Deposit\">\n";
    echo "<input type=\"hidden\" name=\"item_number\" value=\"1\">\n";
    echo "<input type=\"hidden\" name=\"rm\" value=\"2\">\n";
    echo "<input type=\"hidden\" name=\"return\" value=\"$return_url\">\n";
    echo "<input type=\"hidden\" name=\"no_shipping\" value=\"1\">\n";
    echo "<input type=\"submit\" value=\"Click Here to Add Funds to Your Account\" style=\"border: 1px solid #D6DDE6;\">\n";
    echo "</form></center>";
	echo "<br />Please make sure that the PayPal you pay with matches the PayPal you entered upon registration <br />(This can be edited in Account Options).";
	echo "<br />Also note that, PayPal charges us a fee for every transaction. So you will be credited a little less than you deposit.";
	//echo "To all users who don't have paypal (and are going to pay with a credit card, debit card, etc) or do not want to pay with existing paypal funds: <br /> Please enter the email address (you entered into the paypal field when you signed up) into the Note field!";
  ?>
  </div>
  <?php

  return(0);
}

function balance_deposit() {
  ?>
  Thank you,<br>
  Money has successfully received.<br>
  Your balance will be increased by admin later.
  <?php

  return(0);
}

function referrals_show() {
  global $db_prefix;

  ?>
  <div align="center">
  <h1>Referral Information:</h1>
  <?php
  $r = my_query( "select id, username from " . $db_prefix . "users where referrer_id='" . $_SESSION['lt_user_id'] . "' order by username");
  while (list($id, $username) = mysqli_fetch_row($r)) {
    echo "$username<br>";
  }
  ?>
  <h2>Tickets Purchased (by your referrals):</h2>
  <?php
    $r = my_query( "select u.username, l.id, l.started, l.duration, l.ticket_price
                   from " . $db_prefix . "users u, " . $db_prefix . "lotteries l, " . $db_prefix . "tickets t
                   where u.referrer_id='" . $_SESSION['lt_user_id'] ."'
                   and t.user_id=u.id and t.lottery_id=l.id and l.ended='0' order by u.username");
  ?>
    <table width="100%" border="1" cellspacing="0" cellpadding="2">
  <tr>
  <th width="20%" bgcolor="#BCBCBC">Name</th>
  <th width="20%" bgcolor="#BCBCBC">Raffle ID</th>
  <th width="20%" bgcolor="#BCBCBC">Started</th>
  <th width="20%" bgcolor="#BCBCBC">Duration</th>
  <th width="20%" bgcolor="#BCBCBC">Total amount</th>
  </tr>
  <?php
    while (list($username, $id, $started, $duration, $ticket_price) = mysqli_fetch_row($r)) {
      $r1 = my_query( "select id from " . $db_prefix . "tickets where lottery_id='$id'");
      $tickets_qty = mysqli_num_rows($r1);
      $amount = $tickets_qty * $ticket_price;
  ?>
  <tr>
  <td><?php echo $username; ?></td>
  <td><?php echo $id; ?></td>
  <td><?php echo date("m-d-Y",$started); ?></td>
  <td><?php echo $duration; ?></td>
  <td><?php echo $amount; ?></td>
  </tr>
  <?php
    }
  ?>
  </table>

  <h2>Winners:</h2>
  <?php
    $r = my_query( "select u.username, l.id, l.started, l.ended, l.ticket_price, t.won
                   from " . $db_prefix . "users u, " . $db_prefix . "lotteries l, " . $db_prefix . "tickets t
                   where u.referrer_id='" . $_SESSION['lt_user_id'] ."'
                   and t.user_id=u.id and t.lottery_id=l.id and t.won>'0' order by u.username");
  ?>
  <table width="100%" border="1" cellspacing="0" cellpadding="2">
  <tr>
  <th width="20%" bgcolor="#BCBCBC">Name</th>
  <th width="20%" bgcolor="#BCBCBC">Raffle ID</th>
  <th width="20%" bgcolor="#BCBCBC">Started</th>
  <th width="20%" bgcolor="#BCBCBC">Ended</th>
  <th width="20%" bgcolor="#BCBCBC">Profit</th>
  </tr>
  <?php
    while (list($username, $id, $started, $ended, $ticket_price, $won) = mysqli_fetch_row($r)) {
      $r1 = my_query( "select id from " . $db_prefix . "tickets where lottery_id='$id'");
      $tickets_qty = mysqli_num_rows($r1);
  ?>
  <tr>
  <td><?php echo $username; ?></td>
  <td><?php echo $id; ?></td>
  <td><?php echo date("m-d-Y",$started); ?></td>
  <td><?php echo date("m-d-Y",$ended); ?></td>
  <td><?php echo $won; ?></td>
  </tr>
  <?php
    }
  ?>
  </table>

  </div>
  <?php

  return(0);
}

function parse_get_params() {
if (isset($_GET['go'])) {
  $go = $_GET['go'];
  if ('editopts' == $go) {
    options_edit2();
    return(1);
  }
  if ('history' == $go) {
    history_show();
    return(1);
  }
  if ('referrals' == $go) {
    referrals_show();
    return(1);
  }
  if ('balance' == $go) {
    balance_show();
    return(1);
  }
  if ('deposit' == $go) {
    balance_deposit();
    return(1);
  }
}
  options_edit();
  return(0);
}

parse_get_params();

include_once("footer.php");

?>