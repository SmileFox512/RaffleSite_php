<?php

include_once("config.php");

$table_name = $db_prefix . "users";
$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                date int not null default '0',
                username varchar(255) not null,
                username_hash varchar(255) not null,
                name varchar(255) not null,
                password varchar(255) not null,
                password_hash varchar(255) not null,
                email varchar(255) not null,
                city varchar(255) not null,
                paypal varchar(255) not null,
                referrer_id int not null default '0',
                last_visit int not null default '0',
                last_login int not null default '0',
                last_password_request int not null default '0',
                balance float not null default '0',
                country varchar(255) not null,
                zip int not null default '0',
                gender enum('m','f') not null default 'm',
                birthdate int not null default '0',
                signup_date int not null default '0',
                ip int,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "fictitious";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                user_id int not null,
                username varchar(255) not null,
                name varchar(255) not null,
                password varchar(255) not null,
                email varchar(255) not null,
                city varchar(255) not null,
                paypal varchar(255) not null,
                referrer_id int not null default '0',
                last_visit int not null default '0',
                ip int,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}


$table_name = $db_prefix . "lotteries";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                ticket_price int not null,
                available int not null default '100',
                win_percentage int not null,
                started int not null,
                ended int not null default '0',
                duration int not null,
                description text,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "tickets";

$r = @mysqli_query($link,"drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                user_id int not null,
                lottery_id int not null,
                won float not null default '0',
                price int not null default '0',
                date int not null default '0',
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "archive";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                lot_id int not null,
                amount float not null,
                started int not null,
                duration int not null,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "messages";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                date int not null,
                user_id int not null,
                lottery_id int not null,
                mestype enum('w','r') not null default 'w',
                displayed enum('y','n') not null default 'n',
                amount float not null default '0',
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "debts";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                user_id int not null,
                amount float not null default '0',
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "frauds";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link, 
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                user_id int not null,
                reason enum('am','au') not null default 'am',
                incorrect int not null default '0',
                correct int not null default '0',
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "suspended";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                user_id int not null,
                started int not null default '0',
                until int not null,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "profits";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link, 
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                date int not null,
                amount float not null,
                paid enum('y','n') not null default 'n',
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "support";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                user_id int not null,
                date int not null,
                status enum('r','o') default 'o',
                category varchar(255) not null default 'm',
                subject varchar(255),
                message text,
                answer text,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "faq";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                date int not null,
                question text,
                search text,
                answer text,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "closed_accounts";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                user_id int not null,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "failed_logins";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                username varchar(255),
                date int not null default '0',
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "image_verifications";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                pagename varchar(255),
                status enum('y','n') not null default 'y',
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "visits";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                ip int unsigned not null,
                date int unsigned not null,
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$table_name = $db_prefix . "support_categories";

$r = @mysqli_query($link, "drop table " . $table_name);

$r = mysqli_query ($link,
                "create table " . $table_name . " (
                id int unsigned not null AUTO_INCREMENT,
                name varchar(255),
                primary key (id)
                )");

if ($r) echo "Table <em>" . $table_name . "</em> created.<BR>\n";
else {
  $r=mysqli_error($link);
  echo "<br><b>Error while " . $table_name . " table create:</b>$r<br>";
  exit;
}

$r = @mysqli_query($link, "insert into " . $db_prefix . "support_categories(name) values('No Ticket Reports')");
$r = @mysqli_query($link, "insert into " . $db_prefix . "support_categories(name) values('Suggestions')");
$r = @mysqli_query($link, "insert into " . $db_prefix . "support_categories(name) values('Questions Regarding Site Usage')");
$r = @mysqli_query($link, "insert into " . $db_prefix . "support_categories(name) values('Misc')");
if (!$r) {
  echo mysqli_error($link);
}

$r = @mysqli_query($link, "insert into " . $db_prefix . "image_verifications(pagename) values('forget password')");
$r = @mysqli_query($link, "insert into " . $db_prefix . "image_verifications(pagename) values('login')");
$r = @mysqli_query($link, "insert into " . $db_prefix . "image_verifications(pagename) values('sign up')");
$r = @mysqli_query($link, "insert into " . $db_prefix . "image_verifications(pagename) values('support tickets')");
if (!$r) {
  echo mysqli_error($link);
}

echo "<br>Script installed successfully<br>";

?>