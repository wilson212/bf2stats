<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .' - SF Medals</title>

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
		<h1 id="page-title">BF2 SF Medals<small> '. $TITLE .' Awards and Ranks Guide</small></h1>
		<div id="page-3">
			<div id="content"><div id="content-id"><!-- template header end == begin content below -->

				<div id="ubar"> 
					<ul id="ubarnav">
						<li class="li1"><a href="'.$ROOT.'?go=ubar">Main</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons">Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons-sf">SF: Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges">Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges-sf">SF: Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals">Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals-sf" class="current">SF: Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ranks">Ranks</a></li>
						<li><a href="http://wiki.bf2s.com/weapons/unlocks/">Unlocks</a></li>
						<li><a href="http://wiki.bf2s.com/classes/">Kits</a></li>
					</ul>
					<div style="clear:both"> </div>

					<div id="ubarContent"> 
						<p style="margin:0 20px;"><em><strong>IAR:</strong> You must meet this requirement <strong>I</strong>n <strong>A</strong> single <strong>R</strong>ound.</em></p>

						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Navy Seal Special Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2261913.png" class="badge" /></td>
								<td valign="top">
									<ul>
										<li>50h while playing SEALs</li>
										<li>100 best round while playing SEALs</li>
										<li>50 wins while playing SEALs</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">SAS Special Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2261919.png" class="badge" /></td>
								<td valign="top">
									<ul>
										<li>50h while playing SAS</li>
										<li>100 best round while playing SAS</li>
										<li>50 wins while playing SAS</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">SPETZ Special Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2261613.png" class="badge" /></td>
								<td valign="top">
									<ul>
										<li>50h while playing SPETZ</li>
										<li>100 best round while playing SPETZ</li>
										<li>50 wins while playing SPETZ</li>
									</ul>
								</td>
							</tr>
						</table>

						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">MECSF Special Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2261303.png" class="badge" /></td>
								<td valign="top">
									<ul>
										<li>50h while playing MECSF</li>
										<li>100 best round while playing MECSF</li>
										<li>50 wins while playing MECSF</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Rebel Special Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2261802.png" class="badge" /></td>
								<td valign="top">
									<ul>
										<li>50h while playing Rebel</li>
										<li>100 best round while playing Rebel</li>
										<li>50 wins while playing Rebel</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Insurgent Special Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2260914.png" class="badge" /></td>
								<td valign="top">
									<ul>
										<li>50h while playing Insurgent</li>
										<li>100 best round while playing Insurgent</li>
										<li>50 wins while playing Insurgent</li>
									</ul>
								</td>
							</tr>
						</table>
					</div>
				</div>

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