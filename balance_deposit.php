<?php
    include_once('config.php');
    include_once('utils.php');

    global $db_prefix;
    global $user_id;
    if(isset($_POST['amount']) && !empty($_POST['amount'])) {
        $amount = $_POST['amount'];
        $r = my_query("update " . $db_prefix . "users set balance=balance+{$amount} where id={$_SESSION['lt_user_id']}");
        if($r){
            echo true;
        }    
    }
    
?>