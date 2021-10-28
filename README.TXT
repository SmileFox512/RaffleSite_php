Raffle Script

-RaffleScript.com

Requirements:

- PHP
- MySQL

Directions:
1. Unzip files.
2. Edit variables in config.php
3. Run setup.php
4. Delete setup.php
5. Make sure to delete this file (README.txt) from your server
6. To access admin panel, navigate to "a12.php"
7. YOU WILL NEED TO RUN CRON.PHP EVERYDAY! (THIS CAN BE DONE BY NAVIGATING TO THE URL USING YOUR BROWSER OR USING CRON)

Designs:
-The default design is design1.
-If you want to change a design simply open one of the design folders, and replace the files in the root directory.


Files description:

SETUP.PHP - installation file
CONFIG.PHP - options (you must change it)
FLVBY.PHP - admin section (protected by password set in config.php)
INDEX.PHP - main file.
ERROR.PHP - error messages


Tables description:

lt_archive - store minimal info about lotteries sended to archive
lt_debts - store info about winners who has not been paid yet
lt_fictitious - store registration info of suspended users who tried to register second account
lt_frauds - store potential fraud ID's with reasons and ID's of users who was paid too much
lt_lotteries - store info about open and finished lotteries
lt_messages - store messages for users who won.
lt_suspended - store ID and time for suspended users
lt_tickets - store info about all tickets bought
lt_users - store registration info for all users
