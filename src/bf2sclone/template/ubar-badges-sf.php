<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>'. $TITLE .' - SF Badges</title>

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
		<h1 id="page-title">BF2 SF Badges<small> '. $TITLE .' Awards and Ranks Guide</small></h1>
		<div id="page-3">
			<div id="content"><div id="content-id"><!-- template header end == begin content below -->

				<div id="ubar"> 
					<ul id="ubarnav">
						<li class="li1"><a href="'.$ROOT.'?go=ubar">Main</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons">Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=ribbons-sf">SF: Ribbons</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges">Badges</a></li>
						<li><a href="'.$ROOT.'?go=ubar&p=badges-sf" class="current">SF: Badges</a></li>
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
									<h3>Engineer Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261105_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 11 kills while Engineer</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261105_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 22 kills while Engineer</li>
										<li>16h while playing Engineer</li>
										<li>Basic Engineer Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261105_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 44 kills while Engineer</li>
										<li>100h while playing Engineer</li>
										<li>Veteran Engineer Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Sniper Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261109_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 11 kills while Sniper</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261109_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 17 kills while Sniper</li>
										<li>16h while playing Sniper</li>
										<li>Basic Sniper Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261109_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 38 kills while Sniper</li>
										<li>100h while playing Sniper</li>
										<li>Veteran Sniper Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Medic Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261113_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 11 kills while Medic</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261113_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 22 kills while Medic</li>
										<li>16h while playing Medic</li>
										<li>Basic Medic Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261113_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 44 kills while Medic</li>
										<li>100h while playing Medic</li>
										<li>Veteran Medic Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Spec Ops Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261115_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 6 kills while Spec Ops</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261115_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 22 kills while Spec Ops</li>
										<li>16h while playing Special-Ops</li>
										<li>Basic Spec Ops Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261115_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 44 kills while Spec Ops</li>
										<li>100h while playing Special-Ops</li>
										<li>Veteran Spec Ops Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Assault Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261119_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 6 kills while Assault</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261119_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 22 kills while Assault</li>
										<li>16h while playing Assault</li>
										<li>Basic Assault Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261119_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 44 kills while Assault</li>
										<li>100h while playing Assault</li>
										<li>Veteran Assault Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Anti-Tank Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261120_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 11 kills while Anti-Tank</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261120_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 22 kills while Anti-Tank</li>
										<li>16h while playing Anti-tank</li>
										<li>Basic Anti-Tank Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261120_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 44 kills while Anti-Tank</li>
										<li>100h while playing Anti-tank</li>
										<li>Veteran Anti-Tank Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Support Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261121_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 11 kills while Support</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261121_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 22 kills while Support</li>
										<li>16h while playing Support</li>
										<li>Basic Support Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1261121_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: 44 kills while Support</li>
										<li>100h while playing Support</li>
										<li>Veteran Support Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Tactical Support Weaponry Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1260602_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: Deploy 10 Flashbangs/Gas Grenades</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1260602_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>100 usage with the Flash/Gas/Smoke</li>
										<li>Basic Tactical Support Weaponry Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1260602_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>500 usage with the Flash/Gas/Smoke</li>
										<li>Veteran Tactical Support Weaponry Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Grappling Hook Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1260708_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: Deploy 10 Grapples</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1260708_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>100 usage with the Grappling Hook</li>
										<li>Basic Grappling Hook Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1260708_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>500 usage with the Grappling Hook</li>
										<li>Veteran Grappling Hook Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
							</tr>
						</table>

						
						<table cellpadding="0" cellspacing="0" border="0" class="basic-stat">
							<tr>
								<th colspan="3">
									<h3>Zip Line Specialist Badge</h3>
								</th>
							</tr>
							<tr valign="top">
								<th>Basic</th>
								<th>Veteran</th>
								<th>Expert</th>
							</tr>
							<tr valign="top">
								<td width="26%">
									<img src="'.$ROOT.'game-images/awards/ubar/1262612_1.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>IAR: Deploy 10 Zip Lines</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1262612_2.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>100 usage with the Zip Line</li>
										<li>Basic Zip Line Specialist Badge</li>
									</ul>
								</td>
								<td width="32%">
									<img src="'.$ROOT.'game-images/awards/ubar/1262612_3.png" width="96" height="96" alt="" class="badge" />
									<ul>
										<li>500 usage with the Zip Line</li>
										<li>Veteran Zip Line Specialist Badge</li>
									</ul>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="3"></td>
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