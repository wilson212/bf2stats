<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .' - Medals</title>

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
		<h1 id="page-title">BF2 Medals<small> '. $TITLE .' Awards and Ranks Guide</small></h1>
		<div id="page-3">
			<div id="content"><div id="content-id"><!-- template header end == begin content below -->

				<div id="ubar"> 
					<ul id="ubarnav">
						<li class="li1"><a href="'.$ROOT.'?go=ubar">Main</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons">Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons-sf">SF: Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges">Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges-sf">SF: Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals" class="current">Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals-sf">SF: Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ranks">Ranks</a></li>
						<li><a href="http://wiki.bf2s.com/weapons/unlocks/">Unlocks</a></li>
						<li><a href="http://wiki.bf2s.com/classes/">Kits</a></li>
					</ul>
					<div style="clear:both"> </div>

					<div id="ubarContent"> 
						<p style="margin:0 20px;"><em><strong>IAR:</strong> You must meet this requirement <strong>I</strong>n <strong>A</strong> single <strong>R</strong>ound.</em></p>

						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Bronze Star</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2051902.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to those individuals who have distinguished themselves through proven skill and teamwork</p>
									<ul>
										<li>IAR: Third place</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Silver Star</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2051919.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to those individuals who have distinguished themselves through superior skill and teamwork</p>
									<ul>
										<li>IAR: Second place</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Gold Star</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2051907.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to those individuals who have distinguished themselves through excellent skill and teamwork</p>
									<ul>
										<li>IAR: First place</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Distinguished Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2020419.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to an individual who distinguishes themselves by exceptionally meritorious service in a duty of great responsibility</p>
									<ul>
										<li>IAR: Team score of 45</li>
										<li>100h command time</li>
										<li>100h squad leader time</li>
										<li>100h squad member time</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Combat Infantry Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2020903.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to individuals who have distinguished themselves above and beyond that normally expected of infantry in a combat
									situation.</p>
									<ul>
										<li>200h total time played</li>
										<li>Basic Knife Combat Badge</li>
										<li>Basic Pistol Combat Badge</li>
										<li>Basic Assault Combat Badge</li>
										<li>Basic Anti-tank Combat Badge</li>
										<li>Basic Sniper Combat Badge</li>
										<li>Basic Spec Ops Combat Badge</li>
										<li>Basic Support Combat Badge</li>
										<li>Basic Engineer Combat Badge</li>
										<li>Basic Medic Combat Badge</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Marksman Infantry Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2020913.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to individuals who have distinguished themselves by sustained meritorious achievement during combat situations.</p>
									<ul>
										<li>300h total time played</li>
										<li>Combat Infantry Medal</li>
										<li>Veteran Knife Combat Badge</li>
										<li>Veteran Pistol Combat Badge</li>
										<li>Veteran Assault Combat Badge</li>
										<li>Veteran Anti-tank Combat Badge</li>
										<li>Veteran Sniper Combat Badge</li>
										<li>Veteran Spec Ops Combat Badge</li>
										<li>Veteran Support Combat Badge</li>
										<li>Veteran Engineer Combat Badge</li>
										<li>Veteran Medic Combat Badge</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Sharpshooter Infantry Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2020919.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to individuals who have excelled in every action expected of the infantry in a combat situation</p>
									<ul>
										<li>400h total time played</li>
										<li>Marksman Infantry Medal</li>
										<li>Expert Knife Combat Badge</li>
										<li>Expert Pistol Combat Badge</li>
										<li>Expert Assault Combat Badge</li>
										<li>Expert Anti-tank Combat Badge</li>
										<li>Expert Sniper Combat Badge</li>
										<li>Expert Spec Ops Combat Badge</li>
										<li>Expert Support Combat Badge</li>
										<li>Expert Engineer Combat Badge</li>
										<li>Expert Medic Combat Badge</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Medal of Valor</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2021322.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to the individual who distinguishes themselves by gallantry and intrepidity at the risk of their life above and beyond the
									call of duty; the deed performed must have been one of personal bravery or self sacrifice so conspicuous as to clearly distinguish
									the individual above his/her comrades and must have involved the risk of life.</p>
									<ul>
										<li>250h total time played</li>
										<li>5,000 driver special ability points</li>
										<li>1,000 flag defense points</li>
										<li>30,000 team points</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Navy Cross</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2021403.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to members of the United States Armed Forces for extraordinary heroism in action while engaged in military operations
									involving conflict with an opposing foreign force</p>
									<ul>
										<li>100 best round while playing USMC</li>
										<li>100h while playing USMC</li>
										<li>100 wins while playing USMC</li>
										<li>Notes: Generally you do not receive this medal during the round, but it will appear in your stats anonymously.</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Golden Scimitar</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2020719.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to members of the Middle Eastern Coalition for extraordinary heroism in action while engaged in military operations
									involving conflict with an opposing foreign force</p>
									<ul>
										<li>100 best round while playing MEC</li>
										<li>100h while playing MEC</li>
										<li>100 wins while playing MEC</li>
										<li>Notes: Generally you do not receive this medal during the round, but it will appear in your stats anonymously.</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">People\'s Medallion</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2021613.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to members of the People\'s Liberation Army for extraordinary heroism in action while engaged in military operations
									involving conflict with an opposing foreign force</p>
									<ul>
										<li>100 best round while playing PLA</li>
										<li>100h while playing PLA</li>
										<li>100 wins while playing PLA</li>
										<li>Notes: Generally you do not receive this medal during the round, but it will appear in your stats anonymously.</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Combat Action Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2190303.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to any person who has distinguished themselves by superior achievement while participating in infantry combat.</p>
									<ul>
										<li>IAR: Play for 33 minutes</li>
										<li>250h total time played</li>
										<li>25,000 kills</li>
										<li>25 kill streak</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Helicopter Combat Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2190308.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to any person who has distinguished themselves by superior achievement while participating in helicopter combat.</p>

									<ul>
										<li>IAR: 30 kills in a Helicopter</li>
										<li>100h while in Helicopter</li>
										<li>5000 kills while in Helicopter</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Air Combat Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2190309.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to any person who has distinguished themselves by superior achievement while participating in aerial combat.</p>

									<ul>
										<li>IAR: 25 kills in a Plane</li>
										<li>100h while in Aviator</li>
										<li>5000 kills while in Aviator</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Armor Combat Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2190318.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to any person who has distinguished themselves by superior achievement while participating in armored combat.</p>
									<ul>
										<li>IAR: 25 kills in an Armor Vehicle</li>
										<li>100h while in Armor</li>
										<li>5000 kills while in Armor</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Good Conduct Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2190703.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to those individuals who, through exemplary conduct to their comrades, have shown themselves to be models of efficiency
									and fidelity</p>
									<ul>
										<li>IAR: 27 kills and NO Team kills, damage or vehicle damage</li>
										<li>250h total time played</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Meritorious Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2191319.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to an individual for exceptional conduct in the performance of outstanding services and achievements in support of their
									comrades</p>
									<ul>
										<li>250h total time played</li>
										<li>1,000 heals</li>
										<li>1,000 repair points</li>
										<li>1,000 resupply points</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Purple Heart</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2191608.png" class="badge" /></td>
								<td valign="top">
									<p>Awarded to any member of the armed forces who has been killed in any action with an opposing armed force of a foreign country</p>
									<ul>
										<li>IAR: A K:D ratio of 1:4 with a minimum of 5 kills/20 deaths</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">European Union Special Service Medal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" src="'.$ROOT.'game-images/awards/ubar/2270521.png" class="badge" /></td>

								<td valign="top">
									<p>Awarded to members of the European Union Armed Forces for extraordinary heroism in action while engaged in military operations
									involving conflict with an opposing foreign force</p>
									<ul>
										<li>50h while playing European Union</li>
										<li>50 wins while playing European Union</li>
										<li>100 best round while playing European Union</li>
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