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
</div>
</body>
</html>';
?>