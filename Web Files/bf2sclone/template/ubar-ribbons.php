<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .' - Ribbons</title>

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
		<h1 id="page-title">BF2 Ribbons<small> '. $TITLE .' Awards and Ranks Guide</small></h1>
		<div id="page-3">
			<div id="content"><div id="content-id"><!-- template header end == begin content below -->

				<div id="ubar"> 
					<ul id="ubarnav">
						<li class="li1"><a href="'.$ROOT.'?go=ubar">Main</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons" class="current">Ribbons</a></li>
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
						
						<p style="margin:0 20px;"><em>
							<strong>IAR:</strong> You must meet this requirement <strong>I</strong>n <strong>A</strong> single <strong>R</strong>ound.</em>
						</p>

	
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Air-Defense Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3040109.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the individual who performed their duty while engaged in active air-defense combat</p>

								<ul>
									<li>IAR: 3 minutes in Air Defense (SAM, AA Vehicle)</li><li>IAR: 11 kills with Air Defense (SAM, AA Vehicle)</li>
								</ul>
							</td>
						</tr>
						</table>
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Ground Defense Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3040718.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the individual who participated in sustained ground combat</p>
								<ul>
									<li>IAR: 3 minutes in TOW/Mounted Machine Gun</li><li>IAR: 5 kills in TOW/Mounted Machine Gun</li>
								</ul>
							</td>
						</tr>
						</table>
					
					
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Infantry Officer Ribbon</th>
						</tr>

						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3150914.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the superior dedication of unit leaders during combat situations</p>
								<ul>
									<li>IAR: 25 minutes as Squad Leader</li>
									<li>250 team points</li>
								</ul>
							</td>
						</tr>
						</table>
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Staff Officer Ribbon</th>
						</tr>

						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3151920.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize superior achievement by unit commanders during sustained combat situations</p>
								<ul>
									<li>IAR: 28 minutes as Commander</li><li>IAR: 50 Command points (before 2x)</li>
								</ul>
							</td>
						</tr>
						</table>
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Aerial Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3190105.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the individual who performed their duty while engaged in active aerial combat</p>
								<ul>
									<li>IAR: 15 minutes in an Airplane</li><li>IAR: 19 kills in an Airplane</li>		
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Armored Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3190118.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the individual who performed their duty while engaged in active armored combat</p>
								<ul>
									<li>IAR: 20 minutes in an Armor Vehicle</li><li>IAR: 19 kills in an Armor Vehicle</li>	
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Crew Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3190318.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the individual who performed satisfactory duty while on flying status as a crewmember.</p>
								<ul>
									<li>IAR: 1 Driver Special Ability Point</li><li>IAR: 13 Driver Kill Assists</li><li>IAR: 5 kills</li>
								</ul>
							</td>
						</tr>
						</table>


						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Distinguished Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3190409.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the superior achievement of an individual in all aspects of unit command.</p>
								<ul>
									<li>IAR: 15 team points</li>
									<li>10h squad member time</li>
									<li>10h squad leader time</li>
									<li>10h command time</li>
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Far East Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3190605.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize service in the Far East Theater.</p>
								<ul>
									<li>1s  while playing Daqing Oilfields</li>
									<li>1s  while playing Dalian Plant</li>
									<li>1s  while playing Dragon Valley</li>
									<li>1s  while playing FuShe Pass</li>
									<li>1s  while playing Songhua Stalemate</li>
									<li>1s  while playing Wake Island 2007</li>
									<li>Notes: Participant -- As best as we can tell, you get this simply for playing the game for a short period.</li>
								</ul>
							</td>
						</tr>
						</table>

						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Middle East Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3191305.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize service in the Middle East Theater </p>
								<ul>
									<li>1s  while playing Kubra Dam</li>
									<li>1s  while playing Mashtuur City</li>
									<li>1s  while playing Operation Clean Sweep</li>
									<li>1s  while playing Zatar Wetlands</li>
									<li>1s  while playing Strike at Karkand</li>
									<li>1s  while playing Sharqi Peninsula</li>
									<li>1s  while playing Gulf of Oman</li>
									<li>Notes: Participant -- As best as we can tell, you get this simply for playing the game for a short period.</li>
								</ul>
							</td>
						</tr>
						</table>


						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Helicopter Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3190803.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize the individual who performed their duty while engaged in active helicopter combat</p>
								<ul>
									<li>IAR: 15 minutes in Helicopter</li><li>IAR: 19 kills in Helicopter</li>
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Meritorious Unit Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3211305.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize an individual\'s contribution to their unit during sustained combat situations</p>
								<ul>
									<li>IAR: 26 minutes in a Squad</li><li>IAR: 40 team points</li>
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Valorous Unit Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3212201.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize extraordinary heroism in action against an armed enemy.</p>
								<ul>
									<li>IAR: 45 team points</li>
									<li>25h squad member time</li>
									<li>25h squad leader time</li>
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Airborne Ribbon</th>

						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3240102.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize individuals who have  participated in sustained airborne operations during combat situations</p>
								<ul>
									<li>IAR: 10 seconds in a Parachute</li>
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Combat Action Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3240301.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize active participation in ground or air combat</p>
								<ul>
									<li>IAR: 18 kills</li>
									<li>10 kill streak</li>
								</ul>
							</td>
						</tr>
						</table>
				
				
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Good Conduct Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3240703.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize exemplary behavior, efficiency, and fidelity in active service</p>
								<ul>
									<li>IAR: 14 kills</li><li>IAR: No Team kills/damage/vehicle damage</li>
									<li>50h total time played</li>
								</ul>
							</td>
						</tr>
						</table>
				
				
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">Legion of Merit Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3241213.png" class="badge" /></td>
							<td valign="top">
								<p>
									Awarded to recognize the individual who, through gallantry, determination and esprit de corps, succeeds in his/her mission while 
									under difficult and hazardous conditions
								</p>
								<ul>
									<li>IAR: 50 team points</li>
									<li>200h total time played</li>
									<li>10 kill streak</li>
									<li>8 death streak</li>
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">War College ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3242303.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize achievements which, through their dedication and gallantry, have set the individual apart and above other commanders.</p>
								<ul>
									<li>100h command time</li>
									<li>200 wins</li>
									<li>25,000 command points</li>
								</ul>
							</td>
						</tr>
						</table>
						
						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
						<tr>
							<th colspan="3">North American Service Ribbon</th>
						</tr>
						<tr>
							<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/3271401.png" class="badge" /></td>
							<td valign="top">
								<p>Awarded to recognize service in the American Theater.</p>
								<ul>
									<li>IAR: Unknown.</li>
									<li>25h  while in the USMC theater</li>
									<li>1s  while playing Operation Harvest</li>
									<li>1s  while playing Operation Road Rage</li>
									<li>1s  while playing Midnight Sun</li>
									<li>Notes: Unknown.</li>
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