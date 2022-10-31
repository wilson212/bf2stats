<?php

// Database connection information
$DBIP = 'db';
$DBNAME = 'bf2stats';
$DBLOGIN = 'admin';
$DBPASSWORD = 'admin';

// Leader board title
$TITLE = 'BF2S Clone';

// Refresh time in seconds for stats
define ('RANKING_REFRESH_TIME', 600); // -> default: 600 seconds (10 minutes)

// Whether to hide bots from rankings
define ('RANKING_HIDE_BOTS', false);

// Whether to hide hidden players from rankings
define ('RANKING_HIDE_HIDDEN_PLAYERS', false);

// Number of players to show on the leaderboard frontpage
define ('LEADERBOARD_COUNT', 25);
?>
