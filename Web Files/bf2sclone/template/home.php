<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .'</title>

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
	
		<h1 id="page-title">'. $TITLE .' Leaderboard</h1>
		<div id="page-3">
			<div id="content"><div id="content-id">
	
				<!--
				<ul id="stats-nav">
					<li class="current"><a href="'.$ROOT.'">Home</a></li>
					<li><a href="'.$ROOT.'?go=search">Search Stats</a></li>
					<li><a href="'.$ROOT.'?go=currentranking">Current Ranking</a></li>
					<li><a href="'.$ROOT.'?go=my-leaderboard">My Leaderboard</a></li>
				</ul>
				-->
			
				<div id="content">
					<div id="content-id"><!-- template header end == begin content below -->
						<table align="center">
							<tbody>
							<!-- <tr><th colspan="3"><center><a href="../index.php">Return to Index</a></center></th></tr> -->
								<tr><th colspan="3"><h2>Top '. LEADERBOARD_COUNT .' Players</h2></th></tr>
								<tr>
									<th>#</th>
									<th>Nick</th>
									<th>Score</th>
								</tr>';
								foreach ($topten as $key => $value)
								{		
									$template .= '
									<tr>
										<td>'.($key+1).'</td>
										<td>
											<img src="'.$ROOT.'game-images/ranks/icon/rank_'.$value['rank'].'.gif">&nbsp;
											<a href="'.$ROOT.'?pid='.$value['id'].'">'.$value['name'].'</a>&nbsp;
											<img src="'.$ROOT.'game-images/flags/'.strtoupper((string) $value['country']).'.png" height="12" width="16">
										</td>
										<td>'.$value['score'].'</td>
									</tr>';
								}			
								$template .= '
							</tbody>
						</table>

						<a id="secondhome" href="'.$ROOT.'"> </a>
						<!-- end content == footer below -->
	
						<hr class="clear">
	
					</div>
				</div> <!-- content-id --><!-- content -->
			</div></div>
		</div>	<!-- Page 3 -->
		
		<div id="footer">This page was last updated {:LASTUPDATE:} ago. Next update will be in {:NEXTUPDATE:}<br>
		This page was processed in {:PROCESSED:} seconds.</div>
	
		<ul id="navitems">
			<li><a href="'. $ROOT .'">Home</a></li>
			<li><a href="'. $ROOT .'?go=search">Search Players</a></li>
			<li><a href="'. $ROOT .'?go=my-leaderboard">My Leader Board</a></li>
			<li><a href="'. $ROOT .'?go=currentranking">Rankings</a></li>


			<li><a href="http://ubar.bf2s.com/">UBAR</a></li>
			<li><a href="http://wiki.bf2s.com/">Wiki</a></li>
		</ul>
	</div><!-- page 2 -->
</div>
</body>
</html>';
?>