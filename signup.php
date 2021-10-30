<?php

include_once("utils.php");
include_once("config.php");


if (isset($_SESSION['lt_user_id'])) {
  header("Location: index.php");
}

  if (isset($_POST['username'])) {
    user_signup2();
  }
  else {
    include_once("header.php");
    user_signup();
  }


function user_signup() {
  global $db_prefix;
?>

<div align="center">
<h1>Sign Up</h1>
<form action="signup.php" method="post">
<table width="100%">
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   Username:<br>  </td>
  <td align="left">
   <input type="text" name="username" size="30" <?php if (isset($_POST['username'])) echo "value=\"" . $_POST['username'] . "\"" ?>>  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   Name:<br>  </td>
  <td align="left">
   <input type="text" name="name" size="30" <?php if (isset($_POST['name'])) echo "value=\"" . $_POST['name'] . "\"" ?>>  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   Password:<br>  </td>
  <td align="left">
   <input type="password" name="password" size="30">  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   Confirm Password:<br>  </td>
  <td align="left">
   <input type="password" name="password2" size="30">  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   E-mail:<br>  </td>
  <td align="left">
   <input type="text" name="email" size="30" <?php if (isset($_POST['email'])) echo "value=\"" . $_POST['email'] . "\"" ?>>  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   Paypal:<br>  </td>
  <td align="left">
   <input type="text" name="paypal" size="30" <?php if (isset($_POST['paypal'])) echo "value=\"" . $_POST['paypal'] . "\"" ?>>  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   City:<br>  </td>
  <td align="left">
   <input type="text" name="city" size="30" <?php if (isset($_POST['city'])) echo "value=\"" . $_POST['city'] . "\"" ?>>  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   Country:<br>  </td>
  <td align="left"><input type="text" name="country" size="30" <?php if (isset($_POST['country'])) echo "value=\"" . $_POST['country'] . "\"" ?> /></td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   ZIP Code:<br>  </td>
  <td align="left">
   <input type="text" name="zip" size="30" <?php if (isset($_POST['zip'])) echo "value=\"" . $_POST['zip'] . "\"" ?>>  </td>
</tr>
<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">
   Gender:<br>  </td>
  <td align="left">
  <select name="gender">
  <option value="m">male
  <option value="f">female
  </select>  </td>
</tr>


<tr>
  <td width="10%">  </td>
  <td width="30%" valign="top" align="right">Date of Birth: <br>  </td>
  <td  align="left">
  <select name="day">
  <?php
    for ($i = 1; $i < 32; $i++) {
      ?>
      <option value="<?php echo $i ?>"><?php echo $i ?>
      <?php
    }
  ?>
  </select> 
  / 
  <select name="month">
    <?php
    for ($i = 1; $i < 13; $i++) {
      ?>
    <option value="<?php echo $i ?>"><?php echo $i ?>
    <?php
    }
  ?>
    </option>
  </select>
/  
<select name="year">
    <?php
    for ($i = 1950; $i < (date("Y",time()) - 3); $i++) {
      ?>
    <option value="<?php echo $i ?>"><?php echo $i ?>
    <?php
    }
  ?>
    </option>
  </select>
(Day/Month/Year) </td>
</tr>
</table>
<?php
   $r = my_query("select * from " . $db_prefix . "image_verifications where pagename='sign up' and status='y'");
   if (mysqli_num_rows($r)) {
?>
 <table width="100%">
 <tr>
 <td width="10%">  </td>
  <td width="30%" align="right" valign="top">
   Enter the number exactly how you see it.<br>
  </td>
  <td valign="top" width="46">
   <input type="text" name="checker" size="6">
  </td>
  <td width="672" align="left" valign="top">
   <img src="jpeg.php" border="0" style="border:#000000; border-style:solid; border-width:1px">  </td>
 </tr>
 </table>
<?php
  }
?>
<br>
<input type="submit" value="  Submit  ">
</form>
</div>
    <?php

    return(0);
  }

