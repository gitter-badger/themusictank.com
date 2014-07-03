# Read me file for The Music Tank

Generating a new db schema
 Update line 117 of bootstrap.php
/Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php schema generate --force

Applying the db schema
/Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php schema create

Running cron tasks

 /Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php App daily themusictank.nvi
 php app/Console/cake.php App daily themusictank.com

 /Applications/MAMP/bin/php/php5.4.10/bin/php app/Console/cake.php App twohours themusictank.nvi
 php app/Console/cake.php App twohours themusictank.com
