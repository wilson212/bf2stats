<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>Search Players, '. $TITLE .'</title>

	<link rel="icon" href="'.$ROOT.'favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="'.$ROOT.'favicon.ico" type="image/x-icon">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/two-tiers.css">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/nt.css">
	<link rel="stylesheet" type="text/css" media="print" href="'.$ROOT.'css/print.css">
	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/default.css">

	<script type="text/javascript">/* no frames */ if(top.location != self.location) top.location.replace(self.location);</script>
	<script type="text/javascript" src="'.$ROOT.'nt2.js"></script>
</head>

<body class="inner">
<div id="page-1">
	<div id="page-2">
	
	<h1 id="page-title">Search Stats </h1>

	<div id="page-3">
	
		<div id="content"><div id="content-id">
		
			<!--
			<ul id="stats-nav">
				<li><a href="'.$ROOT.'">Home</a></li>
				<li class="current"><a href="'.$ROOT.'?go=search">Search Stats</a></li>
				<li><a href="'.$ROOT.'?go=currentranking">Current Ranking</a></li>
				<li><a href="'.$ROOT.'?go=my-leaderboard">My Leaderboard</a></li>
			</ul>
			-->
			<div id="mlb-instructions">
				<p>
					Here you can search for Player Stats using their Player ID [PID] or their Name. If you are not sure about the full Name of a player, 
					just use % and _ as wildcards.<br /><br />% stands for none, one or any number of characters.<br />_ stands for exactly one character
					<br /><br /
					>E.g.: %Elvis% would return TheKingIsElvis, ElvisHasLeftTheBuilding, IsElvisAlive, ElvisNo1 etc...
					<br />Number_Rules would return Number1Rules, NumberZRules etc...
				</p>


				<form action="?go=search" method="post">
					<label>Search Player by ID\'s or Nick: <br>
						<input name="searchvalue" size="80" value="';
							if ($SEARCHVALUE) {
                                $template .= $SEARCHVALUE;
                            }
							$template .= '" type="text">
					</label> 
					<input name="search" value="Search" type="submit">
				</form>
			</div>';
if ($SEARCHVALUE)
{
	$template .= '
		<!-- RESULTS START HERE -->
			<table id="searchresults" class="stat sortable" border="0" cellpadding="0" cellspacing="0">
				<tbody>
					<tr>
						<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Search Results <span class="sortarrow"></span></a></th> 
						<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Score<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">SPM<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">K:D<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Time Played<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
						<th class="nosort">Last Update</th>
						<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">PID<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
					</tr>
					<tr>';
				$total = count($searchresults);
				for ($i=0; $i < $total; $i++)
				{
					$template .= '
						<td>
							<img src="'.$ROOT.'game-images/ranks/icon/rank_'.$searchresults[$i]['rank'].'.gif" alt="" style="border: 0pt none ;"> 
							<a href="'.$ROOT.'?pid='.$searchresults[$i]['id'].'"> '.$searchresults[$i]['name'].'</a>&nbsp;
							<img src="'.$ROOT.'game-images/flags/'.strtoupper((string) $searchresults[$i]['country']).'.png" height="12" width = "16">
						</td>
						<td>'.$searchresults[$i]['score'].'</td>
						<td>'.$searchresults[$i]['spm'].'</td>
						<td>';
							if ($searchresults[$i]['kdr']) {
                                $template .= $searchresults[$i]['kdr'];
                            } else {
                                $template .= 'N/A';
                            }
							$template .= '
						</td>
						<td title="'.$searchresults[$i]['time'].'">'.intToTime($searchresults[$i]['time']).'</td>
						<td>';
							if (getLastUpdate(getcwd().'/cache/'.$searchresults[$i]['id'].'.cache')<0) {
                                $template .= 'N/A';
                            } else {
                                $template .= intToTime(getLastUpdate(getcwd().'/cache/'.$searchresults[$i]['id'].'.cache'));
                            }
						  $template .= '								
						</td>
						<td>'.$searchresults[$i]['id'].'</td>
					</tr>';
				}
				$template .= '
				</tbody>
			</table>
		<!-- END OF RESULT TABLE -->';
}
$template .= '
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
?>