<?php
  include_once("config.php");
  include_once("utils.php");

  function winner($id, $amount) {
    global $db_prefix;
    
    $r = my_query("select t.user_id, t.lottery_id, u.referrer_id
                      from " . $db_prefix . "tickets t, " . $db_prefix ."users u
                      where t.id='$id' and t.user_id=u.id");

    list($user_id, $lottery_id, $referrer_id) = mysqli_fetch_row($r);
    
    $r = my_query("select available from " . $db_prefix . "lotteries where id='$lottery_id'");
    
    list($available) = mysqli_fetch_row($r);

    if ($referrer_id) {
      $referrer_amount = floor(5 * $amount)/100;
      $winner_amount = floor(95 * $amount)/100;

      $referrer_amount = floor($referrer_amount * $available)/100;

      $r = my_query("insert into " . $db_prefix . "messages(date,user_id,lottery_id,mestype, amount)
                        values('" . time() . "','$referrer_id', '$lottery_id', 'r', '$referrer_amount')");
      $r = my_query("insert into " . $db_prefix . "debts(user_id,amount)
                        values('$referrer_id','$referrer_amount')");
    }
    else $winner_amount = $amount;

    $winner_amount = floor($winner_amount * $available)/100;

    $r = my_query("update " . $db_prefix . "tickets set won='$winner_amount' where id='$id'");

    $r = my_query("insert into " . $db_prefix . "messages(date,user_id,lottery_id,mestype, amount)
                      values('" . time() . "','$user_id', '$lottery_id', 'w', '$winner_amount')");
    $r = my_query("insert into " . $db_prefix . "debts(user_id,amount)
                      values('$user_id','$winner_amount')");

    return(0);
  }

  function finish_lottery($id) {
    global $db_prefix;
    global $link;

    $r = my_query("select win_percentage, ticket_price from " . $db_prefix . "lotteries where id='$id'");
    list($win_percentage, $ticket_price) = mysqli_fetch_row($r);

    $r = my_query("select id from " . $db_prefix . "tickets where lottery_id='$id'");
    $players_qty = mysql_num_rows($r);

    $winners_qty = floor($players_qty * $win_percentage / 100);

    if (!$winners_qty) {
      if ($players_qty) $winners_qty = 1; else {
        $r = my_query("update " . $db_prefix . "lotteries set ended='" . time() . "' where id='$id'");
        return(1);
      }
    }

    $winner_prize = floor(100 * $ticket_price * $players_qty / $winners_qty) / 100;

    $players = array();

    while(list($ticket_id) = mysqli_fetch_row($r)) {
      array_push($players, $ticket_id);
    }

    srand((float)microtime() * 1000000);

    for ($i = 0; $i < $winners_qty; $i++) {
      shuffle($players);
      $ticket_id = array_pop($players);
      winner($ticket_id, $winner_prize);
    }

    $r = my_query("update " . $db_prefix . "lotteries set ended='" . time() . "' where id='$id'");

    return(0);
  }

  $r = my_query("select id from " . $db_prefix . "lotteries where ended='0'
                   and started+(duration*24*3600) < '" . strtotime(date("Y",time()) . "-" . date("m",time()) . "-" . date("d",time()) . " 23:59:59") . "'");

  while (list($lottery_id) = mysqli_fetch_row($r)) {
    finish_lottery($lottery_id);
  }
?>