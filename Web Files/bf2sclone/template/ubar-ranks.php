<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .' - Ranks</title>

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
		<h1 id="page-title">BF2 Ranks<small> '. $TITLE .' Awards and Ranks Guide</small></h1>
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
						<li><a href="'.$ROOT.'?go=ubar&p=medals-sf">SF: Medals</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ranks" class="current">Ranks</a></li>
						<li><a href="http://wiki.bf2s.com/weapons/unlocks/">Unlocks</a></li>
						<li><a href="http://wiki.bf2s.com/classes/">Kits</a></li>
					</ul>
					<div style="clear:both"> </div>

					<div id="ubarContent"> 
						<p>
							The major difference between several of the higher ranks is that you get the &quot;higher&quot; rank if you have 
							certain badges. Whichever rank you get, each pair only gives you one unlock. 
						</p>
						<p>
							It is also worth noting that the upper ranks <strong>are not progressive</strong>. You will only earn one rank per point bracket. 
							If you make it to Master Sergeant without the awards, you will not be promoted to First upon earning the missing awards. 
						</p>

						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Private</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_0.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>

									<ul>
										<li>Notes: First Rank, No Reqs</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Private First Class</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_1.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Score: 150</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Lance Corporal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_2.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Private First Class</li>
										<li>Score: 500</li>
										<li>Unlock Available</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Corporal</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_3.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Lance Corporal</li>
										<li>Score: 800</li>
										<li>Unlock Available</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Sergeant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_4.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Corporal</li>
										<li>Score: 2,500</li>
										<li>Unlock Available</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Staff Sergeant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_5.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Sergeant</li>
										<li>Score: 5,000</li>
										<li>Unlock Available</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Gunnery Sergeant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_6.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Staff Sergeant</li>
										<li>Score: 8,000</li>
										<li>Unlock Available</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Master Sergeant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_7.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Gunnery Sergeant</li>
										<li>Score: 20,000</li>
										<li>Unlock Available</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">First Sergeant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_8.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Gunnery Sergeant</li>
										<li>Score: 20,000</li>
										<li>
											Awards: Basic Knife Combat Badge, Basic Pistol Combat Badge, Basic Assault Combat Badge, Basic Anti-tank Combat Badge, Basic
											Sniper Combat Badge, Basic Spec Ops Combat Badge, Basic Support Combat Badge, Basic Engineer Combat Badge, Basic Medic Combat
											Badge
										</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Master Gunnery Sergeant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_9.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Master Sergeant</li>
										<li>Score: 50,000</li>
										<li>Unlock Available</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Sergeant Major</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_10.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Master Sergeant</li>
										<li>Score: 50,000</li>
										<li>
											Awards: Basic Armor Badge, Basic Transport Badge, Basic Helicopter Badge, Basic Aviator Badge, Basic Air Defense Badge, Basic
											Ground Defense Badge
										</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Sergeant Major of the Corps</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_11.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Master Sergeant</li>
										<li>Notes: This rank is only awarded to one player at a time. It can not be earned in game via points/awards.</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">2nd Lieutenant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_12.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Master Gunnery Sergeant</li>
										<li>Score: 60,000</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">1st Lieutenant</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_13.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: 2nd Lieutenant</li>
										<li>Score: 75,000</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Captain</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_14.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: 1st Lieutenant</li>
										<li>Score: 90,000</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Major</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_15.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Captain</li>
										<li>Score: 115,000</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Lieutenant Colonel</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_16.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Major</li>
										<li>Score: 125,000</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Colonel</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_17.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Lieutenant Colonel</li>
										<li>Score: 150,000</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Brigadier General</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_18.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Colonel</li>
										<li>Score: 180,000</li>
										<li>
											Awards: Veteran Armor Badge, Veteran Transport Badge, Veteran Helicopter Badge, Veteran Aviator Badge, Veteran Air Defense
											Badge, Veteran Ground Defense Badge
										</li>
										<li>Notes: Requires 1080 hours playtime</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Major General</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_19.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Brigadier General</li>
										<li>Score: 180,000</li>
										<li>
											Awards: Veteran Knife Combat Badge, Veteran Pistol Combat Badge, Veteran Assault Combat Badge, Veteran Anti-tank Combat
											Badge, Veteran Sniper Combat Badge, Veteran Spec Ops Combat Badge, Veteran Support Combat Badge, Veteran Engineer Combat Badge,
											Veteran Medic Combat Badge
										</li>
										<li>Notes: Requires 1250 hours playtime</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">Lieutenant General</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_20.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Major General</li>
										<li>Score: 200,000</li>
										<li>Notes: Requires 1440 hours playtime</li>
									</ul>
								</td>
							</tr>
						</table>

						
						<table border="0" cellspacing="0" cellpadding="0" class="basic-stat">
							<tr>
								<th colspan="3">General</th>
							</tr>
							<tr>
								<td width="96" valign="top"><img border="0" style="border: 1px solid #222" src="'.$ROOT.'game-images/ranks/ubar/rank_21.jpg" class="badge" /></td>
								<td valign="top">
									<p><strong>Requirements:</strong></p>
									<ul>
										<li>Rank: Lieutenant General</li>
										<li>Score: 200,000</li>
										<li>Notes: Highest rank of the month</li>
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