function user_signup2() {
  global $db_prefix;
  global $link;

   $r = my_query("select * from " . $db_prefix . "image_verifications where pagename='sign up' and status='y'");
   if (mysqli_num_rows($r)) {
     if (md5(md5($_POST['checker'])) != $_SESSION['jpeg_code']) {
	 include_once("header.php");
      error_report(28);
      user_signup();
      return(28);
     }
   }


  while (list($key,$value) = each($_POST)) {
    if ("" == $value) {
      include_once("header.php");
      error_report(11);
	  
      user_signup();
      return(11);
    }

    if (strlen($value) > 35) {
      include_once("header.php");
      error_report(25);
      user_signup();
      return(25);
    }

    if (("email" == $key) || ("paypal" == $key)) {
      if (valid_email($value)) {
        $value = str_replace("@","",$value);
        $value = str_replace(".","",$value);
      }
      else {
        include_once("header.php");
        error_report(1);
        user_signup();
        return(1);
      }
    }

    if (!preg_match("/^[a-zA-Z0-9\s]*$/", $value)) {
      include_once("header.php");
      error_report(7);
      echo "<br>$value";
      user_signup();
      return(7);
    }

    if (("username" == $key) && (!preg_match("/^[a-zA-Z0-9]*$/", $value))) {
      include_once("header.php");
      error_report(6);
      echo "<br>$value";
      user_signup();
      return(6);
    }

    if (("country" == $key) && (!preg_match("/^[a-zA-Z]*$/", $value))) {
      include_once("header.php");
      error_report(22);
      user_signup();
      return(22);
    }

    if (("username" == $key) && (strlen($value) < 3)) {
      include_once("header.php");
      error_report(5);
      user_signup();
      return(5);
    }
    if (("username" == $key) && (is_numeric(substr($value,0,1)))) {
      include_once("header.php");
      error_report(4);
      user_signup();
      return(4);
    }
   /* if (("zip" == $key)) {
      include_once("header.php");
      error_report(2);
      user_signup();
      return(2);
    }*/
  }

  $username = htmlspecialchars($_POST['username']);
  $password = $_POST['password'];
  $password2 = $_POST['password2'];
  $name = $_POST['name'];
  $email = $_POST['email'];
  $city = $_POST['city'];
  $paypal = $_POST['paypal'];
  $country = $_POST['country'];
  $gender = $_POST['gender'];
  $zip = $_POST['zip'];
  $birthdate = strtotime($_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['day']);

  if ($password != $password2) {
    include_once("header.php");
    error_report(12);
    user_signup();
    return(12);
  }

  $r = my_query("select id from " . $db_prefix . "users
  where username='" . mysqli_real_escape_string($link, $username) . "'");

  if (mysqli_num_rows($r)) {
    include_once("header.php");
    error_report(13);
    user_signup();
    return(13);
  }

  $r = my_query("select id from " . $db_prefix . "users
  where email='" . mysqli_real_escape_string($link, $email) . "'");

  if (mysqli_num_rows($r)) {
    include_once("header.php");
    error_report(17);
    user_signup();
    return(17);
  }

  $r = my_query("select id from " . $db_prefix . "users
  where paypal='" . mysqli_real_escape_string($link, $paypal) . "'");

  if (mysqli_num_rows($r)) {
    include_once("header.php");
    error_report(18);
    user_signup();
    return(18);
  }

  if (isset($_SESSION['lt_referrer_id'])) {
    $referrer_id = $_SESSION['lt_referrer_id'];
  }
  else {
    $referrer_id = 0;
  }

  $r = my_query("select u.id from " . $db_prefix . "users u, " . $db_prefix . "suspended s
  where s.user_id=u.id and u.ip='" . ip2long($_SERVER['REMOTE_ADDR']) . "'");

  if (mysqli_num_rows($r)) {
    list($fraud_id) = mysqli_fetch_row($r);
  }
  if (isset($_COOKIE['lt_user_id']) && (0 != $_COOKIE['lt_user_id'])) {
    $r = my_query("select id from " . $db_prefix . "suspended where user_id='" . mysqli_real_escape_string($link, $_COOKIE['lt_user_id']) . "'");
    if (mysqli_num_rows($r)) {
      $fraud_id = $_COOKIE['lt_user_id'];
    }
  }
  if (isset($fraud_id)) {
    $r = my_query("update " . $db_prefix . "suspended set until='0'
    where user_id='" . mysqli_real_escape_string($link, $fraud_id) . "'");

    $r = my_query("insert into " . $db_prefix . "fictitious(user_id,username,
    name, password, email, city, paypal, last_visit, referrer_id, ip) values(
    '$fraud_id',
    '" . mysqli_real_escape_string($link, $username) . "',
    '" . mysqli_real_escape_string($link, $name) . "',
    '" . mysqli_real_escape_string($link, $password) . "',
    '" . mysqli_real_escape_string($link, $email) . "',
    '" . mysqli_real_escape_string($link, $city) . "',
    '" . mysqli_real_escape_string($link, $paypal) . "',
    '" . time() . "',
    '$referrer_id',
    '" . ip2long($_SERVER['REMOTE_ADDR']) . "'
    )");

    setcookie("lt_user_id", "$fraud_id", time() + 3600*24*365*100);

    include_once("header.php");
    echo "Your account suspended forever";

    return(1);
  }

  $r = my_query("insert into " . $db_prefix . "users(date,username, name, password,
  email, city, paypal, last_visit, referrer_id, ip,country,zip,gender,birthdate,
  last_login, signup_date, username_hash,password_hash) values(
  '" . time() . "',
  '" . mysqli_real_escape_string($link, $username) . "',
  '" . mysqli_real_escape_string($link, $name) . "',
  '" . mysqli_real_escape_string($link, $password) . "',
  '" . mysqli_real_escape_string($link, $email) . "',
  '" . mysqli_real_escape_string($link, $city) . "',
  '" . mysqli_real_escape_string($link, $paypal) . "',
  '" . time() . "',
  '$referrer_id',
  '" . ip2long($_SERVER['REMOTE_ADDR']) . "',
  '" . mysqli_real_escape_string($link, $country) . "',
  '" . mysqli_real_escape_string($link, $zip) . "',
  '" . mysqli_real_escape_string($link, $gender) . "',
  '" . mysqli_real_escape_string($link, $birthdate) . "',
  '" . time() . "',
  '" . time() . "',
  '" . mysqli_real_escape_string($link, md5(md5($username))) . "',
  '" . mysqli_real_escape_string($link, md5(md5($password))) . "'
  )");

  $_SESSION['lt_user_id'] = mysqli_insert_id($link);
  $_SESSION['lt_user_pass'] = $password;

  $logged = true;

  include_once("header.php");

  ?>
  <div align="center">
  Congratulations! You have successfully registered!<br>
  Click <a href="index.php">here</a> to login
  </div>
  <?php

  return(0);
}

  include_once("footer.php");

?>