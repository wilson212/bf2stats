<?php

// Database connection information
$DBIP = 'localhost';
$DBNAME = 'bf2stats';
$DBLOGIN = 'admin';
$DBPASSWORD = 'admin';

// Leader board title
$TITLE = 'BF2s Clone';

// Refresh time in seconds for stats
define ('RANKING_REFRESH_TIME', 600); // -> default: 600 seconds (10 minutes)

// Number of players to show on the leaderboard frontpage
define ('LEADERBOARD_COUNT', 25);



// === DONOT EDIT BELOW THIS LINE == //



// Determine our http hostname, and site directory
$host = rtrim($_SERVER['HTTP_HOST'], '/');
$site_dir = dirname( $_SERVER['PHP_SELF'] );
$site_url = str_replace('//', '/', $host .'/'. $site_dir);
while(strpos($site_url, '//') !== FALSE) $site_url = str_replace('//', '/', $site_url);

// Root url to bf2sclone
$ROOT = str_replace( '\\', '', 'http://' . rtrim($site_url, '/') .'/' );

// Your domain name (eg: www.example.com)
$DOMAIN = preg_replace('@^(http(s)?)://@i', '', $host);

// cleanup
unset($host, $site_dir, $site_url);

// Setup the database connection
$link = mysql_connect($DBIP, $DBLOGIN, $DBPASSWORD) or die('Could not connect: ' . mysql_error());
mysql_select_db($DBNAME) or die('Could not select database');
?>