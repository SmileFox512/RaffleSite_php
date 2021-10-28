<?php

//Edit the MySQL server values below. You will need to replace: DatabaseName, User, Password, and Host

define('DB_NAME', 'raffle_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost:3306');

define('DEBUG_MODE', '0');


$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die ("Could not connect to database. Try later<BR>");

//If you plan on running multiple sites on one database, make sure you have a different prefix for each website.
  $db_prefix = "raffle_";

// Enter the PayPal where you want users to send $ to

  $paypal = "YourPayPal@mail.com";
 
//Below is the admin password and admin username used to get into the admin menu. MAKE SURE TO EDIT THESE ASAP!
  $admin_password = "admin";
  $admin_username = "admin";
  
  //If you rename the a12.php login file, make sure you make the appropriate change below
  $adminlogin = "a12.php";
  
  //Enter your website's url. Example: www.google.com/

  // $siteurl = "http://RaffleScript.com/design1/";

  $page_limit = 40;

  // Limit for password requests (minutes):

  $password_request_limit = 10;
?>