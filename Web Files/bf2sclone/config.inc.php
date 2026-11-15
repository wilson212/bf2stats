<?php

// Database connection information
$DBIP = 'localhost';
$DBNAME = 'bf2stats';
$DBLOGIN = 'bf2stats';
$DBPASSWORD = 'bf2stats';

// Leader board title
$TITLE = 'BF2s Clone';

// Refresh time in seconds for stats
define ('RANKING_REFRESH_TIME', 600); // -> default: 600 seconds (10 minutes)

// Number of players to show on the leaderboard frontpage
define ('LEADERBOARD_COUNT', 25);



// === DONOT EDIT BELOW THIS LINE == //



// Determine our http hostname, and site directory
$host = rtrim((string) $_SERVER['HTTP_HOST'], '/');
$site_dir = dirname( (string) $_SERVER['PHP_SELF'] );
$site_url = str_replace('//', '/', $host .'/'. $site_dir);
while(str_contains($site_url, '//')) $site_url = str_replace('//', '/', $site_url);

// Root url to bf2sclone
$ROOT = str_replace( '\\', '', 'http://' . rtrim($site_url, '/') .'/' );

// Your domain name (eg: www.example.com)
$DOMAIN = preg_replace('@^(http(s)?)://@i', '', $host);

// cleanup
unset($host, $site_dir, $site_url);

// Setup the database connection
$link = mysqli_connect($DBIP, $DBLOGIN, $DBPASSWORD, $DBNAME) or die('Could not connect: ' . mysqli_connect_error());
define('$link', $link);
?>