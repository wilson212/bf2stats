<?php
$template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>My Leader Board, '. $TITLE .'</title>

	<link rel="icon" href="'.$ROOT.'favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="'.$ROOT.'favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/two-tiers.css">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/nt.css">
	<link rel="stylesheet" type="text/css" media="print" href="'.$ROOT.'css/print.css">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/default.css">

	<script type="text/javascript">/* no frames */ if(top.location != self.location) top.location.replace(self.location);</script>
	<script type="text/javascript" src="'.$ROOT.'js/nt2.js"></script>
</head>

<body class="inner">

<div id="page-1">
	<div id="page-2">
		<h1 id="page-title">My Leader Board</h1>
		<div id="page-3">
	
			<div id="content"><div id="content-id"><!-- template header end == begin content below -->
	
				<!-- 
				<ul id="stats-nav">
					<li><a href="'.$ROOT.'">Home</a></li>
					<li><a href="'.$ROOT.'?go=search">Search Stats</a></li>
					<li><a href="'.$ROOT.'?go=currentranking">Current Ranking</a></li>
					<li class="current"><a href="'.$ROOT.'?go=my-leaderboard">My Leaderboard</a></li>
				</ul>
				-->
				<div id="mlb-instructions">
					<p>
						Setup your own leaderboard to track the stats of other Battlefield 2 players.
						It\'s pretty easy! Just type in a list of PID numbers (comma separated)
						and click "Get Leader Board". Once you\'ve got the list set right, you
						can just bookmark the page, copy &amp; paste the url to friends, or
						click "Save Leader Board" to keep the board in a cookie.
					</p>
					<p class="poof">
						This service will work for BF2 players only. They must play on leased,
						rented, or public Ranked Servers for the EA game Battlefield 2 in order
						for this gaming website to be able to track their stats.
					</p>

					<form action="'.$ROOT.'?go=my-leaderboard" method="post">
						<label>Player ID\'s (Nick* or Number): <br><input name="leaderboard" size="80" value="'.$LEADERBOARD.'" type="text"></label>
						<input name="set" value="Save Leader Board" type="submit">
						<input name="get" value="Get Leader Board" type="submit">
					</form>

					<p>
						<strong>You MUST use PID numbers.</strong> Also, the limit is 50 players.
					</p>

				</div>

				<form action="/compare.php" method="get">
				<table border="0" align="center" cellpadding="0" cellspacing="0" class="stat sortable" id="myleaderboard">
				<tbody>
					<tr>
						<th><a href="#" class="sortheader" onClick="ts_resortTable(this);return false;">Personal Leaderboard<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th> <!-- colspan="2" -->
						<th><a href="#" class="sortheader" onClick="ts_resortTable(this);return false;">Score<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th><a href="#" class="sortheader" onClick="ts_resortTable(this);return false;">SPM<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th><a href="#" class="sortheader" onClick="ts_resortTable(this);return false;">K:D<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th><a href="#" class="sortheader" onClick="ts_resortTable(this);return false;">Time Played<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th class="nosort">Last Update</th>
						<th><a href="#" class="sortheader" onClick="ts_resortTable(this);return false;">PID<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th class="nosort"><img nicetitle="Remove A Player" src="'.$ROOT.'site-images/user_delete.png"></th>
					</tr>';

				foreach ($LEADER as $value)
				{
					$template .= '		
					<tr>
						<td>
							<img src="'.$ROOT.'game-images/ranks/icon/rank_'.$value['rank'].'.gif" alt="" style="border: 0pt none ;"> 
							<a href="'.$ROOT.'?pid='.$value['id'].'">&nbsp;'.$value['name'].'&nbsp;<img src="'.$ROOT.'game-images/flags/'.$value['country'].'.png" width="16" height="12"></a>
						</td>
						<td>'.$value['score'].'</td>
						<td>'.$value['spm'].'</td>
						<td>';
							if ($value['kdr']) {
                                $template .= $value['kdr'];
                            } else {
                                $template .= $value['kills'];
                            }
							$template .= '
						</td>
						<td title="'.$value['time'].'">'.intToTime($value['time']).'</td>
						<td>';
							if (getLastUpdate(getcwd().'/cache/'.$value['id'].'.cache')>0) {
                                $template .= intToTime(getLastUpdate(getcwd().'/cache/'.$value['id'].'.cache'));
                            } else {
                                $template .=  'N/A';
                            }
							$template .= '
						</td>
						<td>'.$value['id'].'</td>
						<td>
							<a nicetitle="Remove '.$value['name'].'" href="'.$ROOT.'?go=my-leaderboard&amp;remove='.$value['id'].'">
							<img nicetitle="Remove '.$value['name'].'" src="'.$ROOT.'site-images/user_delete.png"></a>
						</td>
					</tr>';
				}			
			
				$template .= '
				</tbody>
				</table>
				</form>

				<div style="margin: 20px auto 0pt; text-align: center;"></div>
				<!-- end content == footer below -->
	
				<hr class="clear">
	
			</div></div> <!-- content-id --><!-- content -->
			<a id="secondhome" href="'.$ROOT.'"> </a>

		</div><!-- page 3 -->
	</div><!-- page 2 -->
	
	<div id="footer">This page was processed in {:PROCESSED:} seconds.</div>
	<ul id="navitems">
		<li><a href="'. $ROOT .'">Home</a></li>
		<li><a href="'. $ROOT .'?go=search">Search Players</a></li>
		<li><a href="'. $ROOT .'?go=my-leaderboard">My Leader Board</a></li>
		<li><a href="'. $ROOT .'?go=currentranking">Rankings</a></li>
		<li><a href="http://ubar.bf2s.com/">UBAR</a></li>
		<li><a href="http://wiki.bf2s.com/">Wiki</a></li>
	</ul>
</div><!-- page 1 -->
</body>
</html>';