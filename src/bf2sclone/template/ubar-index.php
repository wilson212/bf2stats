<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .' - UBAR</title>

	<link rel="icon" href="'.$ROOT.'favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="'.$ROOT.'favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/two-tiers.css">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/nt.css">
	<link rel="stylesheet" type="text/css" media="print" href="'.$ROOT.'css/print.css">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/default.css">
	
	<style type="text/css">#ubarnav,#ubar,#ubarContent{width:100%}#ubarnav li,#ubarnav li.li1{width:10%}</style>
	<link rel="stylesheet" type="text/css" media="print" href="'.$ROOT.'css/ubarp.css" />

	<script type="text/javascript">/* no frames */ if(top.location != self.location) top.location.replace(self.location);</script>
	<script type="text/javascript" src="'.$ROOT.'js/nt2.js"></script>
</head>

<body class="inner">
<div id="page-1">
	<div id="page-2">
		<h1 id="page-title">UBAR<small> '. $TITLE .' Battlefield 2 Awards and Ranks Guide</small></h1>
		<div id="page-3">
			<div id="content"><div id="content-id"><!-- template header end == begin content below -->

				<div id="ubar"> 
					<ul id="ubarnav">
						<li class="li1"><a href="'.$ROOT.'?go=ubar" class="current">Main</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons">Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons-sf">SF: Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges">Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges-sf">SF: Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals">Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals-sf">SF: Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ranks">Ranks</a></li>
						<li><a href="http://wiki.bf2s.com/weapons/unlocks/">Unlocks</a></li>
						<li><a href="http://wiki.bf2s.com/classes/">Kits</a></li>
					</ul>
					<div style="clear:both"> </div>

					<div id="ubarContent"> 
						
						<table width="100%" class="basic-stat" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th><h3>Welcome!</h3></th>
							</tr>

							<tr>
								<td>
									<p>If you are trying to figure out what medals, ribbons, badges or ranks you can earn in Battlefield 2, you have come to to the right place. UBAR is the definitive guide to what it takes to receive every award in Battlefield 2. Read up and start earning those shiny pieces of glory!</p>
								</td>
							</tr>
						</table>
						
						<table width="75%" border="0" cellspacing="0" cellpadding="0" align="center">
							<tr align="center">

								<td><a style="border: 0" href="'.$ROOT.'?go=ubar&p=ribbons"><img src="'.$ROOT.'game-images/awards/ubar/3242303.png" width="96" height="96" alt="" /> <br />
									BF2 Ribbons</a>
									<br />
									<i>-or-</i>
									<br />
									<a style="border: 0" href="'.$ROOT.'?go=ubar&p=ribbons-sf">Special Forces</a></td>
								<td><a style="border: 0" href="'.$ROOT.'?go=ubar&p=badges"><img src="'.$ROOT.'game-images/awards/ubar/1190601_2.png" width="96" height="96" alt="" /> <br />

									BF2 Badges</a>
									<br />
									<i>-or-</i>
									<br />
									<a style="border: 0" href="'.$ROOT.'?go=ubar&p=badges-sf">Special Forces</a></td>
								<td><a style="border: 0" href="'.$ROOT.'?go=ubar&p=medals"><img src="'.$ROOT.'game-images/awards/ubar/2020913.png" width="96" height="96" alt="" /> <br />
									BF2 Medals</a>

									<br />
									<i>-or-</i>
									<br />
									<a style="border: 0" href="'.$ROOT.'?go=ubar&p=medals-sf">Special Forces</a></td>
								<td><a style="border: 0" href="'.$ROOT.'?go=ubar&p=ranks"><img src="'.$ROOT.'game-images/ranks/ubar/rank_3.jpg" width="96" height="96" alt="" /> <br />
									Ranks</a></td>
								<td><a style="border: 0" href="http://wiki.bf2s.com/weapons/unlocks/"><img src="site-images/ubar/l96unlockiconci3.png" width="96" height="96" alt="" /> <br />

									Unlocks</a></td>
								<td><a style="border: 0" href="http://wiki.bf2s.com/classes/"><img src="site-images/ubar/kitantitankac6.jpg" width="96" height="96" alt="" /> <br />
									Kits</a></td>
							</tr>
						</table>
						
						<table width="100%" class="basic-stat" border="0" cellspacing="0" cellpadding="0">
							<tr>

								<th><h3>Reading The Guide</h3></th>
							</tr>
							<tr>
								<td>
									<p>Please note, some requirements are on a per round basis, some are on a global basis. Most have been marked appropriately. </p>
									<p>Global requirements must be met <em>before</em> you join a server. The per round requirement will then trigger the award. All IAR (In A Round) requirements must be met in the same round. </p>

									<p>If you find any errors, please drop by the forums to bring it up.</p>
								</td>
							</tr>
						</table>
						
						<table width="100%" class="basic-stat" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<th><h3>Special Thanks:</h3></th>
							</tr>

							<tr>
								<td>
									<ul>
										<li>blue60007</li>
										<li>phusioN</li>
										<li>-=Scott=-</li>
										<li>TheDude</li>

									</ul>
								</td>
							</tr>
						</table>
						
					</div>

				</div><!-- /ubar -->

				<!-- end content == footer below -->
				<hr class="clear" />

			</div></div> <!-- content-id --><!-- content -->
		</div>	<!-- Page 3 -->
	
		<ul id="navitems">
			<li><a href="'. $ROOT .'">Home</a></li>
			<li><a href="'. $ROOT .'?go=my-leaderboard">My Leader Board</a></li>
			<li><a href="'. $ROOT .'?go=currentranking">Rankings</a></li>
			<li><a href="'. $ROOT .'?go=ubar">UBAR</a></li>
			<li><a href="http://wiki.bf2s.com/">Wiki</a></li>
		</ul>
		
		<form action="'.$ROOT.'?go=search" method="post" id="getstats">
			<label for="pid">Get Stats</label>
			<input type="text" name="searchvalue" id="pid" value="" />
			<input type="submit" class="btn" value="Go" />
		</form>
	</div><!-- page 2 -->
	<div id="footer">This page was processed in {:PROCESSED:} seconds.</div>
</div>
</body>
</html>';
?>