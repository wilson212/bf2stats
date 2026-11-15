<?php
$template = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="inner">
<head>
	<title>' . $player['name'] .' '. getRankByID($player['rank']) . ' Stats, '. $TITLE .'</title>

	<link rel="icon" href="' . $ROOT . 'favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="' . $ROOT . 'favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" media="screen" href="' . $ROOT . 'css/two-tiers.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="' . $ROOT . 'css/nt.css" />
	<link rel="stylesheet" type="text/css" media="print" href="' . $ROOT . 'css/print.css" />

	<script type="text/javascript">/* no frames */ if(top.location != self.location) top.location.replace(self.location);</script>
	<script type="text/javascript" src="' . $ROOT . 'js/nt2.js"></script>

	<link rel="stylesheet" type="text/css" media="screen" href="'.$ROOT.'css/default.css">
	<!--<script src="' . $ROOT . 'js/show.js" type="text/javascript"></script>-->
</head>
<body class="inner">

<div id="page-1">
	<div id="page-2">
	
		<h1 id="page-title">
			<img src="' . $ROOT . 'game-images/ranks/header/rank_' . $player['rank'] . '.png" alt="" />' . $player['name'] . '<small> ' . getRankByID($player['rank']) . '</small>
		</h1>

		<div id="page-3">
	
			<div id="content">
				<div id="content-id"><!-- template header end == begin content below -->
	
			<!--
			<ul id="stats-nav">
				<li class="current"><a href="' . "$ROOT?pid=$PID" . '">Stats</a></li>
				<li><a href="' . $ROOT.'?go=currentranking' . '">Current Ranking</a></li>
				<li><a href="' . $ROOT.'?go=my-leaderboard' . '">My Leaderboard</a></li>
			</ul>
			-->
			<div id="prefCol">
				<div id="prefers">
					<img class="solider" src="';
					if (file_exists(getcwd() . '/game-images/soldiers/' . $playerFavorite['army'] . '_' . $playerFavorite['kit'] . '_' . $playerFavorite['weapon'] . '.jpg')) {
                        $template .= $ROOT . 'game-images/soldiers/' . $playerFavorite['army'] . '_' . $playerFavorite['kit'] . '_' . $playerFavorite['weapon'] . '.jpg';
                    } else {
                        $template .= $ROOT . 'game-images/soldiers/' . $playerFavorite['army'] . '_' . $playerFavorite['kit'] . '_5.jpg';
                    } // show pistol...
					$template .= '" alt="' . $player['name'] . ' - ' . getArmyByID($playerFavorite['army']) . '" />
					<img class="weapon" src="' . $ROOT . 'game-images/weapons/weapon_' . $playerFavorite['weapon'] . '.jpg" alt="' . $weapons[$playerFavorite['weapon']]['name'] . '" />
					<img class="vehicle" src="' . $ROOT . 'game-images/vehicles/vehicles_' . $playerFavorite['vehicle'] . '.jpg" alt="' . getVehicleByID($playerFavorite['vehicle']) . '" />
					<img class="kit" src="' . $ROOT . 'game-images/kits/kit_' . $playerFavorite['kit'] . '.jpg" alt="' . getKitByID($playerFavorite['kit']) . '" />
					<img class="map" src="' . $ROOT . 'game-images/maps/map_' . $playerFavorite['map'] . '.jpg" alt="' . getMapByID( $playerFavorite['map'] ) . '" />
					<img id="flag" src="' . $ROOT . 'game-images/flags/' . strtoupper((string) $player['country']) . '.png" alt="' . getCountryByCode($player['country']) . '" width="32" height="24" />
				</div>
				<a id="add-to-mlb" href="'.$ROOT.'/?go=my-leaderboard&add=' . $PID . '"><img src="' . $ROOT . '/site-images/user_add.png" alt="Add to My Leader Board" /></a>
			</div>
			
			<table border="0" cellpadding="0" cellspacing="0" id="profile">
				<tr valign="top">
					<th>Player Profile</th>
					<th>Team Profile</th>
					<th>Combat Profile</th>
				</tr>
				<tr valign="top">
					<td>
						<table border="0" cellpadding="0" cellspacing="0" id="player">
							<tr>
								<th colspan="2">Scores</th>
							</tr>
							<tr>
								<td>Global</td>
								<td>' . @number_format($player['score']) . '</td>
							</tr>

							<tr>
								<td>Team</td>
								<td>' . @number_format($player['teamscore']) . '</td>
							</tr>
							<tr>
								<td>Combat</td>
								<td>' . @number_format($player['skillscore']) . '</td>
							</tr>
							<tr>
								<td>Commander</td>
								<td>' . @number_format($player['cmdscore']) . '</td>
							</tr>
							<tr>
								<th colspan="2">Time</th>
							</tr>
							<tr>
								<td nowrap="nowrap">Parachute</td>
								<td nowrap="nowrap">' . intToTime($vehicles[0]['timepara']) . '</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Commander</td>
								<td nowrap="nowrap">' . intToTime($player['cmdtime']) . '</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Squad Leader </td>
								<td nowrap="nowrap">' . intToTime($player['sqltime']) . '</td>
							</tr>
							<tr>

								<td nowrap="nowrap">Squad Member </td>
								<td nowrap="nowrap">' . intToTime($player['sqmtime']) . '</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Lone Wolf </td>
								<td nowrap="nowrap">' . intToTime($player['lwtime']) . '</td>
							</tr>
							<tr>
								<td>Total</td>
								<td nowrap="nowrap">' . intToTime($player['time']) . '</td>
							</tr>
							
						</table>
					</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0" id="team">	
							<tr>
								<td nowrap="nowrap">Wins &amp; Losses</td>
								<td nowrap="nowrap">';
									$template .= @number_format($player['wins']) ."&nbsp;/&nbsp;" . @number_format($player['losses']).'&nbsp;(';
									if ($player['losses']) {
                                        $template .= round($player['wins'] / $player['losses'], 2);
                                    } else {
                                        $template .= $player['wins'];
                                    }
									$template .= ')
								</td>
							</tr>
							<tr>
								<th colspan="2">Capture Points</th>
							</tr>

							<tr>
								<td nowrap="nowrap">Captured CP </td>
								<td>' . @number_format($player['captures']) . '</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Capture Assist </td>
								<td>' . @number_format($player['captureassists']) . '</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Defended CP </td>
								<td>' . @number_format($player['defends']) . '</td>
							</tr>
							<tr>
								<th colspan="2">Teamwork</th>
							</tr>
							<tr>
								<td nowrap="nowrap">Kill Assist </td>
								<td>' . ($player['damageassists']+$player['passengerassists']+$player['targetassists']) . '</td>
							</tr>
							<tr>
								<td>Heal</td>
								<td>' . @number_format($player['heals']) . '</td>
							</tr>
							<tr>
								<td>Revive</td>
								<td>' . @number_format($player['revives']) . '</td>
							</tr>
							<tr>
								<td>Support</td>
								<td>' . @number_format($player['ammos']) . '</td>
							</tr>
							<tr>
								<td>Repair</td>
								<td>' . @number_format($player['repairs']) . '</td>
							</tr>
							<tr>
								<td>Driver</td>
								<td>' . @number_format($player['driverspecials'] + $player['driverassists']) . '</td>
							</tr>
						</table>
					</td>
					<td>
						<table border="0" cellpadding="0" cellspacing="0" id="combat">
							<tr>
								<td>Accuracy</td>
								<td>' .  round($weaponSummary['average']['acc'],3) . '%</td>
							</tr>
							<tr>
								<td><acronym title="Score per minute">SPM</acronym></td>
								<td>';
									if (intToMins($player['time'])) {
                                        $template .= round(($player['score']/intToMins($player['time'])),4);
                                    } else {
                                        $template .= $player['score'];
                                    }
									$template .= '
								</td>
							</tr>
							<tr>
								<td>Suicides </td>
								<td>' . $player['suicides'] . '</td>
							</tr>
							<tr>
								<td nowrap="nowrap">K/D Ratio</td>
								<td>';
									if ($player['deaths']) {
                                        $template .= round($player['kills']/$player['deaths'],3);
                                    } else {
                                        $template .= $player['kills'];
                                    }
										
									$template .= '
								</td>
							</tr>

							<tr>
								<th colspan="2">Kills</th>
							</tr>
							<tr>
								<td nowrap="nowrap">Total &amp; Streak</td>
								<td>' . @number_format($player['kills']) . ' / ' . $player['killstreak'] . '</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Per Minute </td>
								<td>';
									if (intToMins($player['time'])) {
                                        $template .= round($player['kills'] / round(intToMins($player['time']),0), 3);
                                    } else {
                                        $template .= @number_format($player['kills']);
                                    }
									$template .= '
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap">Per Round </td>
								<td>';
									if ($player['rounds']) {
                                        $template .= round($player['kills'] / $player['rounds'], 3);
                                    } else {
                                        $template .= round($player['kills']);
                                    }
									$template .= '
								</td>
							</tr>
							<tr>
								<th colspan="2">Deaths</th>
							</tr>
							<tr>
								<td>Total &amp; Streak</td>
								<td>';
									if ($player['deathstreak']) {
                                        $template .= @number_format($player['deaths']) . ' / ' . $player['deathstreak'];
                                    } else {
                                        $template .= @number_format($player['deaths']);
                                    }
									$template .= '
								</td>
							</tr>
							<tr>
								<td>Per Minute </td>
								<td>';
									if ($player['time']) {
                                        $template .= round($player['deaths'] / round(intToMins($player['time']),0), 3);
                                    } else {
                                        $template .= $player['deaths'];
                                    }
									$template .=	'
								</td>
							</tr>
							<tr>
								<td>Per Round </td>
								<td>';
									if ($player['rounds']) {
                                        $template .= round($player['deaths'] / $player['rounds'], 3);
                                    } else {
                                        $template .= round($player['deaths']);
                                    }
									$template .= '
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>

			<hr class="clear" />
			<em style="font-size: 9px; text-align: left; display: block;clear: both;padding-left: 8px;">B.R. = Best Round</em>
			<div id="stats">
				<div class="col">
					<table border="0" cellspacing="0" cellpadding="0" id="army" class="stat sortable">
						<tr>
							<th>Army</th>
							<th>Time</th>
							<th>Wins</th>
							<th>Losses</th>
							<th>Ratio</th>
							<th>B.R.</th>
						</tr>';
				
						$count = getArmyCount();
						for ($i=0; $i < $count; $i++)
						{
							$fav = $playerFavorite['army'];
							$template .= ($fav == $i) ? '<tr class="favorite">' : '<tr>';
							$template .= '

								<td id="army-'.$i.'">'.getArmyByID($i).'</td>
								<td nowrap="nowrap" title="'.$armies[0]['time'.$i].'">'.intToTime($armies[0]['time'.$i]).'</td>
								<td>'. @number_format($armies[0]['win'.$i]) .'</td>
					
								<td>'. @number_format($armies[0]['loss'.$i]) .'</td>
								<td>';
							if ($armies[0]['loss'.$i]) {
                                $template .= round($armies[0]['win'.$i] / $armies[0]['loss'.$i],2);
                            } else {
                                $template .= $armies[0]['win'.$i];
                            }
							$template .= '</td>
									<td>'.$armies[0]["best$i"].'</td>
								</tr>
							';
						}

						$template .= '		
						<tr class="totals sortbottom">			<td>Total</td>
							<td nowrap="nowrap">' . intToTime($armySummary['total']['time']) . '</td>
							<td>' . @number_format($armySummary['total']['win']) . '</td>
							<td>' . @number_format($armySummary['total']['loss']) . '</td>
							<td> - </td>

							<td> - </td>
						</tr>
						<tr class="averages sortbottom">
							<td>Averages</td>
							<td nowrap="nowrap">' . intToTime($armySummary['average']['time']) . '</td>
							<td>' . round($armySummary['average']['win'],0) . '</td>
							<td>' . round($armySummary['average']['loss'],0) . '</td>
							<td>';
								if ($armySummary['average']['loss']) {
                                    $template .= round($armySummary['average']['win']/$armySummary['total']['loss'], 0);
                                } else {
                                    $template .= round($armySummary['average']['win'],0);
                                }
								$template .= '
							</td>
							<td>' . round($armySummary['average']['best'],0) . '</td>
						</tr>
					</table>
			
					<table border="0" cellspacing="0" cellpadding="0" id="map" class="stat sortable">
						<tr>
							<th>Map</th>

							<th>Time</th>
							<th>Wins</th>
							<th>Losses</th>
							<th>Ratio</th>
							<th>B.R.</th>
						</tr>';

						$count = count($maps);
						for ($i=0; $i < $count; $i++)
						{
							$template .= ($playerFavorite['map'] == $maps[$i]['mapid']) ? '<tr class="favorite">' : '<tr>';
							$template .= '
								<td id="map-'.$maps[$i]['mapid'].'">'.getMapByID($maps[$i]['mapid']).'</td>
								<td nowrap="nowrap" title="'.$maps[$i]['time'].'">'.intToTime($maps[$i]['time']).'</td>
								<td>'. @number_format($maps[$i]['win']) .'</td>
								<td>'. @number_format($maps[$i]['loss']) .'</td>
								<td>';
								if ($maps[$i]['loss']) {
                                    $template .= round($maps[$i]['win']/$maps[$i]['loss'],2);
                                } else {
                                    $template .= $maps[$i]['win'];
                                }
								$template .= '
								</td>
								<td>'.$maps[$i]['best'].'</td>
							</tr>';
						}

						$template .= '
						<tr class="totals sortbottom">			<td>Total</td>
							<td nowrap="nowrap">' . intToTime($mapSummary['total']['time']) . '</td>
							<td>' . @number_format($mapSummary['total']['win']) . '</td>
							<td>' . @number_format($mapSummary['total']['loss']) . '</td>
							<td> &ndash; </td>
							<td> &ndash; </td>
						</tr>
						<tr class="averages sortbottom">			<td>Averages</td>
							<td nowrap="nowrap">' . intToTime($mapSummary['average']['time']) . '</td>
							<td>' . @number_format($mapSummary['average']['win']) . '</td>
							<td>' . @number_format($mapSummary['average']['loss']) . '</td>
							<td>' . $mapSummary['average']['ratio'] . '</td>
							<td>' . @number_format($mapSummary['average']['br']) . '</td>
						</tr>
					</table>

					<table id="theater" class="stat sortable" border="0" cellpadding="0" cellspacing="0">
						<tbody>
							<tr>
								<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Theater<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
								<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Time<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
								<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Wins<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
								<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Losses<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>

								<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">Ratio<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
								<th><a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">B.R.<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a></th>
							</tr>';
							
							#add <tr class="favorite">
							
							$count = getArmyCount();
							for ($i=0; $i < $count; $i++)
							{
								$fav = $playerFavorite['theatre'];
								$template .= ($fav == $TheaterData[$i]['name']) ? '<tr class="favorite">' : '<tr>';
								$template .= '
									<td id="theater-'.$i.'">'.$TheaterData[$i]['name'].'</td>
									<td title="'.$TheaterData[$i]['time'].'" nowrap="nowrap">'.intToTime($TheaterData[$i]['time']).'</td>
									<td>'. @number_format($TheaterData[$i]['wins']) .'</td>
									<td>'. @number_format($TheaterData[$i]['losses']).'</td>
									<td>'. @round(getRatio($TheaterData[$i]['wins'], $TheaterData[$i]['losses']),2).'</td>
									<td>'. $TheaterData[$i]['br']. '</td>
								</tr>';
							}
							
							$template .= '
						</tbody>
					</table>

					<table border="0" cellspacing="0" cellpadding="0" id="vehicle" class="stat sortable">
						<tr>
							<th>Vehicle</th>
							<th>Time</th>
							<th>Kills</th>
							<th>Deaths</th>
							<th>Ratio</th>
							<th>Road Kills</th>
						</tr>';
					
						$count = getVehicleCount();
						for ($i=0; $i < $count; $i++)
						{
							$vehicleTotalKills = $vehicles[0]['kills'.$i] ? 100 * round($vehicles[0]['kills'.$i] / $player['kills'], 2) : 0;
							
							$fav = $playerFavorite['vehicle'];
							$template .= ($fav == $i) ? '<tr class="favorite">' : '<tr>';
							$template .= '
								<td id="vehicle-'.$i.'">'.getVehicleByID($i).'</td>
								<td nowrap="nowrap" title="'.$vehicles[0]['time'.$i].'">'.intToTime($vehicles[0]['time'.$i]).'</td>
								<td><span class="abbr" alt="Accounts for '.$vehicleTotalKills.'% of all kills">'. @number_format($vehicles[0]['kills'.$i]) .'</span></td>
								<td>'. @number_format($vehicles[0]['deaths'.$i]) .'</td>
								<td>';
								if ($vehicles[0]['deaths'.$i]) {
                                    $template .= round(($vehicles[0]['kills'.$i] + $vehicles[0]['rk'.$i]) / $vehicles[0]['deaths'.$i], 2);
                                } else {
                                    $template .= @number_format($vehicles[0]['kills'.$i]);
                                }
								$template .= '</td>
								<td>'.$vehicles[0]['rk'.$i].'</td>
							</tr>
							';
						}
						
						$template .= '
						<tr class="averages sortbottom">			<td>Total</td>
							<td nowrap="nowrap">' . intToTime($vehicleSummary['total']['time']) . '</td>
							<td>
								<span class="abbr" alt="Accounts for ';
									if ($vehicleSummary['total']['kills']) {
                                        $template .= (100 * round($vehicleSummary['total']['kills'] / $player['kills'], 2));
                                    } else {
                                        $template .= 0;
                                    }
									$template .= '
									% of all kills">' . @number_format($vehicleSummary['total']['kills']) . '
								</span>
							</td>
							<td>' . @number_format($vehicleSummary['total']['deaths']) . '</td>
							<td> &ndash; </td>
							<td>' . $vehicleSummary['total']['rk'] . '</td>
						</tr>
						<tr class="totals sortbottom">			
							<td>Averages</td>
							<td nowrap="nowrap">' . intToTime($vehicleSummary['average']['time']) . '</td>
							<td>' . @number_format(round($vehicleSummary['average']['kills'])) . '</td>
							<td>' . @number_format(round($vehicleSummary['average']['deaths'])) . '</td>
							<td>' . $vehicleSummary['average']['ratio'] . '</td>
							<td>' . round($vehicleSummary['average']['rk'],0) . '</td>
						</tr>
					</table>
				</div><!-- END COL LEFT -->
			
				<!-- COL RIGHT -->
				<div class="col">
					<table border="0" cellspacing="0" cellpadding="0" id="expansion" class="stat">
						<tr>
							<th>Expansion</th>
							<th>BF2</th>
							<th>SF</th>
							<th>EF</th>
							<th>AF</th>
						</tr>
						<tr>
							<td>Time</td>
							<td nowrap="nowrap" title="'.getExpasionTimeByName($PID, 'bf').'">'.intToTime(getExpasionTimeByName($PID, 'bf')).'</td>
							<td nowrap="nowrap" title="'.getExpasionTimeByName($PID, 'sf').'">'.intToTime(getExpasionTimeByName($PID, 'sf')).'</td>
							<td nowrap="nowrap" title="'.getExpasionTimeByName($PID, 'ef').'">'.intToTime(getExpasionTimeByName($PID, 'ef')).'</td>
							<td nowrap="nowrap" title="'.getExpasionTimeByName($PID, 'af').'">'.intToTime(getExpasionTimeByName($PID, 'af')).'</td>
						</tr>
					</table>

					<table border="0" cellspacing="0" cellpadding="0" id="kit" class="stat sortable">
						<tr>
							<th>Kit</th>
							<th>Time</th>
							<th>Kills</th>
							<th>Deaths</th>
							<th>Ratio</th>
						</tr>';

						$count = getKitCount();
						for ($i=0; $i < $count; $i++)
						{
							$fav = $playerFavorite['kit'];
							
							$template .= ($fav == $i) ? '<tr class="favorite">' : '<tr>';
							# <tr class="favorite"> first line!
							$template .= '

								<td id="kit-'.$i.'">'.getKitByID($i).'</td>

								<td nowrap="nowrap" title="'.$kits[0]['time'.$i].'">'.intToTime($kits[0]['time'.$i]).'</td>
								<td><span class="abbr" alt="Accounts for ';
								if ($player['kills'])
								{
									$template .= (100 * round($kits[0]['kills'.$i] / $player['kills'],2));
								}
								else {
                                    $template .= 0;
                                }
								$template .= '% of all kills">'. @number_format($kits[0]['kills'.$i]) .'</span></td>
								<td>'. @number_format($kits[0]['deaths'.$i]) .'</td>
								<td>';
								if ($kits[0]['deaths'.$i]) {
                                    $template .= round($kits[0]['kills'.$i] / $kits[0]['deaths'.$i], 2);
                                } else {
                                    $template .= $kits[0]['kills'.$i];
                                }
								$template .= '</td>
							</tr>';
						}

						$template .= '
						<tr class="averages sortbottom">
							<td>Total</td>
							<td nowrap="nowrap">' . intToTime($kitSummary['total']['time']) . '</td>
							<td><span class="abbr" alt="Accounts for ' . round(100 * $kitSummary['total']['totalkills'], 2) . '% of all kills">' . @number_format($kitSummary['total']['kills']) . '</span></td>
							<td>' . @number_format($kitSummary['total']['deaths']) . '</td>
							<td> &ndash; </td>
						</tr>
						<tr class="totals sortbottom">
							<td>Averages</td>
							<td nowrap="nowrap">' . intToTime($kitSummary['average']['time']) . '</td>
							<td>' . @number_format(round($kitSummary['average']['kills'],0)) . '</td>
							<td>' . @number_format(round($kitSummary['average']['deaths'],0)) . '</td>
							<td>' . round($kitSummary['average']['ratio'],2) . '</td>
						</tr>
					</table>
			
					<table border="0" cellspacing="0" cellpadding="0" id="weapon" class="stat sortable">
						<tr>
							<th>Weapon</th>
							<th>Time</th>
							<th>Kills</th>
							<th>Deaths</th>
							<th>Ratio</th>
							<th>Acc</th>
						</tr>';
						
						// add  class="favorite" later
						for ($i=0; $i <= 10; $i++)
						{
							$fav = $playerFavorite['weapon'];
							$template .= ($fav == $i) ? '<tr class="favorite">' : '<tr>';
							$template .= '
								<td id="weapon-'.$i.'">'.$weapons[$i]['name'].'</td>
								<td nowrap="nowrap" title="'.$weapons[$i]['time'].'">'.intToTime($weapons[$i]['time']).'</td>
								<td><span class="abbr" alt="Accounts for '.round($weapons[$i]['totalkills'], 2).'% of all kills">'. @number_format($weapons[$i]['kills']) .'</span></td>
								<td>'. @number_format($weapons[$i]['deaths']) .'</td>
					
								<td>';
								if ($weapons[$i]['deaths']) {
                                    $template .=  round($weapons[$i]['kills'] / $weapons[$i]['deaths'],2);
                                } else {
                                    $template .=  $weapons[$i]['kills'];
                                }
								#calc shot hit Ratio
								if ($weapons[$i]['hit'])
								{
									$ratio = round(100 * $weapons[$i]['hit'] / $weapons[$i]['fired'],2);
									if ($ratio == 1) {
                                        $ratio = 100;
                                    }
								}
								else {
                                    $ratio = 0;
                                }
								$template .=  '</td>
								<td title="'.$ratio.'"><span class="abbr" alt="Shots: '. @number_format($weapons[$i]['fired']) .', Hits: '. @number_format($weapons[$i]['hit']) .'">'.round($ratio, 2).'%</span></td>
							</tr>';	
						}		
						$template .= '
						<tr>
							<td id="weapon-11">Explosives (C4, Claymore, AT Mine)</td>
							<td nowrap="nowrap" title="' . ($weapons[11]['time'] + $weapons[13]['time'] + $weapons[14]['time']) . '">' . intToTime($weapons[11]['time'] + $weapons[13]['time'] + $weapons[14]['time']) . '</td>';
						
							if ($weapons[11]['kills'] + $weapons[13]['kills'] + $weapons[14]['kills']) {
                                $ratio = round((100 * $weapons[11]['kills'] + $weapons[13]['kills'] + $weapons[14]['kills']) / $player['kills'],2);
                            } else {
                                $ratio = 0;
                            }
								
							$template .= '<td><span class="abbr" alt="Accounts for ';
							$template .= round($ratio,2);

							$template .=  '
							% of all kills">' . @number_format($weapons[11]['kills'] + $weapons[13]['kills'] + $weapons[14]['kills']) . '</span></td>

							<td>' . @number_format($weapons[11]['deaths'] + $weapons[13]['deaths'] + $weapons[14]['deaths']) . '</td>';
							
								// kd ratio
								if ($weapons[11]['deaths' ]+ $weapons[13]['deaths'] + $weapons[14]['deaths']) {
                                    $ratio = ($weapons[11]['kills'] + $weapons[13]['kills'] + $weapons[14]['kills']) / ($weapons[11]['deaths'] + $weapons[13]['deaths'] + $weapons[14]['deaths']);
                                } else {
                                    $ratio =  $weapons[11]['kills'] + $weapons[13]['kills'] + $weapons[14]['kills'];
                                }
									
							$template .=   
							'<td>'.round($ratio,2);
								//kd fired/hit ratio
								if ($weapons[11]['hit'] + $weapons[13]['hit'] + $weapons[14]['hit']) {
                                    $ratio = ($weapons[11]['fired'] + $weapons[13]['fired'] + $weapons[14]['fired']) / ($weapons[11]['hit'] + $weapons[13]['hit'] + $weapons[14]['hit']);
                                } else {
                                    $ratio =  0;
                                }			
								$template .= '
							</td>
							<td title="' . $ratio . '"><span class="abbr" alt="Shots: ' . @number_format($weapons[11]['fired']+$weapons[13]['fired']+$weapons[14]['fired']) . ', Hits: ' . 
								@number_format($weapons[11]['hit']+$weapons[13]['hit']+$weapons[14]['hit']) . '">' . round($ratio,2) . '%</span></td>
						</tr>
						<tr>
							<td id="weapon-12">' . $weapons[12]['name'] . '</td>
							<td nowrap="nowrap" title="' . $weapons[12]['time'] . '">' . intToTime($weapons[12]['time']) . '</td>
							<td><span class="abbr" alt="Accounts for ' . $weapons[12]['totalkills'] . '% of all kills">' . @number_format($weapons[12]['kills']) . '</span></td>
							<td>' . @number_format($weapons[12]['deaths']) . '</td>
							<td>';
								if ($weapons[12]['deaths']) {
                                    $template .= round($weapons[12]['kills']/$weapons[12]['deaths'],2);
                                } else {
                                    $template .= $weapons[12]['kills'];
                                }

								$template .= '</td>';

								$ratio = $weapons[12]['hit'] ? round(100*$weapons[12]['hit']/$weapons[12]['fired'],2) : $weapons[12]['fired'];
							$template .= '
							<td title="' . $ratio . '"><span class="abbr" alt="Shots: ' . @number_format($weapons[12]['fired']) . ', Hits: ' . @number_format($weapons[12]['hit']) . '">' . round($ratio,2) . '%</span></td>
						</tr>
						<tr class="totals sortbottom">
							<td>Total</td>
							<td nowrap="nowrap">' . intToTime($weaponSummary['total']['time']) . '</td>
							<td><span class="abbr" alt="Accounts for ' . round($weaponSummary['total']['totalkills'], 2) . '% of all kills">' . @number_format($weaponSummary['total']['kills']) . '</span></td>
							<td>' . @number_format($weaponSummary['total']['deaths']) . '</td>
							<td> &ndash; </td>
							<td> &ndash; </td>

						</tr>
						<tr class="averages sortbottom">
							<td>Averages</td>
							<td nowrap="nowrap">' . intToTime($weaponSummary['average']['time']) . '</td>
							<td>' . @number_format(round($weaponSummary['average']['kills'],0)) . '</td>
							<td>' . @number_format(round($weaponSummary['average']['deaths'],0)) . '</td>
							<td>' . round($weaponSummary['average']['ratio'],0) . '</td>

							<td><span class="abbr" alt="Shots: ' . 
								@number_format(round($weaponSummary['average']['fired'],0)) . ', Hits: ' . 
								@number_format(round($weaponSummary['average']['hit'],0)) . 
								'">' . round($weaponSummary['average']['acc'],3) . '%</span></td>
						</tr>
					</table>
			
					<table border="0" cellspacing="0" cellpadding="0" id="equipment" class="stat sortable">
						<tr>
							<th>Equipment</th>
							<th>Time</th>
							<th>Kills</th>
							<th>Deaths</th>
							<th>Ratio</th>
							<th>Usage</th>
						</tr>';
						#<!-- add  class="favorite" -->

						for ($i=9; $i<=17; $i++)
						{
							$fav = $playerFavorite['equipment'];
							$template .= ($fav == $i) ? '<tr class="favorite">' : '<tr>';
							$template .= '
									<td>'.$weapons[$i]['name'].'</td>
									<td nowrap="nowrap" title="'. $weapons[$i]['time'] .'">'. intToTime($weapons[$i]['time']) .'</td>
									<td><span class="abbr" alt="Accounts for '. round($weapons[$i]['totalkills'], 2) .'% of all kills">'. @number_format($weapons[$i]['kills']) .'</span></td>
									<td>'. @number_format($weapons[$i]['deaths']) .'</td>
									<td>';
							if ($weapons[$i]['deaths']) {
                                $template .= round($weapons[$i]['kills']/$weapons[$i]['deaths'],2);
                            } else {
                                $template .= $weapons[$i]['kills'];
                            }	
							$template .= '
									</td>
									<td>'. @number_format($weapons[$i]['fired']) .'</td>
								</tr>';
						}
						$template .= '
					
						<tr class="totals sortbottom">			<td>Total</td>
							<td nowrap="nowrap">' . intToTime($equipmentSummary['total']['time']) . '</td>
							<td>
								<span class="abbr" alt="Accounts for ' . round($equipmentSummary['total']['totalkills'], 2) . '% of all kills">
									' . @number_format($equipmentSummary['total']['kills']) . '
								</span>
							</td>
							<td>' . @number_format($equipmentSummary['total']['deaths']) . '</td>
							<td>' . round($equipmentSummary['total']['ratio'],2) . '</td>
							<td>' . @number_format(round($equipmentSummary['total']['fired'],0)) . '</td>
						</tr>
						<tr class="averages sortbottom">
							<td>Averages</td>
							<td nowrap="nowrap">' . intToTime($equipmentSummary['average']['time']) . '</td>
							<td>' . @number_format(round($equipmentSummary['average']['kills'],0)) . '</td>
							<td>' . @number_format(round($equipmentSummary['average']['deaths'],0)) . '</td>
							<td>' . round($equipmentSummary['average']['ratio'],2) . '</td>
							<td>' . @number_format(round($equipmentSummary['average']['fired'],0)) . '</td>
						</tr>
					</table>
			
					<table border="0" cellspacing="0" cellpadding="0" id="fov" class="stat">
						<tr>
							<th colspan="2">Favorite Victims &amp; Worst Enemies</th>
						</tr>
						<tr>
							<td>Favorite Victim<br />(Kills to) </td>';

								if ($victims && trim((string) $victims[0]['victim']) !== '') {
                                    $template .= '<td nowrap="nowrap"><acronym title="his rank is '.getRankByID(getRankFromPID($victims[0]['victim'])).'"><img src="'.$ROOT.'game-images/ranks/icon/rank_'.getRankFromPID($victims[0]['victim']).'.gif"> <a rel="nofollow" href="?pid=' . $victims[0]['victim'] . '">' . getNickFromPID($victims[0]['victim']) . '</a></acronym> (' . $victims[0]['count'] . ')</td>';
                                } else {
                                    $template .= '<td>You are no one\'s worst enemy. Go bully someone.</td>';
                                }
							$template .= '
						</tr>
						<tr>
							<td nowrap="nowrap">More Victims<br />(Top 10)</td>
							<td>';

								$limit = count($victims) < 10 ? count($victims)-1 : 9;
								$written = 0;
								for ($i=1; $i<=$limit; $i++)
								{
									if (trim((string) $victims[$i]['victim']) !== '' && $victims[$i]['count'] > 1) // minimum 2 kills
									{
										if ($written != 0) {
                                            $template .=  ', ';
                                        }
										$written++;
										$template .=  '<acronym title="his rank is '.getRankByID(getRankFromPID($victims[$i]['victim'])).'"><img src="'.$ROOT.'game-images/ranks/icon/rank_'.getRankFromPID($victims[$i]['victim']).'.gif"> <a rel="nofollow" href="?pid='.$victims[$i]['victim'].'">'.getNickFromPID($victims[$i]['victim']).'</a></acronym> ('.$victims[$i]['count'].')';
									}
								}
								$template .= '
							</td>	
						</tr>
						<tr>
							<td nowrap="nowrap">Worst Enemy<br />(Deaths by)</td>';
								if ($enemies) {
                                    $template .=  '<td nowrap="nowrap"><acronym title="his rank is '.getRankByID(getRankFromPID($enemies[0]['attacker'])).'"><img src="'.$ROOT.'game-images/ranks/icon/rank_'.getRankFromPID($enemies[0]['attacker']).'.gif"> <a rel="nofollow" href="?pid=' . $enemies[0]['attacker'] . '">' . getNickFromPID($enemies[0]['attacker']) . '</a></acronym> (' . $enemies[0]['count'] . ')</td>';
                                } else {
                                    $template .=  '<td>It seems you are invincible!</td>';
                                }
								$template .= '
						</tr>
						<tr>
							<td nowrap="nowrap">More Enemies<br />(Top 10)</td>
							<td>';
								$limit = count($enemies) < 10 ? count($enemies)-1 : 9;			
								
								$written = 0;
								for ($i=1; $i<=$limit; $i++)
								{
									if ($enemies && trim((string) $enemies[$i]['attacker']) !== '' && $enemies[$i]['count'] > 1) // minimum 2 kills
									{
										if ($written != 0) {
                                            $template .=  ', ';
                                        }
										$written++;
										$template .=  '<acronym title="his rank is '.getRankByID(getRankFromPID($enemies[$i]['attacker'])).'"><img src="'.$ROOT.'game-images/ranks/icon/rank_'.getRankFromPID($enemies[$i]['attacker']).'.gif"> <a rel="nofollow" href="?pid='.$enemies[$i]['attacker'].'">'.getNickFromPID($enemies[$i]['attacker']).'</a></acronym> ('.$enemies[$i]['count'].')';
									}
								}
								$template .= '	
							</td>
						</tr>
					</table>
			
					<table border="0" cellpadding="0" cellspacing="0" id="misc" class="stat">
						<tr>
							<th colspan="2">Misc. Stats</th>

						</tr>
						<tr>
							<td>PID (Player ID Number)</td>
							<td nowrap="nowrap">' . $player['id'] . ' </td>
						</tr>
						<tr>
							<td>Enrollment Date </td>
							<td nowrap="nowrap">' . date('Y-m-d H:i:s', $player['joined']) . '</td>
						</tr>
						<tr>
							<td>Last Battle On </td>
							<td nowrap="nowrap">' . date('Y-m-d H:i:s', $player['lastonline']) . '</td>
						</tr>
						<tr>
							<td>Last Update </td>
							<td nowrap="nowrap">{:LASTUPDATE:}</td>
						</tr>
						<tr>
							<td>Next Update</td>
							<td nowrap="nowrap">{:NEXTUPDATE:}</td>
						</tr>	
						<tr>
							<td>Kicked &amp; Banned</td>
							<td nowrap="nowrap">' . $player['kicked'] . ' / ' . $player['banned'] . '</td>
						</tr>
						<tr>
							<td>Team: Kills, Damage, &amp; Vehicle Damage </td>

							<td nowrap="nowrap">' . $player['teamkills'] . ' / ' . $player['teamdamage'] . ' / ' . $player['teamvehicledamage'] . '</td>
						</tr>
						<tr>
							<td>Cost ($50 base)</td>
							<td nowrap="nowrap">';
								if ($player['time']) {
                                    $template .= round(50/($player['time']/3600), 4);
                                } else {
                                    $template .= 0;
                                }
								$template .= '&nbsp;<u><abbr title="cents per hour">cph</abbr></u>
							</td>
						</tr>
					</table>
				</div>
				<!-- END COL RIGHT -->

				<hr class="clear" />

				<table border="0" cellspacing="0" cellpadding="0" id="unlocks" class="basic-stat">
					<tr>
						<th>Unlocks</th>
					</tr>
					<tr>
						<td>';
							foreach ($unlocks as $uid => $value)
							{
								$weapon = getUnlockByID($uid);
								$template .=  '
									<div class="unlock-inline" onmouseover="show_mine(this);" onmouseout="hide_mine(this);">
											<a href="http://wiki.bf2s.com/weapons/unlocks/'.strtolower((string) $weapon).'"><img src="'.$ROOT.'spacer.gif" style="background: url(\''.$ROOT.'game-images/unlocks/'.$unlocks[$uid]['state'] .'/'.$uid.'.png\');" width="115" height="33" alt="" /></a>
											<div class="unlock-pop dir-left">
												<img src="'.$ROOT.'spacer.gif" style="background: url(\''.$ROOT.'game-images/unlocks/full/'.$uid.'.jpg\');" width="128" height="128" alt="" />
												<strong>Click for more about the '.$weapon.'</strong>
											</div>
										</div>';
							}
							$template .= '
						</td>
					</tr>
				</table>

				<table border="0" cellspacing="0" cellpadding="0" id="awards" class="stat">
				
					<tr>
						<th colspan="2">Awards</th>
					</tr>
					<tr>
						<td>Badges</td>
						<td class="awards-row">';
							$count = getBadgeCount();
							for ($i=0; $i<$count;$i++)
							{
								$awardlevel = getBadgeLevel($PlayerAwards[$i]);
								$template .= '
								<div class="award-inline" onMouseOver="show_mine(this);" onMouseOut="hide_mine(this);">
									<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/';
									if ($awardlevel>0) {
                                        $template .= 'front/'.$PlayerAwards[$i][$awardlevel][AWD].'_'.$awardlevel;
                                    } else {
                                        $template .= 'locked/'.$PlayerAwards[$i][$awardlevel][AWD].'_0';
                                    }
									
									$template .= '.png\');" width="42" height="42" alt="" />
									
									<div class="award-pop dir-';
										if ($i < $count / 2) {
                                            $template .= 'left';
                                        } else {
                                            $template .= 'right';
                                        }
									
										$template .= '">
										<p>
											<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/perspective/'.$PlayerAwards[$i][$awardlevel][AWD].'_'.$awardlevel.'.jpg\');" width="128" height="128" alt="" />
											<strong>'.$PlayerAwards[$i][$awardlevel][NAME].'</strong>
										</p>
										<ul>
											<li><strong>Basic</strong>'.earned($PlayerAwards[$i][1][EARNED]).'</li>
											<li><strong>Veteran</strong>'.earned($PlayerAwards[$i][2][EARNED]).'</li>
											<li><strong>Expert</strong>'.earned($PlayerAwards[$i][3][EARNED]).'</li>
										</ul>
									</div>
								</div>';
							}
							$template .= '
							</tr>
							<tr>
								<td>Medals</td>
								<td class="awards-row extra-space">';
									$oldcount = $count;
									$awdcount = getMedalCount();
									$count = $oldcount+$awdcount;
									for ($i=$oldcount; $i<$count;$i++)
									{

										$template .= '
										<div class="award-inline" onMouseOver="show_mine(this);" onMouseOut="hide_mine(this);">
											<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/';
											if ($PlayerAwards[$i][0][LEVEL]>0) {
                                                $template .= 'front/'.$PlayerAwards[$i][0][AWD];
                                            } else {
                                                $template .= 'locked/'.$PlayerAwards[$i][0][AWD];
                                            }
											
											$template .= '.png\');" width="42" height="42" alt="" />
											<div class="award-pop dir-';
											if ($i-$oldcount<$awdcount/2) {
                                                $template .= 'left';
                                            } else {
                                                $template .= 'right';
                                            }
											
											$template .= '">
												<p>
													<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/perspective/'.$PlayerAwards[$i][0][AWD].'.jpg\');" width="128" height="128" alt="" />
													<strong>'.$PlayerAwards[$i][0][NAME].'</strong>
												</p>
												<ul>
													<li>First received: '.earned($PlayerAwards[$i][0][FIRST]).'</li>
													<li>Last awarded: '.earned($PlayerAwards[$i][0][EARNED]).'</li>
													<li>Total awards: '.$PlayerAwards[$i][0][LEVEL].'</li>
												</ul>
											</div>
										</div>';
									}

									$template .= '
								</td>
							</tr>

							<tr>
								<td>Ribbons</td>
								<td class="awards-row extra-space">';
									$oldcount = $count;
									$awdcount = getRibbonCount();
									$count = $oldcount+$awdcount;
									for ($i=$oldcount; $i<$count;$i++)
									{

										$template .= '
										<div class="award-inline" onMouseOver="show_mine(this);" onMouseOut="hide_mine(this);">
											<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/';
											if ($PlayerAwards[$i][0][LEVEL]>0) {
                                                $template .= 'front/'.$PlayerAwards[$i][0][AWD];
                                            } else {
                                                $template .= 'locked/'.$PlayerAwards[$i][0][AWD];
                                            }
											
											$template .= '.png\');" width="42" height="42" alt="" />
											<div class="award-pop dir-';
											if ($i-$oldcount<$awdcount/2) {
                                                $template .= 'left';
                                            } else {
                                                $template .= 'right';
                                            }
											
											$template .= '">
												<p>
													<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/perspective/'.$PlayerAwards[$i][0][AWD].'.jpg\');" width="128" height="128" alt="" />
													<strong>'.$PlayerAwards[$i][0][NAME].'</strong>
												</p>
												<ul>
													<li>First received: '.earned($PlayerAwards[$i][0][EARNED]).'</li>
												</ul>
											</div>
										</div>';
									}

									$template .= '
									<tr>
										<td>SF Badges<br />&amp; Medals</td>
										<td class="awards-row">';
											$oldcount = $count;
											$awdcount = getSFBadgeCount();
											$count = $oldcount+$awdcount;
											for ($i=$oldcount; $i<$count;$i++)
											{
												$awardlevel = getBadgeLevel($PlayerAwards[$i]);
												if ($i-$oldcount<10)
												{
													$template .= '
													<div class="award-inline" onMouseOver="show_mine(this);" onMouseOut="hide_mine(this);">
														<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/';
														if ($awardlevel>0) {
                                                            $template .= 'front/'.$PlayerAwards[$i][$awardlevel][AWD].'_'.$awardlevel;
                                                        } else {
                                                            $template .= 'locked/'.$PlayerAwards[$i][$awardlevel][AWD].'_0';
                                                        }

														$template .= '.png\');" width="42" height="42" alt="" />
														<div class="award-pop dir-';
														if ($i-$oldcount<$awdcount/2) {
                                                            $template .= 'left';
                                                        } else {
                                                            $template .= 'right';
                                                        }
															
														$template .= '">
															<p>
																<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/perspective/'.$PlayerAwards[$i][$awardlevel][AWD].'_'.$awardlevel.'.jpg\');" width="128" height="128" alt="" />
																<strong>'.$PlayerAwards[$i][$awardlevel][NAME].'</strong>
															</p>
															<ul>
																<li><strong>Basic</strong>'.earned($PlayerAwards[$i][1][EARNED]).'</li>
																<li><strong>Veteran</strong>'.earned($PlayerAwards[$i][2][EARNED]).'</li>
																<li><strong>Expert</strong>'.earned($PlayerAwards[$i][3][EARNED]).'</li>
															</ul>
														</div>
													</div>';
												}
												else
												{
													$template .= '
													<div class="award-inline" onMouseOver="show_mine(this);" onMouseOut="hide_mine(this);">
														<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/';
														if ($PlayerAwards[$i][0][LEVEL]>0) {
                                                            $template .= 'front/'.$PlayerAwards[$i][0][AWD];
                                                        } else {
                                                            $template .= 'locked/'.$PlayerAwards[$i][0][AWD];
                                                        }
														
														$template .= '.png\');" width="42" height="42" alt="" />
														<div class="award-pop dir-';
														if ($i-$oldcount<$awdcount/2) {
                                                            $template .= 'left';
                                                        } else {
                                                            $template .= 'right';
                                                        }
														
														$template .= '">
															<p>
																<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/perspective/'.$PlayerAwards[$i][0][AWD].'.jpg\');" width="128" height="128" alt="" />
																<strong>'.$PlayerAwards[$i][0][NAME].'</strong>
															</p>
															<ul>
																<li>First received: '.earned($PlayerAwards[$i][0][FIRST]).'</li>
																<li>Last awarded: '.earned($PlayerAwards[$i][0][EARNED]).'</li>
																<li>Total awards: '.$PlayerAwards[$i][0][LEVEL].'</li>
															</ul>
														</div>
													</div>';
												}
											}

											$template .= '
										</td>
									</tr>

				
									<tr>
										<td>SF Ribbons</td>
										<td class="awards-row extra-space">';
											$oldcount = $count;
											$awdcount = getSFRibbonCount();
											$count = $oldcount+$awdcount;
											for ($i=$oldcount; $i<$count;$i++)
											{
												$template .= '
												<div class="award-inline" onMouseOver="show_mine(this);" onMouseOut="hide_mine(this);">
													<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/';
													if ($PlayerAwards[$i][0][LEVEL]>0) {
                                                        $template .= 'front/'.$PlayerAwards[$i][0][AWD];
                                                    } else {
                                                        $template .= 'locked/'.$PlayerAwards[$i][0][AWD];
                                                    }
													
													$template .= '.png\');" width="42" height="42" alt="" />
													<div class="award-pop dir-';
													if ($i-$oldcount<$awdcount/2) {
                                                        $template .= 'left';
                                                    } else {
                                                        $template .= 'right';
                                                    }
													
													$template .= '">
														<p>
															<img src="' . $ROOT . 'spacer.gif" style="background: url(\'' . $ROOT . 'game-images/awards/perspective/'.$PlayerAwards[$i][0][AWD].'.jpg\');" width="128" height="128" alt="" />
															<strong>'.$PlayerAwards[$i][0][NAME].'</strong>
														</p>
														<ul>
															<li>First received: '.earned($PlayerAwards[$i][0][EARNED]).'</li>
														</ul>
													</div>
												</div>';
											}

											$template .= '
										</td>
									</tr>
								</td>
							</tr>
						</table>';
			
						$RANK_INFO = getNextRankInfo($_GET['pid']);
						$template .= '
						<table border="0" cellspacing="0" cellpadding="0" id="tta" class="stat">
							<tr>
								<th colspan="2">Time To Advancement</th>
							</tr>
							<tr>
								<td>Rank</td>
								<td>';
								foreach($RANK_INFO as $value)
								{
									$template .= '
									<img src="' . $ROOT . 'game-images/ranks/progress/rank_'.$value['rank'].'.png" alt="" style="float: left; margin: 0 5px 5px 0" height="83" width="83" />			
									<p>
										<strong>Next Rank: '.$value['title'].'</strong>
									</p>
									
									<div class="progressbar">
										<div class="progress" style="width: '.$value['percent'].'%"><span>'.$value['percent'].'%</span></div>
									</div>
									
									<small>Score: '. @number_format($player['score']) .' of '. @number_format($value['rank_points']) .'. At your historical rate, you should earn '
										. @number_format($value['points_needed']) .' in '.$value['days'].' days (or '.$value['time_straight'].' straight).</small>
									
									<div class="clear"> </div>';
								}
								
								$template .='	
								</td>
							</tr>
						</table>

						<a id="secondhome" href="'.$ROOT.'"> </a>
						<!-- end content == footer below -->

						<hr class="clear" />
						<br><br><br><br><br><br><br><br><br>
					</tr>
				</table>
			</div> <!-- Page 2 -->
			<div id="footer">This page was processed in {:PROCESSED:} seconds.</div>
		</div> <!-- page 1 --><!-- content -->
		<ul id="navitems">
			<li><a href="'. $ROOT .'">Home</a></li>
			<li><a href="'. $ROOT .'?go=search">Search Players</a></li>
			<li><a href="'. $ROOT .'?go=my-leaderboard">My Leader Board</a></li>
			<li><a href="'. $ROOT .'?go=currentranking">Rankings</a></li>
			<li><a href="http://ubar.bf2s.com/">UBAR</a></li>
			<li><a href="http://wiki.bf2s.com/">Wiki</a></li>
		</ul>
	</div>
</body>
</html>';
?>