<?php

include_once("utils.php");
@session_start();
if ($_SESSION['aname'] == 4) {


  if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header("WWW-Authenticate: Basic realm=\"Cool Stuff\"");
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access Denied';
    exit;
  } else {
    if (($_SERVER['PHP_AUTH_PW'] != $admin_password) || ($_SERVER['PHP_AUTH_USER'] != $admin_username)) {
      header('HTTP/1.0 401 Unauthorized');
      echo "<h1>Access Denied</h1>";
      exit;
    }
  }

?>
  <style>
    a:link {
      color: black;
    }

    a:visited {
      color: black;
    }

    a:hover {
      text-decoration: none;
      background: white
    }

    a:active {
      color: navy;
    }

    table {
      border: 1px solid #333;
      border-collapse: collapse;
    }

    td {
      border: 1px solid #D6DDE6;

    }

    th {
      border: 1px solid #828282;
      background: #E8EBF9
    }

    h1 {
      font-family: verdana, arial, sans-serif;
    }

    hr {
      height: 1px;
      color: #000033;
    }

    .help {
      width: 55%;
      text-align: left;
      border: 1px solid #828282;
      padding: 5px;
      background: #F0F0FF;
    }
  </style>

  <div align="center" style="border: 1px solid #333; padding:4px">
    <div style="background:#98C8F8; padding:2px; font-family: verdana, arial, sans-serif; font-size:80%">
      <a href="flvby.php">Open Raffles </a>&nbsp; &nbsp;
      <a href="flvby.php?go=users">Users</a>&nbsp; &nbsp;
      <a href="flvby.php?go=finished">Finished Raffles </a>&nbsp; &nbsp;
      <a href="flvby.php?go=archive">Archive</a>&nbsp; &nbsp;
      <a href="flvby.php?go=tickets">Tickets</a>&nbsp; &nbsp;
      <a href="flvby.php?go=winnerslist">Winner List</a>&nbsp; &nbsp;
      <a href="flvby.php?go=winnerslistcsv">Winner List (CSV)</a>&nbsp; &nbsp;
      <a href="flvby.php?go=parser">PayPal Parser</a>&nbsp; &nbsp;
      <a href="flvby.php?go=frauds">Potential Frauds</a>&nbsp; &nbsp;
      <a href="flvby.php?go=suspended">Suspended users</a>&nbsp; &nbsp;
      <a href="flvby.php?go=fictitious">Fraudulent Registrations</a>&nbsp; &nbsp;
      <a href="flvby.php?go=profits">Profits</a>&nbsp; &nbsp;
      <a href="flvby.php?go=support">Support Tickets</a>&nbsp; &nbsp;
      <a href="flvby.php?go=userdeposit">User Deposits</a>&nbsp; &nbsp;
      <a href="flvby.php?go=closed">Closed Accounts</a>&nbsp; &nbsp;
      <a href="flvby.php?go=idle">Idle Accounts</a>&nbsp; &nbsp;
      <a href="flvby.php?go=credits">Credit Accounts</a>&nbsp; &nbsp;
      <a href="flvby.php?go=iv">Image Verifications</a>&nbsp; &nbsp;
      <a href="flvby.php?go=stats">Statistics</a>&nbsp; &nbsp;
      <a href="flvby.php?go=faq">Frequently Asked Questions</a>&nbsp; &nbsp;
    </div>

  </div>



  <?php

  function accounts_idle()
  {
    global $db_prefix;
    global $page_limit;

    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }

    if (isset($_POST['period']) && ($_POST['period'] != "")) {
      $period = $_POST['period'];
    } elseif (isset($_GET['period']) && ($_GET['period'] != "")) {
      $period = $_GET['period'];
    } else {
      $period = 30;
    }

    $visit_limit = time() - $period * 24 * 60 * 60;

    $r = my_query("select count(*) from " . $db_prefix . "users u
  where u.last_visit<'$visit_limit' order by u.username");

    list($pages_qty) = mysqli_fetch_row($r);

    if ($page == ceil($pages_qty / $page_limit)) {
      $limit = $pages_qty % $page_limit;
    }

    if ((!isset($limit)) || (!$limit)) {
      $limit = $page_limit;
    }

    $r = my_query("select u.id, u.username, u.name, u.email, u.city, u.referrer_id, u.paypal, u.last_visit, u.balance
  from " . $db_prefix . "users u where u.last_visit<'$visit_limit'
  order by u.username limit " . (($page - 1) * $page_limit) . ", $limit");

  ?>
    <div align="center">
      <h1>Idle Accounts</h1>
      <form action="flvby.php?go=idle" method="post">
        <b>Find Accounts Idle For </b>
        <input type="text" name="period" size="10" value="<?php echo $period; ?>">
        <b>Days</b>
        <input type="submit" value="  Refresh  ">
      </form>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="8%" bgcolor="#dddddd">Username</th>
          <th width="8%" bgcolor="#dddddd">Name</th>
          <th width="8%" bgcolor="#dddddd">E-Mail</th>
          <th width="6%" bgcolor="#dddddd">City</th>
          <th width="8%" bgcolor="#dddddd">Referrer</th>
          <th width="8%" bgcolor="#dddddd">PayPal</th>
          <th width="6%" bgcolor="#dddddd">Active</th>
          <th width="8%" bgcolor="#dddddd">Referrals</th>
          <th width="8%" bgcolor="#dddddd">Won ($)</th>
          <th width="8%" bgcolor="#dddddd">Balance</th>
          <th width="24%" bgcolor="#dddddd" colspan="3">Options</th>
        </tr>
        <?php
        while (list($id, $username, $name, $email, $city, $referrer_id, $paypal, $last_visit, $balance) = mysqli_fetch_row($r)) {
          $r3 = my_query("select * from " . $db_prefix . "closed_accounts where user_id='$id'");
          if (mysqli_num_rows($r3)) {
            continue;
          }
          if ($referrer_id) {
            $r1 = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
            list($referrer_username) = mysqli_fetch_row($r1);
          } else {
            $referrer_username = "None";
          }
        ?>
          <tr>
            <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
            <td><?php echo $name; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo $city; ?></td>
            <td><?php echo $referrer_username; ?></td>
            <td><?php echo $paypal; ?></td>
            <td align="center">
              <?php if ((time() - $last_visit) > 5 * 60) { ?>
                <font color="#cc3232"><b>NO</b></font>
              <?php
              } else {
              ?>
                <font color="#22cc32"><b>YES</b></font>
              <?php } ?>
            </td>
            <td>
              <?php
              $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$id'");
              list($qty) = mysqli_fetch_row($r2);
              echo $qty;
              ?>
            </td>
            <td>
              <?php
              $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$id'");
              list($won) = mysqli_fetch_row($r3);
              if ("" == $won) {
                $won = 0;
              }
              echo currency_display($won);
              ?>
            <td><?php echo currency_display($balance); ?></td>
            </td>
            <td align="center"><a href="flvby.php?go=suspend&id=<?php echo $id; ?>">Suspend</a></td>
            <td align="center"><a href="flvby.php?go=deluser&id=<?php echo $id; ?>">Delete</a></td>
            <td align="center"><a href="flvby.php?go=closeacc&id=<?php echo $id; ?>">Close</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <br>
      <hr>
      <?php
      for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
        <a href="flvby.php?go=idle&page=<?php echo $i; ?>&period=<?php echo $period; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
      }
      ?><p>
      <div class="help">-This page allows the Admin to find users who have not logged in a certain number of days.<br />-The Admin can suspend a user or close his account.</div>
    </div>
  <?php

    return (0);
  }

  function accounts_closed()
  {
    global $db_prefix;
    global $page_limit;

    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }

    $r = my_query("select count(*)
  from " . $db_prefix . "closed_accounts c, " . $db_prefix . "users u
  where u.id=c.user_id order by u.username");
    list($pages_qty) = mysqli_fetch_row($r);

    if ($page == ceil($pages_qty / $page_limit)) {
      $limit = $pages_qty % $page_limit;
    }

    if ((!isset($limit)) || (!$limit)) {
      $limit = $page_limit;
    }

    $r = my_query("select c.id, u.id, u.username
  from " . $db_prefix . "closed_accounts c, " . $db_prefix . "users u
  where u.id=c.user_id order by u.username
  limit " . (($page - 1) * $page_limit) . ", $limit");
  ?>
    <div align="center">
      <h1>Closed Accounts</h1>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="70%" bgcolor="#dddddd">Username</th>
          <th width="30%" bgcolor="#dddddd">Options</th>
        </tr>
        <?php
        while (list($id, $user_id, $username) = mysqli_fetch_row($r)) {
        ?>
          <tr>
            <td><a href="flvby.php?go=userdetails&id=<?php echo $user_id; ?>"><?php echo $username; ?></a></td>
            <td align="center"><a href="flvby.php?go=reopenacc&id=<?php echo $id; ?>">Re-open</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <br>
      <hr>
      <?php
      for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
        <a href="flvby.php?go=closed&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
      }
      ?><p>
      <div class="help">-This page displays user accounts that have been closed by the Admin due to inactivity (the user not logging in for a specific amount of time). </div>
    </div>
  <?php

    return (0);
  }


  function accounts_close()
  {
    global $db_prefix;
    global $link;
    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $r = my_query("insert into " . $db_prefix . "closed_accounts(user_id) values('" . mysqli_real_escape_string($link, $_GET['id']) . "')");

    accounts_closed();

    return (0);
  }

  function accounts_reopen()
  {
    global $db_prefix;
    global $link;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $r = my_query("delete from " . $db_prefix . "closed_accounts where id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

    accounts_closed();

    return (0);
  }

  function frauds_list()
  {
    global $db_prefix;
    global $page_limit;
    global $link;

    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }

    $r = my_query("select count(*)
  from " . $db_prefix . "users u, " . $db_prefix . "frauds f
  where u.id=f.user_id and f.incorrect <= f.correct
  order by u.username");
    list($pages_qty) = mysqli_fetch_row($r);

    if ($page == ceil($pages_qty / $page_limit)) {
      $limit = $pages_qty % $page_limit;
    }

    if ((!isset($limit)) || (!$limit)) {
      $limit = $page_limit;
    }

    $r = my_query("select u.id, u.username, u.name, u.email, u.city, u.paypal,
  f.reason, f.incorrect, f.correct, u.balance
  from " . $db_prefix . "users u, " . $db_prefix . "frauds f
  where u.id=f.user_id and f.incorrect <= f.correct
  order by u.username limit " . (($page - 1) * $page_limit) . ", $limit");
  ?>
    <div align="center">
      <h1>Potential Frauds</h1>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="8%" bgcolor="#dddddd">Username</th>
          <th width="8%" bgcolor="#dddddd">Name</th>
          <th width="8%" bgcolor="#dddddd">E-Mail</th>
          <th width="8%" bgcolor="#dddddd">City</th>
          <th width="8%" bgcolor="#dddddd">PayPal</th>
          <th width="8%" bgcolor="#dddddd">Referrals</th>
          <th width="8%" bgcolor="#dddddd">Won ($)</th>
          <th width="8%" bgcolor="#dddddd">Balance</th>
          <th width="10%" bgcolor="#dddddd">Reason</th>
          <th width="8%" bgcolor="#dddddd">Incorrect value</th>
          <th width="8%" bgcolor="#dddddd">Correct value</th>
          <th width="10%" bgcolor="#dddddd">Options</th>
        </tr>
        <?php
        while (list($id, $username, $name, $email, $city, $paypal, $reason, $incorrect, $correct, $balance) = mysqli_fetch_row($r)) {
          if ("au" == $reason) {
            $correct = "&nbsp;";
            $incorrect = "&nbsp;";
            $reason = "Invalid auction ID";
          } else {
            $reason = "Incorrect amount";
          }

        ?>
          <tr>
            <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
            <td><?php echo $name; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo $city; ?></td>
            <td><?php echo $paypal; ?></td>
            <td>
              <?php
              $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$id'");
              list($qty) = mysqli_fetch_row($r2);
              echo $qty;
              ?>
            </td>
            <td>
              <?php
              $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$id'");
              list($won) = mysqli_fetch_row($r3);
              if ("" == $won) {
                $won = 0;
              }
              echo currency_display($won);
              ?>
            </td>
            <td><?php echo currency_display($balance); ?></td>
            <td><?php echo $reason; ?></td>
            <td><?php echo $incorrect; ?></td>
            <td><?php echo $correct; ?></td>
            <td align="center"><a href="flvby.php?go=delfraud&id=<?php echo $id; ?>">Delete from list</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <br>
      <hr>
      <?php
      for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
        <a href="flvby.php?go=frauds&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
      }
      ?>
      <br><br>
      <a href="flvby.php?go=fraudchecker">Check users with same IP</a>
      <p>
      <div class="help">-This page displays users who have potentially tried to cheat the website. <br />-The <em>Reason</em> column explains why a user is in this list.<br />-The Admin can delete a user's name from this list.</div>
    </div>
  <?php

    return (0);
  }

  function frauds_delete()
  {
    global $db_prefix;
    global $link;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $r = my_query("delete from " . $db_prefix . "frauds where user_id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

    frauds_list();

    return (0);
  }

  function check_ticket_exist($user_id, $lottery_id)
  {
    global $db_prefix;

    $r = my_query("select id from " . $db_prefix . "tickets where user_id='$user_id' and lottery_id='$lottery_id'");

    return (mysqli_num_rows($r));
  }

  function csv_show()
  {
    global $db_prefix;
    echo "<p>";
    $r = my_query("select d.id, u.id, u.username, u.name, u.email ,u.paypal, sum(d.amount)
                 from " . $db_prefix . "users u, " . $db_prefix . "debts d
                 where d.user_id=u.id group by u.id");
    while (list($id, $gag, $username, $name, $email, $paypal, $amount) = mysqli_fetch_row($r)) {
      echo "$paypal," . currency_display($amount) . "<br>";
    }
    echo '<p><div align="center"><div class="help">-This page displays the winners who need to be paid (users\' paypal, amount in $). This page is a very simplified version of (<em>Winner List</em>). It exists in case the Admin wants to create a program that will mass pay users. In order to delete a user the program will need to request this URL: <br /> "' . $PHP_SELF . '?go=deluser&paypal=UsersPayPal@site.com" </div></div>';
    return (0);
  }

  function debts_show()
  {
    global $db_prefix;

    $r = my_query("select d.id, u.id, u.username, u.name, u.email ,u.paypal, sum(d.amount), u.balance
                 from " . $db_prefix . "users u, " . $db_prefix . "debts d
                 where d.user_id=u.id group by u.id");
  ?>
    <div align="center">
      <h1>Winner List</h1>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="15%" bgcolor="#dddddd">Username</th>
          <th width="15%" bgcolor="#dddddd">Name</th>
          <th width="10%" bgcolor="#dddddd">E-Mail</th>
          <th width="10%" bgcolor="#dddddd">PayPal</th>
          <th width="10%" bgcolor="#dddddd">Referrals</th>
          <th width="10%" bgcolor="#dddddd">Won ($)</th>
          <th width="10%" bgcolor="#dddddd"><u>Amount</u></th>
          <th width="10%" bgcolor="#dddddd">Balance</th>
          <th width="10%" bgcolor="#dddddd">Options</th>
        </tr>
        <?php
        while (list($id, $user_id, $username, $name, $email, $paypal, $amount, $balance) = mysqli_fetch_row($r)) {
        ?>
          <tr>
            <td><a href="flvby.php?go=userdetails&id=<?php echo $user_id; ?>"><?php echo $username; ?></a></td>
            <td><?php echo $name; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo $paypal; ?></td>
            <td>
              <?php
              $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$user_id'");
              list($qty) = mysqli_fetch_row($r2);
              echo $qty;
              ?>
            </td>
            <td>
              <?php
              $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$user_id'");
              list($won) = mysqli_fetch_row($r3);
              if ("" == $won) {
                $won = 0;
              }
              echo currency_display($won);
              ?>
            </td>
            <td><?php echo currency_display($amount); ?></td>
            <td><?php echo currency_display($balance); ?></td>
            <td align="center"><a href="flvby.php?go=deldebt&id=<?php echo $user_id; ?>">Delete</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <p>
      <div class="help">-This page displays the winners of finished raffles and how much they need to be paid. The <em>amount</em> column displays the amount a user needs to be paid. Use this column to determine how much you need to send the user. <br />
        -After you pay a user, click <em>Delete</em>! The purpose of this page is to show the Admin how much money he needs to send and to who.</div>
    </div>
  <?php

    return (0);
  }


  function debts_delete($id = 0)
  {
    global $db_prefix;
    global $link;
    if ((!$id) && ((!isset($_GET['id'])) || (!is_numeric($_GET['id'])))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    if (!$id) {
      $r = my_query("delete from " . $db_prefix . "debts where user_id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");
      debts_show();
    } else {
      $r = my_query("delete from " . $db_prefix . "debts where user_id='" . mysqli_real_escape_string($link, $id) . "'");
    }

    return (0);
  }


  function users_show()
  {
    global $db_prefix;
    global $page_limit;
    global $link;

    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }

    $where_clause = "";

    if (isset($_POST['id']) && ($_POST['id'] != "")) {
      if ("" == $where_clause) {
        $where_clause .= " where id='" . mysqli_real_escape_string($link, $_POST['id']) . "'";
      } else {
        $where_clause .= " and id='" . mysqli_real_escape_string($link, $_POST['id']) . "'";
      }
    }
    if (isset($_POST['username']) && ($_POST['username'] != "")) {
      if ("" == $where_clause) {
        $where_clause .= " where username like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['username'])) . "'";
      } else {
        $where_clause .= " and username like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['username'])) . "'";
      }
    }
    if (isset($_POST['name']) && ($_POST['name'] != "")) {
      if ("" == $where_clause) {
        $where_clause .= " where name like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['name'])) . "'";
      } else {
        $where_clause .= " and name like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['name'])) . "'";
      }
    }
    if (isset($_POST['email']) && ($_POST['email'] != "")) {
      if ("" == $where_clause) {
        $where_clause .= " where email like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['email'])) . "'";
      } else {
        $where_clause .= " and email like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['email'])) . "'";
      }
    }
    if (isset($_POST['city']) && ($_POST['city'] != "")) {
      if ("" == $where_clause) {
        $where_clause .= " where city like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['city'])) . "'";
      } else {
        $where_clause .= " and city like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['city'])) . "'";
      }
    }
    if (isset($_POST['paypal']) && ($_POST['paypal'] != "")) {
      if ("" == $where_clause) {
        $where_clause .= " where paypal like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['paypal'])) . "'";
      } else {
        $where_clause .= " and paypal like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['paypal'])) . "'";
      }
    }
    if (isset($_POST['referrer']) && ($_POST['referrer'] != "")) {
      $r = my_query("select id from " . $db_prefix . "users where username like '" . mysqli_real_escape_string($link, str_replace("*", "%", $_POST['referrer'])) . "'");
      list($referrer_id) = mysqli_fetch_row($r);
      if ("" == $where_clause) {
        $where_clause .= " where referrer_id='" . mysqli_real_escape_string($link, $referrer_id) . "'";
      } else {
        $where_clause .= " and referrer_id='" . mysqli_real_escape_string($link, $referrer_id) . "'";
      }
    }

    $r = my_query("select count(*) from " . $db_prefix . "users " . $where_clause . " order by username");
    list($pages_qty) = mysqli_fetch_row($r);

    if ($page == ceil($pages_qty / $page_limit)) {
      $limit = $pages_qty % $page_limit;
    }

    if ((!isset($limit)) || (!$limit)) {
      $limit = $page_limit;
    }

    $r = my_query("select id, username, name, email, city, referrer_id, paypal, last_visit, balance
  from " . $db_prefix . "users " . $where_clause . " order by username limit " . (($page - 1) * $page_limit) . ", $limit");

  ?>
    <div align="right">
      <b>Total Number of Users: <?php echo $pages_qty; ?></b>
    </div>
    <div align="center">
      <h1>Manage Users</h1>
      <form action="flvby.php?go=users" method="post" style="border: 1px solid #333; width:80%">
        <p><b>Search User Database:</b> <br />(Use * for wildcard) </p>
        <p>
          ID:
          <input type="text" name="id" size="10">
          Username:
          <input type="text" name="username" size="10">
          Name:
          <input type="text" name="name" size="10">
          E-Mail:
          <input type="text" name="email" size="10">

          City:
          <input type="text" name="city" size="10">
          Referrer:
          <input type="text" name="referrer" size="10">
          PayPal:
          <input type="text" name="paypal" size="10">
          <br><br />
          <input type="submit" value="  Search  ">
        </p>
      </form>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="10%" bgcolor="#dddddd">Username</th>
          <th width="10%" bgcolor="#dddddd">Name</th>
          <th width="8%" bgcolor="#dddddd">E-Mail</th>
          <th width="8%" bgcolor="#dddddd">City</th>
          <th width="8%" bgcolor="#dddddd">Referrer</th>
          <th width="8%" bgcolor="#dddddd">PayPal</th>
          <th width="8%" bgcolor="#dddddd">Active</th>
          <th width="8%" bgcolor="#dddddd">Referrals</th>
          <th width="8%" bgcolor="#dddddd">Won ($)</th>
          <th width="8%" bgcolor="#dddddd">Balance</th>
          <th width="16%" bgcolor="#dddddd" colspan="2">Options</th>
        </tr>
        <?php
        while (list($id, $username, $name, $email, $city, $referrer_id, $paypal, $last_visit, $balance) = mysqli_fetch_row($r)) {
          if ($referrer_id) {
            $r1 = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
            list($referrer_username) = mysqli_fetch_row($r1);
          } else {
            $referrer_username = "None";
          }
        ?>
          <tr>
            <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
            <td><?php echo $name; ?></td>
            <td><?php echo $email; ?></td>
            <td><?php echo $city; ?></td>
            <td><?php echo $referrer_username; ?></td>
            <td><?php echo $paypal; ?></td>
            <td align="center">
              <?php if ((time() - $last_visit) > 5 * 60) { ?>
                <font color="#cc3232"><b>NO</b></font>
              <?php
              } else {
              ?>
                <font color="#22cc32"><b>YES</b></font>
              <?php } ?>
            </td>
            <td>
              <?php
              $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$id'");
              list($qty) = mysqli_fetch_row($r2);
              echo $qty;
              ?>
            </td>
            <td>
              <?php
              $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$id'");
              list($won) = mysqli_fetch_row($r3);
              if ("" == $won) {
                $won = 0;
              }
              echo currency_display($won);
              ?>
            <td><?php echo currency_display($balance); ?></td>
            </td>
            <td align="center"><a href="flvby.php?go=suspend&id=<?php echo $id; ?>">Suspend</a></td>
            <td align="center"><a href="flvby.php?go=deluser&id=<?php echo $id; ?>">Delete</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <br>
      <hr>
      <?php
      for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
        <a href="flvby.php?go=users&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
      }
      ?>
      <br>
      <br>
      <a href="flvby.php?go=fraudchecker">Check users with same IP</a>
      <p>
      <div class="help">-This page gives a complete listing of the website's users. Clicking on the username will provide more information about each user. <br />
        -The Admin is able to search the user database by entering info into <em>one</em> of the boxes. The script supports wildcards (example: *@yahoo.com). <br />
        -The Admin can also check the database for users who signed up using the same IP. </div>
    </div>
  <?php

    return (0);
  }

  function users_delete()
  {
    global $db_prefix;
    global $link;

    if (isset($_GET['paypal'])) {
      $r = my_query("select id from " . $db_prefix . "users where paypal='" . mysqli_real_escape_string($link, $_GET['paypal']) . "'");
      list($id) = mysqli_fetch_row($r);
      debts_delete($id);
      return (0);
    }

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    if (isset($_GET['id'])) {
      $r = my_query("delete from " . $db_prefix . "users where id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

      users_show();
    }

    return (0);
  }

  function users_suspend()
  {
    global $db_prefix;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

  ?>
    <div align="center">
      <h1>Suspend User:</h1>
      <form action="flvby.php?go=suspend2&id=<?php echo $_GET['id']; ?>" method="POST">
        Enter the amount of days:
        <input type="text" name="duration" size="30"></input><br><br>
        <input type="submit" value="  Suspend  ">
      </form>
    </div>
  <?php

    return (0);
  }

  function users_suspend2()
  {
    global $db_prefix;

    while (list($key, $value) = each($_POST)) {
      if ("" == $value) {
        error_report(11);
        users_suspend();
        return (11);
      }
    }

    if (!is_numeric($_POST['duration'])) {
      error_report(8);
      users_suspend();
      return (8);
    }

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $r = my_query("select * from " . $db_prefix . "suspended where user_id='" . $_GET['id'] . "' and until > '" . time() . "'");
    if (mysqli_num_rows($r)) {
      $r = my_query("update " . $db_prefix . "suspended
    set until='" . (time() + $_POST['duration'] * 24 * 3600) . "' where user_id='" . $_GET['id'] . "'");
    } else {
      $r = my_query("insert into " . $db_prefix . "suspended(started, user_id, until) values(
    '" . time() . "','" . $_GET['id'] . "', '" . (time() + $_POST['duration'] * 24 * 3600) . "')");
    }

    suspended_show();

    return (0);
  }

  function users_unsuspend()
  {
    global $db_prefix;
    global $link;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    //  $r = my_query("delete from " . $db_prefix . "suspended where user_id='" . mysqli_real_escape_string( $link, $_GET['id']) . "'");
    $r = my_query("update " . $db_prefix . "suspended set until='" . time() . "' where user_id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

    suspended_show();

    return (0);
  }

  function users_deposit()
  {
    global $db_prefix;

    $r = my_query("select sum(balance) from " . $db_prefix . "users");
    list($deposits) = mysqli_fetch_row($r);
  ?>
    <div align="center">
      <h1>User Deposits</h1>
      Total User Deposits: $<?php echo currency_display($deposits); ?><p>
      <div class="help">-This page displays (in total) how much money users have deposited into their accounts in order to purchase tickets.</div>
    </div>
  <?php

    return (0);
  }

  function users_details()
  {
    global $db_prefix;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $user_id = $_GET['id'];

    $r = my_query("select ip,date,username, name, email, city, referrer_id, paypal, last_visit, balance, country,zip,gender,birthdate
  from " . $db_prefix . "users where id='$user_id'");

    list($ip, $date, $username, $name, $email, $city, $referrer_id, $paypal, $last_visit, $balance, $country, $zip, $gender, $birthdate) = mysqli_fetch_row($r);

    if ($referrer_id) {
      $r1 = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
      list($referrer_username) = mysqli_fetch_row($r1);
    } else {
      $referrer_username = "None";
    }

  ?>
    <div align="center">
      <h1>User Details</h1>
      <table width="100%">
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Username:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo $username; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Name:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo $name; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>E-Mail:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo $email; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>PayPal:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo $paypal; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>City:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo $city; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Country:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo $country; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>ZIP Code:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo $zip; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Gender:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php
            if ("f" == $gender) {
              echo "female";
            } else {
              echo "male";
            }
            ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Birth Date:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo date("Y-m-d", $birthdate); ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Referrer:</b>
          </td>
          <td width="50%" align="left" valign="top">

            <?php echo $referrer_username; ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Referrals:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php
            $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$user_id'");
            list($qty) = mysqli_fetch_row($r2);
            echo $qty;
            ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Won ($):</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php
            $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$user_id'");
            list($won) = mysqli_fetch_row($r3);
            if ("" == $won) {
              $won = 0;
            }
            echo currency_display($won);
            ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Balance:</b>
          </td>
          <td>
            <?php echo currency_display($balance);  ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Made off Wining Lotteries ($):</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php
            $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$user_id' and mestype='w'");
            list($won) = mysqli_fetch_row($r3);
            if ("" == $won) {
              $won = 0;
            }
            echo currency_display($won);
            ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Made off Referrals ($):</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php
            $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$user_id' and mestype='r'");
            list($won) = mysqli_fetch_row($r3);
            if ("" == $won) {
              $won = 0;
            }
            echo currency_display($won);
            ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>Registered:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo date("Y-m-d H:i:s", $date); ?>
          </td>
        </tr>
        <tr>
          <td width="50%" align="right" valign="top">
            <b>IP:</b>
          </td>
          <td width="50%" align="left" valign="top">
            <?php echo long2ip($ip); ?>
          </td>
        </tr>
      </table>

      <?php
      $r = my_query("select started,until from " . $db_prefix . "suspended where user_id='$user_id'");
      if (mysqli_num_rows($r)) {
      ?>
        <h2>Suspension Periods:</h2>
        <table width="400" border="1" cellspacing="0" cellpaddig="2">
          <tr>
            <th width="200" bgcolor="#dddddd">Started</th>
            <th width="200" bgcolor="#dddddd">Ended</th>
          </tr>
          <?php
          while (list($started, $until) = mysqli_fetch_row($r)) {
          ?>
            <tr>
              <td><?php echo date("Y-m-d H:i:s", $started) ?></td>
              <td><?php echo date("Y-m-d H:i:s", $until) ?></td>
            </tr>
          <?php
          }
          ?>
        </table>
      <?php
      }
      ?>
      <br>
      <input type="button" value="  Delete   " onclick="location.href='flvby.php?go=deluser&id=<?php echo $user_id; ?>'">&nbsp;
      <input type="button" value="  Suspend  " onclick="location.href='flvby.php?go=suspend&id=<?php echo $user_id; ?>'"><br><br>
      <input type="button" value="    Go Back    " onclick="location.href='flvby.php?go=users'">
    </div>
  <?php

    return (0);
  }

  function suspended_show()
  {
    global $db_prefix;
    global $page_limit;

    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }

    $r = my_query("select count(*)
  from " . $db_prefix . "suspended s, " . $db_prefix . "users u
  where u.id=s.user_id and (s.until > '" . time() . "' or s.until = '0') order by u.username");
    list($pages_qty) = mysqli_fetch_row($r);

    if ($page == ceil($pages_qty / $page_limit)) {
      $limit = $pages_qty % $page_limit;
    }

    if ((!isset($limit)) || (!$limit)) {
      $limit = $page_limit;
    }

    $r = my_query("select u.id, u.username, s.until
  from " . $db_prefix . "suspended s, " . $db_prefix . "users u
  where u.id=s.user_id and (s.until > '" . time() . "' or s.until = '0') order by u.username
  limit " . (($page - 1) * $page_limit) . ", $limit");
  ?>
    <div align="center">
      <h1>Suspended Users</h1>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="30%" bgcolor="#dddddd">Username</th>
          <th width="30%" bgcolor="#dddddd">Suspended Until</th>
          <th width="40%" bgcolor="#dddddd" colspan="2">Options</th>
        </tr>
        <?php
        while (list($id, $username, $until) = mysqli_fetch_row($r)) {
        ?>
          <tr>
            <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
            <td align="center">
              <?php
              if (!$until) echo "Forever";
              else echo date("Y-m-d H:i:s", $until);

              ?></td>
            <td align="center"><a href="flvby.php?go=suspend&id=<?php echo $id; ?>">Extend</a></td>
            <td align="center"><a href="flvby.php?go=unsuspend&id=<?php echo $id; ?>">Unsuspend</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <br>
      <hr>
      <?php
      for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
        <a href="flvby.php?go=suspended&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
      }
      ?><p>
      <div class="help">-This page displays the list of suspended users. The Admin can extend a suspension or terminate it.</div>
    </div>
  <?php

    return (0);
  }

  function fraud_checker()
  {
    global $db_prefix;
  ?>
    <div align="center">
      <h1>IP Checker</h1>
      <?php

      $fraud_flag = false;

      $r1 = my_query("select distinct ip from " . $db_prefix . "users");
      while (list($ipaddr) = mysqli_fetch_row($r1)) {
        $query = "select id,username,name,email from " . $db_prefix . "users where ip='$ipaddr'";
        $r = my_query($query);
        if (mysqli_num_rows($r) < 2) {
          continue;
        }
      ?>
        <h2><?php echo long2ip($ipaddr); ?></h2>
        <table width="100%" cellpading="2" cellspacing="0" border="1">
          <tr>
            <th width="10%" bgcolor="#dddddd">ID</th>
            <th width="30%" bgcolor="#dddddd">Username</th>
            <th width="30%" bgcolor="#dddddd">Name</th>
            <th width="30%" bgcolor="#dddddd">E-Mail</th>
          </tr>
          <?php
          while (list($id, $username, $name, $email) = mysqli_fetch_row($r)) {
          ?>
            <tr>
              <td><?php echo $id; ?></td>
              <td><?php echo $username; ?></td>
              <td><?php echo $name; ?></td>
              <td><?php echo $email; ?></td>
            </tr>
          <?php
          }
          ?>
        </table>
      <?php
        $fraud_flag = true;
      }
      if (!$fraud_flag) echo "There are no users with the same IP!"
      ?>
    </div>
  <?php


    return (0);
  }

  function archive_show()
  {
    global $db_prefix;
    global $page_limit;

    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }

    $r = my_query("select count(*) from " . $db_prefix . "archive order by started desc");
    list($pages_qty) = mysqli_fetch_row($r);

    if ($page == ceil($pages_qty / $page_limit)) {
      $limit = $pages_qty % $page_limit;
    }

    if ((!isset($limit)) || (!$limit)) {
      $limit = $page_limit;
    }

    $r = my_query("select id, lot_id, amount, started, duration from " . $db_prefix . "archive order by started desc limit " . (($page - 1) * $page_limit) . ", $limit");
  ?>
    <div align="center">
      <h1>Archive</h1>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="20%" bgcolor="#dddddd">ID</th>
          <th width="20%" bgcolor="#dddddd">Started</th>
          <th width="20%" bgcolor="#dddddd">Duration (days)</th>
          <th width="20%" bgcolor="#dddddd">Available to be Won($)</th>
          <th width="20%" bgcolor="#dddddd">Winners</th>
        </tr>
        <?php
        while (list($id, $lot_id, $amount, $started, $duration) = mysqli_fetch_row($r)) {
        ?>
          <tr>
            <td align="center"><?php echo $lot_id; ?></td>
            <td align="center"><?php echo date("m-d-Y", $started); ?></td>
            <td><?php echo $duration; ?></td>
            <td><?php echo currency_display($amount); ?></td>
            <td align="center"><a href="flvby.php?go=winners&id=<?php echo $lot_id; ?>">Show</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <br>
      <hr>
      <?php
      for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
        <a href="flvby.php?go=archive&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
      }
      ?><p>
      <div class="help">-This page displays all archived raffles.</div>
    </div>
  <?php

    return (0);
  }

  function finished_show()
  {
    global $db_prefix;
    global $page_limit;

    if (isset($_GET['page'])) {
      $page = $_GET['page'];
    } else {
      $page = 1;
    }

    $r = my_query("select count(*) from " . $db_prefix . "lotteries l where l.ended>'0' order by l.started desc");
    list($pages_qty) = mysqli_fetch_row($r);

    if ($page == ceil($pages_qty / $page_limit)) {
      $limit = $pages_qty % $page_limit;
    }

    if ((!isset($limit)) || (!$limit)) {
      $limit = $page_limit;
    }

    $r = my_query("select l.id, l.started, l.duration, l.ticket_price, l.win_percentage, l.available
  from " . $db_prefix . "lotteries l where l.ended>'0' order by l.started desc limit " . (($page - 1) * $page_limit) . ", $limit");
  ?>
    <div align="center">
      <h1>Finished Raffles </h1>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="6%" bgcolor="#dddddd">ID</th>
          <th width="12%" bgcolor="#dddddd">Started</th>
          <th width="10%" bgcolor="#dddddd">Duration (days)</th>
          <th width="12%" bgcolor="#dddddd">Ticket Price</th>
          <th width="10%" bgcolor="#dddddd">Percentage of Winners</th>
          <th width="10%" bgcolor="#dddddd">Tickets Purchased </th>
          <th width="8%" bgcolor="#dddddd">Total Amount</th>
          <th width="8%" bgcolor="#dddddd">Available to be Won (%)</th>
          <th width="8%" bgcolor="#dddddd">Available to be Won ($)</th>
          <th width="8%" bgcolor="#dddddd">Winners</th>
          <th width="8%" bgcolor="#dddddd">Options</th>
        </tr>
        <?php
        while (list($id, $started, $duration, $ticket_price, $win_percentage, $available) = mysqli_fetch_row($r)) {
          $r1 = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
          $tickets_qty = mysqli_num_rows($r1)
        ?>
          <tr>
            <td align="center"><?php echo $id; ?></td>
            <td align="center"><?php echo date("m-d-Y", $started); ?></td>
            <td><?php echo $duration; ?></td>
            <td><?php echo $ticket_price; ?></td>
            <td><?php echo $win_percentage; ?></td>
            <td><?php echo $tickets_qty; ?></td>
            <td><?php echo ($ticket_price * $tickets_qty); ?></td>
            <td><?php echo $available; ?></td>
            <td><?php echo (floor($ticket_price * $tickets_qty * $available) / 100); ?></td>
            <td align="center"><a href="flvby.php?go=winners&id=<?php echo $id; ?>">Show</a></td>
            <td align="center"><a href="flvby.php?go=arclottery&id=<?php echo $id; ?>">Send to Archive</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <br>
      <hr>
      <?php
      for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
      ?>
        <a href="flvby.php?go=finished&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
      <?php
      }
      ?><p>
      <div class="help">-This page displays the raffles that have finished. <br />
        -The Admin can view the winners of a raffle. <br />-A raffle can also be archived. Archived raffles are not displayed on the main page.</div>

    </div>
  <?php

    return (0);
  }


  function lotteries_show()
  {
    global $db_prefix;

    $r = my_query("select l.id, l.started, l.duration, l.ticket_price, l.win_percentage, l.available
  from " . $db_prefix . "lotteries l where l.ended = '0' order by l.started desc");
  ?>
    <div align="center">
      <h1>Open Raffles </h1>
      <table width="100%" border="1" cellspacing="0" cellpadding="2">
        <tr>
          <th width="6%" bgcolor="#dddddd">ID</th>
          <th width="10%" bgcolor="#dddddd">Started</th>
          <th width="6%" bgcolor="#dddddd">Duration (days)</th>
          <th width="10%" bgcolor="#dddddd">End Date</th>
          <th width="6%" bgcolor="#dddddd">Ticket Price</th>
          <th width="8%" bgcolor="#dddddd">Percentage of Winners</th>
          <th width="6%" bgcolor="#dddddd">Tickets Purchased </th>
          <th width="8%" bgcolor="#dddddd">Total Amount</th>
          <th width="8%" bgcolor="#dddddd">Available to be Won (%)</th>
          <th width="8%" bgcolor="#dddddd">Available to be Won ($)</th>
          <th width="8%" bgcolor="#dddddd">Options</th>
          <th width="16%" bgcolor="#dddddd" colspan="2">Finish Raffle </th>
        </tr>
        <?php
        while (list($id, $started, $duration, $ticket_price, $win_percentage, $available) = mysqli_fetch_row($r)) {
          $r1 = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
          $tickets_qty = mysqli_num_rows($r1)
        ?>
          <tr>
            <td align="center"><?php echo $id; ?></td>
            <td align="center"><?php echo date("m-d-Y", $started); ?></td>
            <td><?php echo $duration; ?></td>
            <td align="center"><?php echo date("m-d-Y", $started + $duration * 3600 * 24); ?></td>
            <td><?php echo $ticket_price; ?></td>
            <td><?php echo $win_percentage; ?></td>
            <td><?php echo $tickets_qty; ?></td>
            <td><?php echo ($ticket_price * $tickets_qty); ?></td>
            <td><?php echo $available; ?></td>
            <td><?php echo (floor($ticket_price * $tickets_qty * $available) / 100); ?></td>
            <td align="center"><a href="flvby.php?go=dellottery&id=<?php echo $id; ?>">Delete</a></td>
            <td align="center"><a href="flvby.php?go=randlottery&id=<?php echo $id; ?>">Randomly</a></td>
            <td align="center"><a href="flvby.php?go=manlottery&id=<?php echo $id; ?>">Manually</a></td>
          </tr>
        <?php
        }
        ?>
      </table>
      <h1>Add New Raffle </h1>
      <form action="flvby.php?go=addlottery" method="post">
        <table width="100%" style="border:hidden">
          <tr style="border:hidden">
            <td width="10%" style="border:hidden">
            </td>
            <td width="30%" valign="top" align="right" style="border:hidden">
              Duration (days):<br>
            </td>
            <td>
              <input type="text" name="duration" size="30">
              How long the raffle will last.
            </td>
          </tr>
          <tr>
            <td width="10%" style="border:hidden">
            </td>
            <td width="30%" valign="top" align="right" style="border:hidden">
              Ticket Price:<br>
            </td>
            <td>
              <input type="text" name="ticket_price" size="30">
              Price of each ticket.
            </td>
          </tr>
          <tr>
            <td width="10%" style="border:hidden">
            </td>
            <td width="30%" valign="top" align="right" style="border:hidden">
              Available to be Won(%):<br>
            </td>
            <td style="border:hidden">
              <input type="text" name="available" size="30">
              Percentage of money gathered from
              raffle that is available to be won.
            </td>
          </tr>
          <tr style="border:hidden">
            <td width="10%" style="border:hidden">
            </td>
            <td width="30%" valign="top" align="right" style="border:hidden">
              Percentage of Winners:<br>
            </td>
            <td>
              <input type="text" name="win_percentage" size="30">
              Percentage of entrants who win.
            </td>
          </tr>
          <tr>
            <td width="10%" style="border:hidden">
            </td>
            <td width="30%" valign="top" align="right" style="border:hidden">
              Description:<br>
            </td>
            <td>
              <textarea name="description" rows="10" cols="50"></textarea>
            </td>
          </tr>
        </table>
        <center><br><input type="submit" value="  Add  "></center>
      </form>
      <div class="help">-This page displays information about the current open raffles and it allows you to create a new raffle. <br />-Explanation of <em>Open Raffles</em> table: <br />
        <em>Percentage of Winners</em> - The percentage of raffle entrants who win. The money (<em>Available to be Won $</em>) will be split evenly among them. <br />
        <em>Total Amount</em> - Refers to the total amount of money the raffle has produced. <br />
        <em>Available to be Won (%)</em>- Percentage of the money <em>(Total Amount</em>) that is given to those who win. The admin will keep the rest. The column to the right shows the actual amount in dollars. <br />
        -Admin can delete a raffle. If Admin click &quot;Randomly,&quot; the raffle will be closed and winners will be chosen at random. If &quot;Manually,&quot; is clicked the Admin will chose the winners himself.
      </div>
    <?php

    return (0);
  }

  function lotteries_add()
  {
    global $db_prefix;
    global $link;

    while (list($key, $value) = each($_POST)) {
      if ("" == $value) {
        error_report(11);
        lotteries_show();
        return (11);
      }
    }

    $r = my_query("insert into " . $db_prefix . "lotteries(started, duration, ticket_price, win_percentage, description,available) values(
  '" . time() . "',
  '" . $_POST['duration'] . "',
  '" . $_POST['ticket_price'] . "',
  '" . $_POST['win_percentage'] . "',
  '" . mysqli_real_escape_string($link, $_POST['description']) . "',
  '" . $_POST['available'] . "'
  )");

    lotteries_show();

    return (0);
  }

  function lotteries_delete()
  {
    global $db_prefix;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $r = my_query("delete from " . $db_prefix . "lotteries where id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

    lotteries_show();

    return (0);
  }

  function lotteries_finish_randomly()
  {
    global $db_prefix;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    include_once("cron.php");

    finish_lottery($_GET['id']);

    $r = my_query("select ticket_price,available from " . $db_prefix . "lotteries where id='" . $_GET['id'] . "'");
    list($ticket_price, $available) = mysqli_fetch_row($r);

    $r = my_query("select count(*) from " . $db_prefix . "tickets where lottery_id='" . $_GET['id'] . "'");
    list($tickets) = mysqli_fetch_row($r);

    $profit = $ticket_price * $tickets - floor($ticket_price * $tickets * $available) / 100;

    $r = my_query("insert into " . $db_prefix . "profits(date,amount) values('" . time() . "','$profit')");

    winners_show();

    return (0);
  }


  function lotteries_finish_manually()
  {
    global $db_prefix;
    global $link;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $id = $_GET['id'];

    $r = my_query("select win_percentage, ticket_price from " . $db_prefix . "lotteries where id='$id'");
    list($win_percentage, $ticket_price) = mysqli_fetch_row($r);

    $r = my_query("select t.id, u.id, u.username from " . $db_prefix . "tickets t, " . $db_prefix . "users u
                      where t.lottery_id='$id' and u.id=t.user_id");
    $players_qty = mysqli_num_rows($r);

    $winners_qty = floor($players_qty * $win_percentage / 100);

    if (!$winners_qty) {
      if ($players_qty) $winners_qty = 1;
      else {
        $r = my_query("update " . $db_prefix . "lotteries set ended='" . time() . "' where id='$id'");
        return (1);
      }
    }

    $winner_prize = floor(100 * $ticket_price * $players_qty / $winners_qty) / 100;

    ?>
      <div align="center">
        <h1>Choose Winners<br />
          (<?php echo $winners_qty ?> winners in this raffle):</h1>
        <form action="flvby.php?go=manlottery2&id=<?php echo $id; ?>" method="POST">
          <table width="100%">
            <?php

            while (list($ticket_id, $user_id, $username) = mysqli_fetch_row($r)) {
            ?>
              <tr>
                <td width="10%">
                </td>
                <td width="40%" valign="top" align="right">
                  <input type="checkbox" name="t_<?php echo $ticket_id ?>">
                </td>
                <td width="50%">
                  <?php echo $username; ?>
                </td>
              </tr>
            <?php
            }
            ?>
          </table>
          <br>
          <input type="submit" value="  Finish  ">
        </form>
      </div>
    <?php

    return (0);
  }

  function lotteries_finish_manually2()
  {
    global $db_prefix;
    global $link;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $id = $_GET['id'];

    $r = my_query("select win_percentage, ticket_price from " . $db_prefix . "lotteries where id='$id'");
    list($win_percentage, $ticket_price) = mysqli_fetch_row($r);

    $r = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
    $players_qty = mysqli_num_rows($r);

    $winners_qty = floor($players_qty * $win_percentage / 100);

    if (!$winners_qty) {
      if ($players_qty) $winners_qty = 1;
      else {
        $r = my_query("update " . $db_prefix . "lotteries set ended='" . time() . "' where id='$id'");
        return (1);
      }
    }

    $winner_prize = floor(100 * $ticket_price * $players_qty / $winners_qty) / 100;


    $players = array();

    while (list($key, $value) = each($_POST)) {
      $arr = explode("_", $key);
      array_push($players, $arr[1]);
    }

    if (sizeof($players) != $winners_qty) {
      error_report(15);
      lotteries_finish_manually();
      return (15);
    }

    include_once("cron.php");

    for ($i = 0; $i < $winners_qty; $i++) {
      $ticket_id = array_pop($players);
      winner($ticket_id, $winner_prize);
    }

    $r = my_query("update " . $db_prefix . "lotteries set ended='" . time() . "' where id='$id'");

    $r = my_query("select ticket_price,available from " . $db_prefix . "lotteries where id='" . $_GET['id'] . "'");
    list($ticket_price, $available) = mysqli_fetch_row($r);

    $r = my_query("select count(*) from " . $db_prefix . "tickets where lottery_id='" . $_GET['id'] . "'");
    list($tickets) = mysqli_fetch_row($r);

    $profit = $ticket_price * $tickets - floor($ticket_price * $tickets * $available) / 100;

    $r = my_query("insert into " . $db_prefix . "profits(date,amount) values('" . time() . "','$profit')");

    winners_show();

    return (0);
  }


  function lotteries_send_to_arc()
  {
    global $db_prefix;
    global $link;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $id = $_GET['id'];

    $r = my_query("select started, duration, ticket_price,available from " . $db_prefix . "lotteries where id='$id'");
    list($started, $duration, $ticket_price, $available) = mysqli_fetch_row($r);

    $r = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
    list($tickets_qty) = mysqli_fetch_row($r);

    $amount = $ticket_price * $tickets_qty * $available / 100;

    $r = my_query("insert into " . $db_prefix . "archive(lot_id,amount,started,duration)
                 values('$id', '$amount', '$started', '$duration')");

    $r = my_query("delete from " . $db_prefix . "lotteries where id='" . mysqli_real_escape_string($link, $id) . "'");

    finished_show();
  }

  function winners_show()
  {
    global $db_prefix;

    if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
      error_report(9);
      lotteries_show();
      return (9);
    }

    $id = $_GET['id'];

    $r = my_query("select u.id, u.username, u.name, u.email, u.city, u.referrer_id, u.paypal
  from " . $db_prefix . "users u, " . $db_prefix . "tickets t where t.lottery_id='$id' and t.user_id=u.id and t.won>'0'");
    ?>
      <div align="center">
        <h1>Raffle Winners</h1>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="20%" bgcolor="#dddddd">Nickname</th>
            <th width="20%" bgcolor="#dddddd">Name</th>
            <th width="15%" bgcolor="#dddddd">E-Mail</th>
            <th width="15%" bgcolor="#dddddd">City</th>
            <th width="15%" bgcolor="#dddddd">Referrer</th>
            <th width="15%" bgcolor="#dddddd">PayPal</th>
          </tr>
          <?php
          while (list($id, $username, $name, $email, $city, $referrer_id, $paypal) = mysqli_fetch_row($r)) {
            if ($referrer_id) {
              $r1 = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
              list($referrer_username) = mysqli_fetch_row($r1);
            } else {
              $referrer_username = "None";
            }
          ?>
            <tr>
              <td><?php echo $username; ?></td>
              <td><?php echo $name; ?></td>
              <td><?php echo $email; ?></td>
              <td><?php echo $city; ?></td>
              <td><?php echo $referrer_username; ?></td>
              <td><?php echo $paypal; ?></td>
            </tr>
          <?php
          }
          ?>
        </table>
      </div>
      <?php

      return (0);
    }

    function paypal_parser()
    {
      global $db_prefix;
      global $link;

      if (isset($_POST['parser_list'])) {
        $strings = explode("\n", $_POST['parser_list']);

        $success = array();
        $failed = array();
        $credited = array();

        for ($i = 0; $i < sizeof($strings); $i++) {

          if ("" == $strings[$i]) {
            continue;
          }

          $strings[$i] = eregi_replace('\"', "", $strings[$i]);
          $strings[$i] = eregi_replace("\'", "", $strings[$i]);
          $strings[$i] = str_replace("\\", "", $strings[$i]);

          $string = explode(",", $strings[$i]);
          // if (sizeof($string) != 16) continue;

          //      $date = strtotime($string[0]);
          $amount = trim($string[8]);
          $name = trim($string[10]);
          $note = trim($string[9]);

          if (false === strpos($note, "@")) {
            $email = trim($string[10]);
          } else {
            $email = $note;
          }

          $r = my_query("select id from " . $db_prefix . "users where paypal='$email'");
          if (!mysqli_num_rows($r)) {
            array_push($failed, "0,$name,$email,Invalid paypal email");
            continue;
          }
          list($user_id) = mysqli_fetch_row($r);

          /*      if ((!is_numeric($lottery_id)) || ($lottery_id > 999999999) || (check_12hours_limit($lottery_id,$date))) {
        $r = my_query("insert into " . $db_prefix . "frauds(user_id,reason) values('$user_id','au')");
        array_push($failed, "$user_id,$name,$email,Invalid auction ID");
        continue;
      }
*/
          /*
      $r = my_query("select ticket_price from " . $db_prefix . "lotteries where id='$lottery_id' and ended='0'");
      if (!mysqli_num_rows($r)) {
        $r = my_query("insert into " . $db_prefix . "frauds(user_id, reason) values('$user_id', 'au')");
        array_push($failed, "$user_id,$name,$email,Invalid auction ID");
        continue;
      }
*/
          //      list($ticket_price) = mysqli_fetch_row($r);

          /*      if ($ticket_price != $amount) {
        if ($ticket_price > $amount) {
//          $r = my_query("delete from " . $db_prefix . "tickets where user_id='$user_id' and lottery_id='$lottery_id'");
          $r = my_query("insert into " . $db_prefix . "frauds(user_id, reason, correct, incorrect) values('$user_id', 'am', '$ticket_price', '$amount')");
          array_push($failed, "$user_id,$name,$email,Incorrect amount \$" . $amount . "(correct - \$" . $ticket_price . ")");
          continue;
        }
        else {
          $r = my_query("insert into " . $db_prefix . "frauds(user_id, reason, correct, incorrect) values('$user_id', 'am', '$ticket_price', '$amount')");
        }
      }
*/
          /*      if (check_ticket_exist($user_id, $lottery_id)) {
          array_push($failed, "$user_id,$name,$email,Ticket already bought");
          continue;
      }
*/
          //      $r = my_query("insert into " . $db_prefix ."tickets(user_id, lottery_id, price) values('$user_id','$lottery_id','$ticket_price')");

          $r = my_query("update " . $db_prefix . "users set balance=balance+'" . mysqli_real_escape_string($link, $amount) . "' where id='$user_id'");

          array_push($success, $user_id);
          array_push($credited, $amount);
        }

        if (sizeof($success)) {
      ?>
          <div align="center">
            <h2>Successfully Credited:</h2>
            <table width="100%" border="1" cellspacing="0" cellpadding="2">
              <th width="8%" bgcolor="#dddddd">Username</th>
              <th width="8%" bgcolor="#dddddd">Name</th>
              <th width="8%" bgcolor="#dddddd">E-Mail</th>
              <th width="6%" bgcolor="#dddddd">City</th>
              <th width="8%" bgcolor="#dddddd">Referrer</th>
              <th width="8%" bgcolor="#dddddd">PayPal</th>
              <th width="6%" bgcolor="#dddddd">Active</th>
              <th width="8%" bgcolor="#dddddd">Referrals</th>
              <th width="8%" bgcolor="#dddddd">Won ($)</th>
              <th width="8%" bgcolor="#dddddd">Balance</th>
              <th width="8%" bgcolor="#dddddd">Credited</th>
              <th width="16%" bgcolor="#dddddd" colspan="2">Options</th>
              <?php
              for ($i = 0; $i < sizeof($success); $i++) {
                $r = my_query("select username, name, email, city, referrer_id, paypal, last_visit, balance
        from " . $db_prefix . "users where id='" . $success[$i] . "'");
                $id = $success[$i];
                list($username, $name, $email, $city, $referrer_id, $paypal, $last_visit, $balance) = mysqli_fetch_row($r);
                if (!$referrer_id) {
                  $referrer_username = "None";
                } else {
                  $r = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
                  list($referrer_username) = mysqli_fetch_row($r);
                }
              ?>
                <tr>
                  <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
                  <td><?php echo $name; ?></td>
                  <td><?php echo $email; ?></td>
                  <td><?php echo $city; ?></td>
                  <td><?php echo $referrer_username; ?></td>
                  <td><?php echo $paypal; ?></td>
                  <td align="center">
                    <?php if ((time() - $last_visit) > 5 * 60) { ?>
                      <font color="#cc3232"><b>NO</b></font>
                    <?php
                    } else {
                    ?>
                      <font color="#22cc32"><b>YES</b></font>
                    <?php } ?>
                  </td>
                  <td>
                    <?php
                    $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$id'");
                    list($qty) = mysqli_fetch_row($r2);
                    echo $qty;
                    ?>
                  </td>
                  <td>
                    <?php
                    $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$id'");
                    list($won) = mysqli_fetch_row($r3);
                    if ("" == $won) {
                      $won = 0;
                    }
                    echo currency_display($won);
                    ?>
                  </td>
                  <td><?php echo currency_display($balance); ?></td>
                  <td><?php echo currency_display($credited[$i]); ?></td>
                  <td align="center"><a href="flvby.php?go=suspend&id=<?php echo $id; ?>">Suspend</a></td>
                  <td align="center"><a href="flvby.php?go=deluser&id=<?php echo $id; ?>">Delete</a></td>
                </tr>

              <?php
              }
              ?>
            </table>
          </div>
        <?php
        }
        if (sizeof($failed)) {
        ?>
          <div align="center">
            <font color="#ff0000">
              <h2>Failed:</h2>
            </font>
            <table width="100%" border="1" cellspacing="0" cellpadding="2">
              <th width="8%" bgcolor="#dddddd">Username</th>
              <th width="8%" bgcolor="#dddddd">Name</th>
              <th width="8%" bgcolor="#dddddd">E-Mail</th>
              <th width="8%" bgcolor="#dddddd">City</th>
              <th width="8%" bgcolor="#dddddd">Referrer</th>
              <th width="8%" bgcolor="#dddddd">PayPal</th>
              <th width="8%" bgcolor="#dddddd">Active</th>
              <th width="8%" bgcolor="#dddddd">Referrals</th>
              <th width="8%" bgcolor="#dddddd">Won ($)</th>
              <th width="14%" bgcolor="#dddddd">Reason</th>
              <th width="14%" bgcolor="#dddddd" colspan="2">Options</th>
              <?php
              for ($i = 0; $i < sizeof($failed); $i++) {
                $arr = explode(",", $failed[$i]);
                $id = $arr[0];
                $name = $arr[1];
                $paypal = $arr[2];
                $reason = $arr[3];

                if (!$id) {
                  $username = "Unknown";
                  $email = "Unknown";
                  $city = "Unknown";
                  $referrer_id = 0;
                  $last_visit = 0;
                } else {
                  $r = my_query("select username, name, email, city, referrer_id, paypal, last_visit
          from " . $db_prefix . "users where id='" . $id . "'");
                  list($username, $name, $email, $city, $referrer_id, $paypal, $last_visit) = mysqli_fetch_row($r);
                }
                if (!$referrer_id) {
                  $referrer_username = "None";
                } else {
                  $r = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
                  list($referrer_username) = mysqli_fetch_row($r);
                }

              ?>
                <tr>
                  <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
                  <td><?php echo $name; ?></td>
                  <td><?php echo $email; ?></td>
                  <td><?php echo $city; ?></td>
                  <td><?php echo $referrer_username; ?></td>
                  <td><?php echo $paypal; ?></td>
                  <td align="center">
                    <?php if ((time() - $last_visit) > 5 * 60) { ?>
                      <font color="#cc3232"><b>NO</b></font>
                    <?php
                    } else {
                    ?>
                      <font color="#22cc32"><b>YES</b></font>
                    <?php } ?>
                  </td>
                  <td>
                    <?php
                    $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$user_id'");
                    list($qty) = mysqli_fetch_row($r2);
                    echo $qty;
                    ?>
                  </td>
                  <td>
                    <?php
                    $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$user_id'");
                    list($won) = mysqli_fetch_row($r3);
                    if ("" == $won) {
                      $won = 0;
                    }
                    echo currency_display($won);
                    ?>
                  </td>
                  <td><?php echo $reason; ?></td>
                  <?php
                  if ($id) {
                  ?>
                    <td align="center"><a href="flvby.php?go=suspend&id=<?php echo $id; ?>">Suspend</a></td>
                    <td align="center"><a href="flvby.php?go=deluser&id=<?php echo $id; ?>">Delete</a></td>
                  <?php
                  } else {
                  ?>
                    <td align="center">Suspend</td>
                    <td align="center">Delete</td>
                  <?php
                  }
                  ?>
                </tr>
              <?php

              }
              ?>
            </table>
          </div>
      <?php
        }
      }

      ?>
      <div align="center">
        <h1>PayPal Parser</h1>
        <form action="flvby.php?go=parser" method="post">
          <textarea name="parser_list" rows="20" cols="80"></textarea><br><br>
          <input type="submit" value="  Submit  ">
        </form>
        <p>
        <div class="help">-This page allows the Admin to credit site users' accounts with the amount they deposited into the Admin's PayPal (they will use this money to purchase raffle tickets). <br />
          -I<span name="intelliTxt">n order to download PayPal log (which you will paste into the box above):<br />
            1. Log into PayPal (Business account required)<br />
            2. Go to History<br />
            3. Under reporting tools, click &ldquo;Download My History&rdquo;<br />
            4. Click &ldquo;Customize Download Field&rdquo;<br />
            5. Make sure to ONLY check &ldquo;Item ID&rdquo; under PayPal Website Payments and &ldquo;Note&rdquo; under Other Fields<br />
            6. Click save<br />
            7. Then you&rsquo;ll be taken back to the Download My History page<br />
            8. Check &ldquo;Last Download to Present&rdquo; <br />
            9. In &ldquo;File Types for Download&rdquo; select &ldquo;Comma Delimited-Completed Payments&rdquo;<br />
            10. Then click &ldquo;Download History&rdquo;<br />
            11. </span>Open the .CSV file in WordPad and copy paste the text into the box above. Make sure <u>not</u> to include the top line that describes the categories. <br />
          If for some reason you cannot use the PayPal parser, you can credit users by clicking &quot;<a href="http://localhost/r/flvby.php?go=credits">Credit Accounts</a>&quot;</div>
      </div>
    <?php
      return (0);
    }

    function tickets_show()
    {
      global $db_prefix;

    ?>
      <div align="center">
        <h1>Purchased Tickets:</h1>
        <?php
        $r = my_query("select t.id, u.username, l.id, l.started, l.duration, l.ticket_price
                   from " . $db_prefix . "users u, " . $db_prefix . "lotteries l, " . $db_prefix . "tickets t
                   where t.user_id=u.id and t.lottery_id=l.id and l.ended='0' order by u.username");
        ?>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="20%" bgcolor="#dddddd">Name</th>
            <th width="10%" bgcolor="#dddddd">Raffle ID</th>
            <th width="20%" bgcolor="#dddddd">Started</th>
            <th width="15%" bgcolor="#dddddd">Duration</th>
            <th width="20%" bgcolor="#dddddd">Total amount</th>
            <th width="15%" bgcolor="#dddddd">Options</th>
          </tr>
          <?php
          while (list($ticket_id, $username, $id, $started, $duration, $ticket_price) = mysqli_fetch_row($r)) {
            $r1 = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
            $tickets_qty = mysqli_num_rows($r1);
            $amount = $tickets_qty * $ticket_price;
          ?>
            <tr>
              <td><?php echo $username; ?></td>
              <td><?php echo $id; ?></td>
              <td><?php echo date("m-d-Y", $started); ?></td>
              <td><?php echo $duration; ?></td>
              <td><?php echo currency_display($amount); ?></td>
              <td align="center"><a href="flvby.php?go=delticket&id=<?php echo $ticket_id; ?>">Delete</a></td>
            </tr>
          <?php
          }
          ?>
        </table>
      </div>
    <?php

      return (0);
    }


    function tickets_add()
    {
      global $db_prefix;

      tickets_show();

      $r = my_query("select id,username from " . $db_prefix . "users");
      $r1 = my_query("select id from " . $db_prefix . "lotteries where ended='0'");
    ?>
      <div align="center">
        <h1>Add Ticket</h1>
        <form action="flvby.php?go=tickets2" method="post">
          Username:
          <select name="user_id">
            <?php while (list($id, $username) = mysqli_fetch_row($r)) { ?>
              <option value="<?php echo $id; ?>"><?php echo $username ?>
              <?php } ?>
          </select>
          Raffle ID:
          <select name="lottery_id">
            <?php while (list($id) = mysqli_fetch_row($r1)) { ?>
              <option value="<?php echo $id; ?>"><?php echo $id ?>
              <?php } ?>
          </select>
          <input type="submit" value="  Add  ">
        </form>
        <p>
        <div class="help">-This page displays the tickets that have been purchased for <em>open</em> raffles.<br />
          -The Admin can manually purchase a ticket for a user by selecting the raffle id and username.</div>
      </div>
    <?php

      return (0);
    }

    function tickets_add2()
    {
      global $db_prefix;

      $lottery_id = $_POST['lottery_id'];
      $user_id = $_POST['user_id'];

      $r = my_query("select id from " . $db_prefix . "tickets where user_id='$user_id' and lottery_id='$lottery_id'");
      if (mysqli_num_rows($r)) {
        error_report(16);
        tickets_add();
        return (16);
      }

      $r = my_query("select ticket_price from " . $db_prefix . "lotteries where id='$lottery_id'");
      list($ticket_price) = mysqli_fetch_row($r);

      $r = my_query("insert into " . $db_prefix . "tickets(user_id, lottery_id, price,date) values('$user_id', '$lottery_id', '$ticket_price','" . time() . "')");

      tickets_add();

      return (0);
    }

    function tickets_delete()
    {
      global $db_prefix;
      global $link;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("delete from " . $db_prefix . "tickets where id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

      tickets_show();

      return (0);
    }

    function fictitious_show()
    {
      global $db_prefix;
      global $page_limit;

      if (isset($_GET['page'])) {
        $page = $_GET['page'];
      } else {
        $page = 1;
      }

      $r = my_query("select count(*) from " . $db_prefix . "fictitious f, " . $db_prefix . "users u
  where u.id=f.user_id order by f.id desc");
      list($pages_qty) = mysqli_fetch_row($r);

      if ($page == ceil($pages_qty / $page_limit)) {
        $limit = $pages_qty % $page_limit;
      }

      if ((!isset($limit)) || (!$limit)) {
        $limit = $page_limit;
      }

      $r = my_query("select f.id, u.id, u.username, f.username, f.name, f.email, f.city, f.referrer_id, f.paypal
  from " . $db_prefix . "fictitious f, " . $db_prefix . "users u
  where u.id=f.user_id order by f.id desc limit " . (($page - 1) * $page_limit) . ", $limit");
    ?>
      <div align="center">
        <h1>Fraudulent Registrations</h1>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="15%" bgcolor="#dddddd">Real Name</th>
            <th width="15%" bgcolor="#dddddd">Username</th>
            <th width="15%" bgcolor="#dddddd">Name</th>
            <th width="15%" bgcolor="#dddddd">E-Mail</th>
            <th width="10%" bgcolor="#dddddd">City</th>
            <th width="10%" bgcolor="#dddddd">Referrer</th>
            <th width="10%" bgcolor="#dddddd">PayPal</th>
            <th width="10%" bgcolor="#dddddd">Options</th>
          </tr>
          <?php
          while (list($id, $user_id, $real_username, $username, $name, $email, $city, $referrer_id, $paypal) = mysqli_fetch_row($r)) {
            if ($referrer_id) {
              $r1 = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
              list($referrer_username) = mysqli_fetch_row($r1);
            } else {
              $referrer_username = "None";
            }
          ?>
            <tr>
              <td><?php echo $real_username; ?></td>
              <td><?php echo $username; ?></td>
              <td><?php echo $name; ?></td>
              <td><?php echo $email; ?></td>
              <td><?php echo $city; ?></td>
              <td><?php echo $referrer_username; ?></td>
              <td><?php echo $paypal; ?></td>
              <td align="center"><a href="flvby.php?go=delfict&id=<?php echo $user_id; ?>">Delete</a></td>
            </tr>
          <?php
          }
          ?>
        </table>
        <br>
        <hr>
        <?php
        for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
        ?>
          <a href="flvby.php?go=fictitious&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
        <?php
        }
        ?><p>
        <div class="help">-This page displays suspended users who have tried to register a new account. <br />
          -If a suspended user attempts to register an account, he is suspended permanently.</div>
      </div>
    <?php

      return (0);
    }

    function fictitious_delete()
    {
      global $db_prefix;
      global $link;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("delete from " . $db_prefix . "fictitious where user_id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

      fictitious_show();

      return (0);
    }

    function profits_show()
    {
      global $db_prefix;
      global $page_limit;

      if (isset($_GET['page'])) {
        $page = $_GET['page'];
      } else {
        $page = 1;
      }

      $r = my_query("select count(*) from " . $db_prefix . "profits  order by date desc");
      list($pages_qty) = mysqli_fetch_row($r);

      if ($page == ceil($pages_qty / $page_limit)) {
        $limit = $pages_qty % $page_limit;
      }

      if ((!isset($limit)) || (!$limit)) {
        $limit = $page_limit;
      }

    ?>
      <div align="right">
        <?php
        $r = my_query("select sum(amount) from " . $db_prefix . "profits where paid='n'");
        list($amount) = mysqli_fetch_row($r);
        ?>
        <b>Total Amount: $<?php echo currency_display($amount); ?></b>
      </div>

      <?php
      $r = my_query("select id,date,amount,paid from " . $db_prefix . "profits
    order by date desc limit " . (($page - 1) * $page_limit) . ", $limit");
      ?>
      <div align="center">
        <h1>Profits</h1>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="20%" bgcolor="#dddddd">Date</th>
            <th width="60%" bgcolor="#dddddd">Amount</th>
            <th width="20%" bgcolor="#dddddd">Options</th>
          </tr>
          <?php
          while (list($id, $date, $amount, $paid) = mysqli_fetch_row($r)) {
          ?>
            <?php
            if ('n' == $paid) {
            ?>
              <tr>
                <td align="center"><?php echo date("m-d-Y", $date); ?></td>
                <td><?php echo currency_display($amount); ?></td>
                <td align="center"><a href="flvby.php?go=accept&id=<?php echo $id; ?>">Withdrawn</a></td>
              </tr>
            <?php
            }
            ?>
            <?php
            if ('y' == $paid) {
            ?>
              <tr>
                <td align="center">
                  <font color="#777777"><?php echo date("m-d-Y", $date); ?></font>
                </td>
                <td>
                  <font color="#777777"><?php echo currency_display($amount); ?></font>
                </td>
                <td align="center">
                  <font color="#777777">Withdrawn</font>
                </td>
              </tr>
            <?php
            }
            ?>
          <?php
          }
          ?>
        </table>
        <br>
        <hr>
        <?php
        for ($i = 1; $i < ceil($pages_qty / $page_limit) + 1; $i++) {
        ?>
          <a href="flvby.php?go=profits&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
        <?php
        }
        ?><p>
        <div class="help">-This page displays the website's profits from raffles. <br />
          -The assumption is that the Admin has a PayPal account solely for this website. <br />
          -The Admin should withdraw the profits he/she has made. Then the Admin should click "Withdrawn."<br />-This page allows the Admin to know how much he/she should withdraw after each raffle, as profit.<br />-When <em>Total Amount</em> (located at the top) says 0.00 that means the Admin has withdrawn all the money (made as profit) from the website's PayPal.</div>
      </div>
    <?php

      return (0);
    }

    function profits_accept()
    {
      global $db_prefix;
      global $link;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("update " . $db_prefix . "profits set paid='y' where id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

      profits_show();

      return (0);
    }

    function questions_show()
    {
      global $db_prefix;

      if (isset($_GET['category'])) {
        $category = $_GET['category'];
      } else {
        $category = 1;
      }

    ?>
      <div align="right">
        <?php
        $r = my_query("select id,name from " . $db_prefix . "support_categories");
        list($categ_id, $categ_name) = mysqli_fetch_row($r);
        ?>
        <a href="flvby.php?go=support&category=<?php echo $categ_id; ?>"><?php echo $categ_name ?></a>
        <?php
        while (list($categ_id, $categ_name) = mysqli_fetch_row($r)) {
        ?>
          &nbsp;-&nbsp;<a href="flvby.php?go=support&category=<?php echo $categ_id; ?>"><?php echo $categ_name ?></a>
        <?php
        }
        ?>
      </div>
      <div align="center">
        <h1>Open Tickets</h1>
        <h2>
          <?php
          $r = my_query("select name from " . $db_prefix . "support_categories where id='" . mysqli_real_escape_string($link, $category) . "'");
          list($header) = mysqli_fetch_row($r);
          echo $header;
          ?>
        </h2>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="10%" bgcolor="#dddddd">Date</th>
            <th width="20%" bgcolor="#dddddd">Username</th>
            <th width="40%" bgcolor="#dddddd">Subject</th>
            <th width="30%" bgcolor="#dddddd" colspan="3">Options</th>
          </tr>
          <?php

          $r = my_query("select s.id, s.user_id, u.username, s.date, s.status, s.subject, s.category
  from " . $db_prefix . "support s, " . $db_prefix . "users u
  where s.status='o' and s.category='" . $category . "' and s.user_id=u.id order by date desc");
          if (mysqli_num_rows($r)) {
            while (list($id, $user_id, $username, $date, $status, $subject, $category) = mysqli_fetch_row($r)) {
          ?>
              <tr>
                <td align="center"><?php echo date("m-d-Y", $date); ?></td>
                <td><?php echo $username; ?></td>
                <td><?php echo $subject; ?></td>
                <td align="center"><a href="flvby.php?go=view&id=<?php echo $id; ?>">View</a></td>
                <td align="center"><a href="flvby.php?go=delque&id=<?php echo $id; ?>">Delete</a></td>
                <td align="center"><a href="flvby.php?go=delallque&id=<?php echo $user_id; ?>">Delete All</a></td>
              </tr>
            <?php
            }
            ?>
          <?php
          }
          ?>
        </table>
        <br>
        <a href="flvby.php?go=editcateg">Edit Support Ticket Categories</a>
        <p>
        <div class="help">-This is the Support Ticket page. Users submit support tickets if they have a question about the website, its usage, etc. <br />
          -The links on the top of the page are categories. When a user submits a Support Ticket he chooses a category for it. The Admin can view tickets in a category by clicking the links above.<br />
          -The Admin can view and delete support tickets. <br />-The Admin can delete all Support Tickets made by a single user by clicking "Delete All."<br />-Support Ticket categories can be edited with relative ease.</div>
      </div>
    <?php

      return (0);
    }

    function questions_categories_show()
    {
      global $db_prefix;
    ?>

      <div align="center">
        <h1>Support Ticket Categories:</h1>
        <?php
        $r = my_query("select id,name from " . $db_prefix . "support_categories");
        ?>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="80%" bgcolor="#dddddd">Name</th>
            <th width="20%" bgcolor="#dddddd">Options</th>
          </tr>
          <?php
          while (list($id, $name) = mysqli_fetch_row($r)) {
          ?>
            <tr>
              <td><?php echo $name; ?></td>
              <td align="center"><a href="flvby.php?go=delcateg&id=<?php echo $id; ?>">Delete</a></td>
            </tr>
          <?php
          }
          ?>
        </table>
        <h1>Add New Category</h1>
        <form action="flvby.php?go=addcateg" method="post">
          <table width="100%">
            <tr>
              <td width="10%">
              </td>
              <td width="30%" valign="top" align="right">
                Category Name:<br>
              </td>
              <td>
                <input type="text" name="categ_name" size="30">
              </td>
            </tr>
          </table>
          <center><br><input type="submit" value="  Add  "></center>
        </form>
        <p>
        <div class="help">-The Admin can delete existing categories, as well as add new ones.</div>
      </div>
    <?php
      return (0);
    }

    function questions_view()
    {
      global $db_prefix;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("select status,subject,message from " . $db_prefix . "support
  where id='" . $_GET['id'] . "'");

      if (!(list($status, $subject, $message) = mysqli_fetch_row($r))) {
        return (1);
      }

      $r = my_query("select u.id, u.username, u.name, u.email, u.city, u.referrer_id, u.paypal, u.last_visit, u.balance
  from " . $db_prefix . "users u, " . $db_prefix . "support s
  where u.id = s.user_id and s.id='" . $_GET['id'] . "'");

    ?>
      <div align="center">
        <h1>User Details:</h1>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="10%" bgcolor="#dddddd">Username</th>
            <th width="8%" bgcolor="#dddddd">Name</th>
            <th width="8%" bgcolor="#dddddd">E-Mail</th>
            <th width="8%" bgcolor="#dddddd">City</th>
            <th width="8%" bgcolor="#dddddd">Referrer</th>
            <th width="8%" bgcolor="#dddddd">PayPal</th>
            <th width="8%" bgcolor="#dddddd">Referrals</th>
            <th width="8%" bgcolor="#dddddd">Won ($)</th>
            <th width="8%" bgcolor="#dddddd">Balance</th>
            <th width="6%" bgcolor="#dddddd">Active</th>
            <th width="20%" bgcolor="#dddddd" colspan="2">Options</th>
          </tr>
          <?php
          while (list($id, $username, $name, $email, $city, $referrer_id, $paypal, $last_visit, $balance) = mysqli_fetch_row($r)) {
            if ($referrer_id) {
              $r1 = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
              list($referrer_username) = mysqli_fetch_row($r1);
            } else {
              $referrer_username = "None";
            }
          ?>
            <tr>
              <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
              <td><?php echo $name; ?></td>
              <td><?php echo $email; ?></td>
              <td><?php echo $city; ?></td>
              <td><?php echo $referrer_username; ?></td>
              <td><?php echo $paypal; ?></td>
              <td>
                <?php
                $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$id'");
                list($qty) = mysqli_fetch_row($r2);
                echo $qty;
                ?>
              </td>
              <td>
                <?php
                $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$id'");
                list($won) = mysqli_fetch_row($r3);
                if ("" == $won) {
                  $won = 0;
                }
                echo currency_display($won);
                ?>
              </td>
              <td><?php echo currency_display($balance);  ?></td>
              <td align="center">
                <?php if ((time() - $last_visit) > 5 * 60) { ?>
                  <font color="#cc3232"><b>NO</b></font>
                <?php
                } else {
                ?>
                  <font color="#22cc32"><b>YES</b></font>
                <?php } ?>
              </td>
              <td align="center"><a href="flvby.php?go=suspend&id=<?php echo $id; ?>">Suspend</a></td>
              <td align="center"><a href="flvby.php?go=deluser&id=<?php echo $id; ?>">Delete</a></td>
            </tr>
          <?php
          }
          ?>
        </table>

        <h1>Question:</h1>
        <form name="support_form" action="flvby.php?go=answer&id=<?php echo $_GET['id']; ?>" method="POST">
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
                <textarea name="question" cols="50" rows="10"><?php echo nl2br($message); ?></textarea>
              </td>
            </tr>
            <tr>
              <td width="40%" align="right" valign="top">
                <b>Answer:</b>
              </td>
              <td width="60%" align="left" valign="top">
                <textarea name="answer" cols="50" rows="10"></textarea>
                </textarea>
              </td>
            </tr>
          </table>
          <br>
          <input type="hidden" name="support" value="1">
          <input type="submit" value="Submit Answer">
          <input type="button" value="Submit/Add to FAQ" onclick="support_form.action='flvby.php?go=faqadd&id=<?php echo $_GET['id']; ?>'; support_form.submit();">
          <input type="button" value="Go Back" onclick="location.href='flvby.php?go=support';">
        </form>
        <p>
        <div class="help">-This page allows the Admin to answer Support Tickets. <br />
          -The Admin can also submit this question/answer to the FAQ database by clicking "Submit/Add to FAQ." Note that if you choose this, the user's question will still be answered.<br />
          -Before submitting to the FAQ database, the Admin can slightly alter the question (example: fix spelling).</div>
      </div>
    <?php

      return (0);
    }

    function questions_answer()
    {
      global $db_prefix;
      global $link;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("update " . $db_prefix . "support set status='r',
  answer='" . mysqli_real_escape_string($link, $_POST['answer']) . "',
  message='" . mysqli_real_escape_string($link, $_POST['question']) . "'
  where id='" . $_GET['id'] . "'");

      questions_show();

      return (0);
    }

    function questions_delete()
    {
      global $db_prefix;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("delete from " . $db_prefix . "support where id='" . $_GET['id'] . "'");

      questions_show();

      return (0);
    }

    function questions_delete_category()
    {
      global $db_prefix;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("delete from " . $db_prefix . "support_categories where id='" . $_GET['id'] . "'");

      questions_categories_show();

      return (0);
    }


    function questions_delete_all()
    {
      global $db_prefix;

      if ((!isset($_GET['id'])) || (!is_numeric($_GET['id']))) {
        error_report(9);
        lotteries_show();
        return (9);
      }

      $r = my_query("delete from " . $db_prefix . "support where user_id='" . $_GET['id'] . "'");

      questions_show();

      return (0);
    }

    function questions_add_category()
    {
      global $db_prefix;

      while (list($key, $value) = each($_POST)) {
        if ("" == $value) {
          error_report(11);
          questions_show();
          return (11);
        }
      }

      $r = my_query("insert into " . $db_prefix . "support_categories(name) values('" . $_POST['categ_name'] . "')");

      questions_categories_show();

      return (0);
    }

    function credit_accounts()
    {
      global $db_prefix;

    ?>
      <div align="center">
        <h1>Credit Accounts</h1>
        Enter Username OR PayPal
        <form action="flvby.php?go=credits2" method="post">
          Username:
          <input type="text" name="username">
          PayPal:
          <input type="text" name="paypal">
          <br><br>
          Amount:
          <input type="text" name="amount">
          <br><br>
          <input type="submit" value="  Credit  ">
        </form>
        <p>
        <div class="help">-The page allows the Admin to credit users' accounts with funds. <br />
          -This could be used if there was a problem with the PayPal parser or the user chose to pay in a different medium.</div>
      </div>
    <?php

      return (0);
    }

    function credit_accounts2()
    {
      global $db_prefix;
      global $link;

      if ((("" == $_POST['username']) && ("" == $_POST['paypal'])) || ("" == $_POST['amount'])) {
        error_report(11);
        credit_accounts();
        return (11);
      }

      $r = my_query("select id from " . $db_prefix . "users
  where username='" . mysqli_real_escape_string($link, $_POST['username']) . "'
  or paypal='" . mysqli_real_escape_string($link, $_POST['paypal']) . "'");

      if (!mysqli_num_rows($r)) {
        error_report(23);
        return (23);
      }

      list($user_id) = mysqli_fetch_row($r);

      $r = my_query("update " . $db_prefix . "users set balance=balance+'" . $_POST['amount'] . "'
  where id='" . $user_id . "'");

      $r = my_query("select id, username, name, email, city, referrer_id, paypal, last_visit, balance
  from " . $db_prefix . "users where id = '$user_id'");

    ?>
      <div align="center">
        <h1>User Account(s) Successfully Credited</h1>
        <h2>User Details:</h2>
        <table width="100%" border="1" cellspacing="0" cellpadding="2">
          <tr>
            <th width="10%" bgcolor="#dddddd">Username</th>
            <th width="8%" bgcolor="#dddddd">Name</th>
            <th width="8%" bgcolor="#dddddd">E-Mail</th>
            <th width="8%" bgcolor="#dddddd">City</th>
            <th width="8%" bgcolor="#dddddd">Referrer</th>
            <th width="8%" bgcolor="#dddddd">PayPal</th>
            <th width="8%" bgcolor="#dddddd">Referrals</th>
            <th width="8%" bgcolor="#dddddd">Won ($)</th>
            <th width="8%" bgcolor="#dddddd">Balance</th>
            <th width="6%" bgcolor="#dddddd">Active</th>
            <th width="20%" bgcolor="#dddddd" colspan="2">Options</th>
          </tr>
          <?php
          while (list($id, $username, $name, $email, $city, $referrer_id, $paypal, $last_visit, $balance) = mysqli_fetch_row($r)) {
            if ($referrer_id) {
              $r1 = my_query("select username from " . $db_prefix . "users where id='$referrer_id'");
              list($referrer_username) = mysqli_fetch_row($r1);
            } else {
              $referrer_username = "None";
            }
          ?>
            <tr>
              <td><a href="flvby.php?go=userdetails&id=<?php echo $id; ?>"><?php echo $username; ?></a></td>
              <td><?php echo $name; ?></td>
              <td><?php echo $email; ?></td>
              <td><?php echo $city; ?></td>
              <td><?php echo $referrer_username; ?></td>
              <td><?php echo $paypal; ?></td>
              <td>
                <?php
                $r2 = my_query("select count(*) from " . $db_prefix . "users where referrer_id='$id'");
                list($qty) = mysqli_fetch_row($r2);
                echo $qty;
                ?>
              </td>
              <td>
                <?php
                $r3 = my_query("select sum(amount) from " . $db_prefix . "messages where user_id='$id'");
                list($won) = mysqli_fetch_row($r3);
                if ("" == $won) {
                  $won = 0;
                }
                echo currency_display($won);
                ?>
              </td>
              <td><?php echo currency_display($balance);  ?></td>
              <td align="center">
                <?php if ((time() - $last_visit) > 5 * 60) { ?>
                  <font color="#cc3232"><b>NO</b></font>
                <?php
                } else {
                ?>
                  <font color="#22cc32"><b>YES</b></font>
                <?php } ?>
              </td>
              <td align="center"><a href="flvby.php?go=suspend&id=<?php echo $id; ?>">Suspend</a></td>
              <td align="center"><a href="flvby.php?go=deluser&id=<?php echo $id; ?>">Delete</a></td>
            </tr>
          <?php
          }
          ?>
        </table>

      <?php

      return (0);
    }

    function image_verifications()
    {
      global $db_prefix;
      global $link;

      if (isset($_GET['id'])) {
        if (!is_numeric($_GET['id'])) {
          error_report(9);
          lotteries_show();
          return (9);
        }

        $r = my_query("update " . $db_prefix . "image_verifications set status='" . mysqli_real_escape_string($link, $_GET['value']) . "'
    where id='" . $_GET['id'] . "'");
      }



      ?>
        <div align="center">
          <h1>Image Verifications</h1>
          <table width="100%" border="1" cellspacing="0" cellpadding="2">
            <tr>
              <th width="60%" bgcolor="#dddddd">Page</th>
              <th width="20%" bgcolor="#dddddd">Status</th>
              <th width="20%" bgcolor="#dddddd">Options</th>
            </tr>
            <?php
            $r = my_query("select id,pagename, status from " . $db_prefix . "image_verifications");
            while (list($id, $pagename, $status) = mysqli_fetch_row($r)) {
            ?>
              <tr>
                <td><?php echo strtoupper($pagename); ?></td>
                <?php
                if ("n" == $status) {
                ?>
                  <td div align="center">Disabled</td>
                  <td div align="center"><a href="flvby.php?go=iv&id=<?php echo $id; ?>&value=y">Enable</a></td>
                <?php
                } else {
                ?>
                  <td div align="center">Enabled</td>
                  <td div align="center"><a href="flvby.php?go=iv&id=<?php echo $id; ?>&value=n">Disable</a></td>
                <?php
                }
                ?>
              </tr>
            <?php
            }
            ?>
          </table>
          <p>
          <div class="help">-Image Verifications force the user to enter a number displayed in a picture before performing a certain action (such as logging in). This prevent programs, spammers, etc. from performing mass actions such as registering thousands of accounts.<br />-The Admin can disable/enable Image Verifications on different parts of the website.</div>
        </div>
      <?php

      return (0);
    }

    function stats_show()
    {
      global $db_prefix;

      ?>
        <div align="center">
          <h1>Statistics</h1>
          <table width="100%">
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Users:</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "users");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Referred Users:</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "users where referrer_id>'0'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Not referred users:</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "users where referrer_id='0'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>New users:</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "users where signup_date>'" . (time() - 24 * 60 * 60) . "'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Open Support Tickets:</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "support where status='o'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>New Support Tickets:</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "support
    where date>'" . (time() - 24 * 60 * 60) . "' and status='o'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Total # of Lotteries Ending (within next 24 hrs):</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "lotteries
    where started < '" . (time() + 24 * 60 * 60) . "' - duration*24*60*60 and ended='0'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Total # of Site Hits (within last 24 hrs):</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select distinct ip from " . $db_prefix . "visits where date > '" . (time() - 24 * 60 * 60) . "'");
                echo mysqli_num_rows($r);
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Raffle Tickets Purchased (within last 24 hrs):
                </b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "tickets where date > '" . (time() - 24 * 60 * 60) . "'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
            <tr>
              <td width="50%" align="right" valign="top">
                <b>Total # of Users Who Logged In (within last 24 hrs):</b>
              </td>
              <td width="50%" align="left" valign="top">
                <?php
                $r = my_query("select count(*) from " . $db_prefix . "users where last_visit > '" . (time() - 24 * 60 * 60) . "'");
                list($qty) = mysqli_fetch_row($r);
                echo $qty;
                ?>
              </td>
            </tr>
          </table>
          <p>
          <div class="help">-This page displays various statistics about the website.</div>
        </div>
      <?php

      return (0);
    }

    function faq_add()
    {
      global $db_prefix;
      global $link;

      $search_line = $_POST['question'];

      $search_line = str_replace("\n", " ", $search_line);
      $search_line = str_replace("\r", "", $search_line);
      $search_line = str_replace("   ", " ", $search_line);
      $search_line = str_replace("  ", " ", $search_line);

      $r = my_query("insert into " . $db_prefix . "faq(question,answer,date, search) values(
  '" . mysqli_real_escape_string($link, $_POST['question']) . "',
  '" . mysqli_real_escape_string($link, $_POST['answer']) . "',
  '" . time() . "',
  '" . mysqli_real_escape_string($link, $search_line) . "')");

      if (isset($_POST['support'])) {
        questions_answer();
      } else {
        faq_show();
      }

      return (0);
    }

    function faq_show()
    {
      global $db_prefix;
      global $page_limit;

      if (isset($_GET['page'])) {
        $page = $_GET['page'];
      } else {
        $page = 1;
      }

      $r = my_query("select count(*) from " . $db_prefix . "faq");
      list($pages_qty) = mysqli_fetch_row($r);

      if ($page == ceil($pages_qty / $page_limit)) {
        $limit = $pages_qty % $page_limit;
      }

      if ((!isset($limit)) || (!$limit)) {
        $limit = $page_limit;
      }

      $r = my_query("select id,question,answer from " . $db_prefix . "faq
    order by date desc limit " . (($page - 1) * $page_limit) . ", $limit");

      ?>
        <div align="center">
          <h1>Frequently Asked Questions</h1>
        </div>
        <div align="left">
          <?php
          while (list($id, $question, $answer) = mysqli_fetch_row($r)) {
          ?>
            <b>Question:</b><?php echo nl2br($question); ?> <a href="flvby.php?go=delfaq&amp;id=<?= $id ?>">(Delete)</a><br>
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
            <a href="flvby.php?go=faq&page=<?php echo $i; ?>"><?php echo $i; ?></a>&nbsp;
          <?php
          }
          ?>
          <h1>Add New Question:</h1>
          <form name="support_form" action="flvby.php?go=faqadd" method="POST">
            <table width="80%" style="border:hidden">
              <tr>
                <td width="40%" align="right" valign="top" style="border:hidden">
                  <b>Question:</b>
                </td>
                <td width="60%" align="left" valign="top" style="border:hidden">
                  <textarea name="question" cols="50" rows="10"></textarea>
                </td>
              </tr>
              <tr>
                <td width="40%" align="right" valign="top" style="border:hidden">
                  <b>Answer:</b>
                </td>
                <td width="60%" align="left" valign="top">
                  <textarea name="answer" cols="50" rows="10"></textarea>
                  </textarea>
                </td>
              </tr>
            </table>
            <br>
            <input type="submit" value="  Submit  ">&nbsp;
          </form>
          <p>
          <div class="help">-This page displays the current FAQ database. It also allows the Admin to submit FAQ questions/answers.<br />-The Admin can also delete FAQ entries.</div>
        </div>
    <?php

      return (0);
    }


    function parse_get_params()
    {
      global $db_prefix;
      global $link;
      if (isset($_GET['go'])) {
        $go = $_GET['go'];
        if ('addlottery' == $go) {
          lotteries_add();
          return (1);
        }
        if ('dellottery' == $go) {
          lotteries_delete();
          return (1);
        }
        if ('randlottery' == $go) {
          lotteries_finish_randomly();
          return (1);
        }
        if ('manlottery' == $go) {
          lotteries_finish_manually();
          return (1);
        }
        if ('manlottery2' == $go) {
          lotteries_finish_manually2();
          return (1);
        }
        if ('users' == $go) {
          users_show();
          return (1);
        }
        if ('deluser' == $go) {
          users_delete();
          return (1);
        }
        if ('fraudchecker' == $go) {
          fraud_checker();
          return (1);
        }
        if ('delfraud' == $go) {
          frauds_delete();
          return (1);
        }
        if ('finished' == $go) {
          finished_show();
          return (1);
        }
        if ('archive' == $go) {
          archive_show();
          return (1);
        }
        if ('arclottery' == $go) {
          lotteries_send_to_arc();
          return (1);
        }
        if ('winners' == $go) {
          winners_show();
          return (1);
        }
        if ('stats' == $go) {
          stats_show();
          return (1);
        }
        if ('tickets' == $go) {
          tickets_add();
          return (1);
        }
        if ('tickets2' == $go) {
          tickets_add2();
          return (1);
        }
        if ('delticket' == $go) {
          tickets_delete();
          return (1);
        }
        if ('winnerslist' == $go) {
          debts_show();
          return (1);
        }
        if ('winnerslistcsv' == $go) {
          csv_show();
          return (1);
        }
        if ('deldebt' == $go) {
          debts_delete();
          return (1);
        }
        if ('frauds' == $go) {
          frauds_list();
          return (1);
        }
        if ('parser' == $go) {
          paypal_parser();
          return (1);
        }
        if ('suspend' == $go) {
          users_suspend();
          return (1);
        }
        if ('suspend2' == $go) {
          users_suspend2();
          return (1);
        }
        if ('unsuspend' == $go) {
          users_unsuspend();
          return (1);
        }
        if ('suspended' == $go) {
          suspended_show();
          return (1);
        }
        if ('fictitious' == $go) {
          fictitious_show();
          return (1);
        }
        if ('delfict' == $go) {
          fictitious_delete();
          return (1);
        }
        if ('profits' == $go) {
          profits_show();
          return (1);
        }
        if ('accept' == $go) {
          profits_accept();
          return (1);
        }
        if ('support' == $go) {
          questions_show();
          return (1);
        }
        if ('view' == $go) {
          questions_view();
          return (1);
        }
        if ('answer' == $go) {
          questions_answer();
          return (1);
        }
        if ('delque' == $go) {
          questions_delete();
          return (1);
        }
        if ('delallque' == $go) {
          questions_delete_all();
          return (1);
        }
        if ('userdetails' == $go) {
          users_details();
          return (1);
        }
        if ('userdeposit' == $go) {
          users_deposit();
          return (1);
        }
        if ('closed' == $go) {
          accounts_closed();
          return (1);
        }
        if ('closeacc' == $go) {
          accounts_close();
          return (1);
        }
        if ('reopenacc' == $go) {
          accounts_reopen();
          return (1);
        }
        if ('idle' == $go) {
          accounts_idle();
          return (1);
        }
        if ('credits' == $go) {
          credit_accounts();
          return (1);
        }
        if ('credits2' == $go) {
          credit_accounts2();
          return (1);
        }
        if ('iv' == $go) {
          image_verifications();
          return (1);
        }
        if ('stats' == $go) {
          stats_show();
          return (1);
        }
        if ('addcateg' == $go) {
          questions_add_category();
          return (1);
        }
        if ('editcateg' == $go) {
          questions_categories_show();
          return (1);
        }
        if ('delcateg' == $go) {
          questions_delete_category();
          return (1);
        }
        if ('faqadd' == $go) {
          faq_add();
          return (1);
        }
        if ('faq' == $go) {
          faq_show();
          return (1);
        }
        //delete faq
        if ('delfaq' == $go) {
          $r = my_query("delete from " . $db_prefix . "faq where id='" . mysqli_real_escape_string($link, $_GET['id']) . "'");

          faq_show();
          return (1);
        }
      }

      lotteries_show();

      return (0);
    }

    parse_get_params();

    //include_once("footer.php");
  } else {
    header("location:" . $adminlogin);
  }
    ?>