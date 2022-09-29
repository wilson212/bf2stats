<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .' - Badges</title>

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
		<h1 id="page-title">BF2 Badges<small> '. $TITLE .' Awards and Ranks Guide</small></h1>
		<div id="page-3">
			<div id="content"><div id="content-id"><!-- template header end == begin content below -->

				<div id="ubar"> 
					<ul id="ubarnav">
						<li class="li1"><a href="'.$ROOT.'?go=ubar">Main</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons">Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons-sf">SF: Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges" class="current">Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges-sf">SF: Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals">Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=medals-sf">SF: Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ranks">Ranks</a></li>
						<li><a href="http://wiki.bf2s.com/weapons/unlocks/">Unlocks</a></li>
						<li><a href="http://wiki.bf2s.com/classes/">Kits</a></li>
					</ul>
					<div style="clear:both"> </div>

					<div id="ubarContent"> 
						<p style="margin:0 20px;"><em><strong>IAR:</strong> You must meet this requirement <strong>I</strong>n <strong>A</strong> single <strong>R</strong>ound.</em></p>

						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Engineer Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031105_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 kills while an Engineer</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031105_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills while an Engineer</li>
										<li>15h while playing Engineer</li>
										<li>Basic Engineer Combat Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031105_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 40 kills while an Engineer</li>
										<li>100h while playing Engineer</li>
										<li>Veteran Engineer Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to engineer personnel who have proven themselves during any period the unit was engaged in active combat.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Sniper Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031109_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 kills while a Sniper</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031109_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 15 kills while a Sniper</li>
										<li>15h while playing Sniper</li>
										<li>Basic Sniper Combat Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031109_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 35 kills while a Sniper</li>
										<li>100h while playing Sniper</li>
										<li>Veteran Sniper Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to sniper personnel who have proven themselves during any period the unit was engaged in active combat.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Medic Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031113_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 kills while a Medic</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031113_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills while a Medic</li>
										<li>15h while playing Medic</li>
										<li>Basic Medic Combat Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031113_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 40 kills while a Medic</li>
										<li>100h while playing Medic</li>
										<li>Veteran Medic Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to medical personnel who have proven themselves during any period the unit was engaged in active combat.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Spec Ops Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031115_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 kills while a SpecOp</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031115_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills while a SpecOp</li>
										<li>15h while playing Special-Ops</li>
										<li>Basic Spec Ops Combat Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031115_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 40 kills while a SpecOp</li>
										<li>100h while playing Special-Ops</li>
										<li>Veteran Spec Ops Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to spec ops personnel who have proven themselves during any period the unit was engaged in active combat.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Assault Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031119_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 kills while Assault</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031119_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills while Assault</li>
										<li>15h while playing Assault</li>
										<li>Basic Assault Combat Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031119_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 40 kills while Assault</li>
										<li>100h while playing Assault</li>
										<li>Veteran Assault Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to assault personnel who have proven themselves during any period the unit was engaged in active combat</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Anti-tank Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031120_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 kills while Anti-tank</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031120_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills while Anti-tank</li>
										<li>15h while playing Anti-tank</li>
										<li>Basic Anti-tank Combat Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031120_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 40 kills while Anti-tank</li>
										<li>100h while playing Anti-tank</li>
										<li>Veteran Anti-tank Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to anti-tank personnel who have proven themselves during any period the unit was engaged in active combat</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Support Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031121_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 kills while Support</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031121_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills while Support</li>
										<li>15h while playing Support</li>
										<li>Basic Support Combat Badge</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031121_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 40 kills while Support</li>
										<li>100h while playing Support</li>
										<li>Veteran Support Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to support personnel who have proven themselves during any period the unit was engaged in active combat.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Knife Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031406_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 7 kills with Knife</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031406_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>50 kills with the Knife</li>
										<li>Basic Knife Combat Badge</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031406_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>100 kills with the Knife</li>
										<li>Veteran Knife Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have proven themselves with a knife during any period the unit was engaged in active combat</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Pistol Combat Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031619_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 kills with a Pistol</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031619_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 7 kills with a Pistol</li>
										<li>50 kills with the Pistols</li>
										<li>Basic Pistol Combat Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031619_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 18 kills with a Pistol</li>
										<li>500 kills with the Pistols</li>
										<li>Veteran Pistol Combat Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have proven themselves with a handgun during any period the unit was engaged in active
								combat</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Explosives Ordinance Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1032415_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 Kills with any combination of C4/Claymore/AT-Mine</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1032415_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 Kills with any combination of C4/Claymore/AT-Mine</li>
										<li>50 kills with the Explosives (C4, Claymore, AT Mine)</li>
										<li>Basic Explosives Ordinance Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1032415_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 30 Kills with any combination of C4/Claymore/AT-Mine</li>
										<li>300 kills with the Explosives (C4, Claymore, AT Mine)</li>
										<li>Veteran Explosives Ordinance Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have proven themselves with explosive ordinance during any period the unit was engaged in active
								combat.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Command Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190304_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 40 Command Points</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190304_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 25 minutes as Commander</li>
										<li>1,000 command points</li>
										<li>Basic Command Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190304_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 30 minutes as Commander</li>
										<li>10,000 command points</li>
										<li>Veteran Command Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded on the basis of proven skill in the area of command.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Engineer Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190507_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 Repair points</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190507_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 Repair points</li>
										<li>15h while playing Engineer</li>
										<li>Basic Engineer Badge</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190507_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 Repair points</li>
										<li>100h while playing Engineer</li>
										<li>250 repair points</li>
										<li>Veteran Engineer Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to engineering personnel on the basis of proven skill in the area of repair.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>First Aid Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190601_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 Heal points</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190601_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 Heal points</li>
										<li>15h while playing Medic</li>
										<li>Basic First Aid Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1190601_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 Heal points</li>
										<li>100h while playing Medic</li>
										<li>750 heals</li>
										<li>Veteran First Aid Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to medical personnel on the basis of proven skill in the area of field medicine.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Resupply Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1191819_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 Ammo points</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1191819_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 Ammo points</li>
										<li>15h while playing Support</li>
										<li>Basic Resupply Badge</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1191819_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 25 Ammo points</li>
										<li>100h while playing Support</li>
										<li>500 resupply points</li>
										<li>Veteran Resupply Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to support personnel on the basis of proven skill in the area of ammo resupply.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Ground Defense Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>

							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031923_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 minutes in Ground Defense (Mounted MG or TOW)</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031923_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 kills in Ground Defense (Mounted MG or TOW)</li>
										<li>Basic Ground Defense Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1031923_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills in Ground Defense (Mounted MG or TOW)</li>
										<li>Veteran Ground Defense Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have been exposed to enemy fire while performing their duties in a ground defense vehicle.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Air Defense Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220104_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 minutes on Air Defense (Stinger or AA Vehicle)</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220104_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 kills while on Air Defense (Stinger or AA Vehicle)</li>

										<li>Basic Air Defense Badge</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220104_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 20 kills while on Air Defense (Stinger or AA Vehicle)</li>
										<li>Veteran Air Defense Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have been exposed to enemy fire while performing their duties in an air defense vehicle.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Armor Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>

							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220118_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 minutes in Armor</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220118_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 12 kills in Armor</li>
										<li>100h while in Armor</li>
										<li>Basic Armor Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220118_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 24 kills in Armor</li>
										<li>400h while in Armor</li>
										<li>Veteran Armor Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have been exposed to enemy fire while performing their duties in an armored vehicle.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Aviator Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220122_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 minutes in an Airplane</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220122_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 12 kills in an Airplane</li>
										<li>50h while in Aviator</li>
										<li>Basic Aviator Badge</li>
									</ul>
								</td>

								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220122_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 24 kills in an Airplane</li>
										<li>150h while in Aviator</li>
										<li>Veteran Aviator Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have been exposed to enemy fire while performing their duties in an airplane,</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Helicopter Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220803_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 15 minutes in a Helicopter</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220803_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 12 kills in a Helicopter</li>
										<li>50h while in Helicopter</li>
										<li>Basic Helicopter Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1220803_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 24 kills in a Helicopter</li>
										<li>150h while in Helicopter</li>
										<li>Veteran Helicopter Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have been exposed to enemy fire while performing their duties in a helicopter.</td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Transport Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>

								<th>Veteran</th>

								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1222016_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 10 minutes in a Transport</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1222016_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 5 road kills while in Transport</li>
										<li>25h while in Transport</li>
										<li>200 driver special ability points</li>
										<li>Basic Transport Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1222016_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 11 road kills while in Transport</li>
										<li>75h while in Transport</li>
										<li>2,000 driver special ability points</li>
										<li>Veteran Transport Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3">Awarded to personnel who have been exposed to enemy fire while performing their duties in a transport vehicle.</td>
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