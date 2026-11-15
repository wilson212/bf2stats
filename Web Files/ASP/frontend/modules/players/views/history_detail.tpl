<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-flag"></i> Round Summary</span>
    </div>
    <div class="mws-panel-body no-padding">
        <div class="table-container">
            <div class="left">
                <img style="height: 96px;" src="/ASP/frontend/images/maps/{round.lc_mapname}.png"/>
                <span style="font-weight: bold; font-size: large; margin-left: 15px;">{round.map_display_name}</span>
            </div>
            <div class="center">
                <div class="table-container">
                    <div class="left">
                        <img style="margin: -3px 0 0 5px; height: 64px;" src="/ASP/frontend/images/armies/large/{round.team1_army_id}.png"/>
                    </div>
                    <div class="center">
                        <span style="font-weight: 900; font-size: 32px;">{round.tickets1} : {round.tickets2}</span>
                    </div>
                    <div class="right">
                        <img style="margin: -3px 0 0 5px; height: 64px;" src="/ASP/frontend/images/armies/large/{round.team2_army_id}.png"/>
                    </div>
                </div>
            </div>
            <div class="right">
                <div class="btn-toolbar">
                    <div class="btn-group" style="margin-right: 20px">
                        <button id="go-{prevRoundId}" type="button" class="btn"{pBtnStyle}>Previous</button>
                        <a href="/ASP/roundinfo/view/{round.round_id}" class="btn" target="_blank">View</a>
                        <button id="go-{nextRoundId}" type="button" class="btn"{nBtnStyle}>Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span>Player Information</span>
    </div>
    <div class="mws-panel-body no-padding">
        <img src="/ASP/frontend/images/armies/wide/{round.army_id}.png"/>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <!-- Hidden fields for JavaScript -->
                <span id="playerId" style="display: none">{round.player_id}</span>
                <!-- End Hidden fields for JavaScript -->
                <span class="key"><i class="icon-user"></i> Player Name</span>
                <span class="val">
					<span class="text-nowrap">
                        <a href="/ASP/players/view/{round.player_id}">{round.name}</a>
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-rating3"></i> Player Rank</span>
                <span class="val">
					<span class="text-nowrap">
                        <img id="rankIcon" style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{round.rank_id}.gif"/>
                        <span id="changeableRank">{round.rankName}</span>
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-flag"></i> Team Name</span>
                <span class="val">
					<span class="text-nowrap">{round.teamName}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-watch2"></i> Time Played</span>
                <span class="val">
					<span class="text-nowrap">{round.timePlayed}</i></span>
				</span>
            </li>
            <?php if ($advanced): ?>
            <li>
                <span class="key"><i class="icon-check"></i> Completed Round</span>
                <span class="val">
					<span class="text-nowrap">{completed}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-gun-forbidden"></i> Kicked & Banned</span>
                <span class="val">
					<span class="text-nowrap">{kicked} / {banned}</span>
				</span>
            </li>
            <?php endif ?>
        </ul>
    </div>
</div>
<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Overall Performance</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-stats-up"></i> Score</span>
                <span class="val">
					<span class="text-nowrap">{round.score}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-gun"></i> Skill Score</span>
                <span class="val">
					<span class="text-nowrap">{round.skillscore}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-tools"></i> Team Score</span>
                <span class="val">
					<span class="text-nowrap">{round.teamscore}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-star"></i> Command Score</span>
                <span class="val">
					<span class="text-nowrap">{round.cmdscore}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-target2"></i> Total Kills</span>
                <span class="val">
					<span class="text-nowrap">{round.kills}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-heart-broken3"></i> Total Deaths</span>
                <span class="val">
					<span class="text-nowrap">{round.deaths}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-balance"></i> K/D Ratio</span>
                <span class="val">
					<span class="text-nowrap">{round.ratio} (<span style="color: {round.ratioColor}">{round.ratio2}</span>)</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-meter-fast"></i> Score Per Min.</span>
                <span class="val">
					<span class="text-nowrap">{round.spm}</span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span> Round Information</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-monitor"></i> Server Name</span>
                <span class="val">
					<span class="text-nowrap">
                        <a href="/ASP/servers/view/{round.server_id}">{round.server}</a>
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-globe"></i>Map Name</span>
                <span class="val">
					<span class="text-nowrap">
                        <a href="/ASP/mapinfo/view/{round.map_id}">{round.map_display_name}</a>
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-users"></i> Players</span>
                <span class="val">
					<span class="text-nowrap">{round.playerCount}</i></span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-folder-closed"></i> Game Mod</span>
                <span class="val">
					<span class="text-nowrap">{round.modname}</i></span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-direction"></i> Game Mode</span>
                <span class="val">
					<span class="text-nowrap">{round.gamemode}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-history-2"></i> Round Start</span>
                <span class="val">
					<span class="text-nowrap">{round.round_start_date}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-retweet"></i> Round End</span>
                <span class="val">
					<span class="text-nowrap">{round.round_end_date}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-watch2"></i> Round Time</span>
                <span class="val">
					<span class="text-nowrap">{round.roundTime}</i></span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div class="clear"></div>
<?php if ($advanced): ?>
<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Team Work</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-aid-kit"></i> Heals</span>
                <span class="val">
					<span class="text-nowrap">{heals}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-pulse"></i> Revives</span>
                <span class="val">
					<span class="text-nowrap">{revives}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-ammo"></i> Resupplies</span>
                <span class="val">
					<span class="text-nowrap">{resupplies}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-wrench"></i> Repairs</span>
                <span class="val">
					<span class="text-nowrap">{repairs}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-flag"></i> Flag Captures</span>
                <span class="val">
					<span class="text-nowrap"
                          style="border-bottom: 1px dotted #000;"
                          rel="tooltip"
                          data-placement="right"
                          title="Captures / Assists">
                        {captures} / {captureassists}
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-flag2"></i> Neutralizes</span>
                <span class="val">
					<span class="text-nowrap"
                          style="border-bottom: 1px dotted #000;"
                          rel="tooltip"
                          data-placement="right"
                          title="Neutralizes / Assists">
                        {neutralizes} / {neutralizeassists}
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-shield"></i> Flag Defends</span>
                <span class="val">
					<span class="text-nowrap">{defends}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-steering-wheel"></i> Driver Specials</span>
                <span class="val">
					<span class="text-nowrap">{driverspecials}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-screenshot"></i> Kill Assists</span>
                <span class="val">
					<span class="text-nowrap">{damageassists}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-pushpin"></i> Target Assists</span>
                <span class="val">
					<span class="text-nowrap">{targetassists}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Time Statistics</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-stars"></i> Commander</span>
                <span class="val">
					<span class="text-nowrap">{cmdtime}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-star"></i> Squad Leader</span>
                <span class="val">
					<span class="text-nowrap">{sqltime}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-star-empty"></i> Squad Member</span>
                <span class="val">
					<span class="text-nowrap">{sqmtime}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-rocket"></i> Lone Wolf</span>
                <span class="val">
					<span class="text-nowrap">{lwtime}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Negative Statistics</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-skull"></i> Team Kills</span>
                <span class="val">
					<span class="text-nowrap">{teamkills}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-pandage"></i> Team Damage</span>
                <span class="val">
					<span class="text-nowrap">{teamdamage}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-truck"></i> Team Vehicle Dam.</span>
                <span class="val">
					<span class="text-nowrap">{teamvehicledamage}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-heart-broken3"></i> Suicides</span>
                <span class="val">
					<span class="text-nowrap">{suicides}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Misc Statistics</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-screenshot"></i> Best Kill Streak</span>
                <span class="val">
					<span class="text-nowrap">{killstreak}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-user-minus"></i> Worst Death Streak</span>
                <span class="val">
					<span class="text-nowrap">{deathstreak}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-skull"></i> Favorite Victim</span>
                <span class="val">
                    <?php if ($favVictim['id'] == 0): ?>
                    <span class="text-nowrap">
                        None (0)
                    </span>
                    <?php else: ?>
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{favVictim.rank}.gif"/>
                        <a href="/ASP/players/view/{favVictim.id}/history/{round.roundid}">
                            {favVictim.name}
                        </a> ({favVictim.count})
                    </span>
                    <?php endif ?>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-skull2"></i> Worst Enemy</span>
                <span class="val">
                    <?php if ($worstOp['id'] == 0): ?>
                    <span class="text-nowrap">
                        None (0)
                    </span>
                    <?php else: ?>
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{worstOp.rank}.gif"/>
                        <a href="/ASP/players/view/{worstOp.id}/history/{round.roundid}">
                            {worstOp.name}
                        </a> ({worstOp.count})
                    </span>
                    <?php endif ?>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-pie-chart"></i> Kill Death Ratio</span>
    </div>
    <div class="mws-panel-body">
        <div class="mws-panel-content">
            <div id="mws-pie-1" style="width:100%; height:300px; "></div>
        </div>
    </div>
</div>

<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-chart"></i> Time Used By</span>
    </div>
    <div class="mws-panel-body">
        <div class="mws-panel-content">
            <div id="mws-pie-2" style="width:100%; height:300px; "></div>
        </div>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Kit Data</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="kitData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Kills</th>
                <th style="width: 15%">Deaths</th>
                <th style="width: 15%">Ratio</th>
            </tr>
            </thead>
            <tbody>
            {kitData}
                <tr>
                    <td>{name}</td>
                    <td>{time}</td>
                    <td>{kills}</td>
                    <td>{deaths}</td>
                    <td>{ratio}</td>
                </tr>
            {/kitData}
            </tbody>
            <tfoot>
            <tr>
                <td>Totals</td>
                <td>{kitTotals.time}</td>
                <td>{kitTotals.kills}</td>
                <td>{kitTotals.deaths}</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Averages</td>
                <td>{kitAverage.time}</td>
                <td>{kitAverage.kills}</td>
                <td>{kitAverage.deaths}</td>
                <td>{kitAverage.ratio}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Vehicle Data</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="vehicleData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Kills</th>
                <th style="width: 15%">Deaths</th>
                <th style="width: 15%">RK</th>
                <th style="width: 15%">Ratio</th>
            </tr>
            </thead>
            <tbody>
            {vehicleData}
                <tr>
                    <td>{name}</td>
                    <td>{time}</td>
                    <td>{kills}</td>
                    <td>{deaths}</td>
                    <td>{roadKills}</td>
                    <td>{ratio}</td>
                </tr>
            {/vehicleData}
            </tbody>
            <tfoot>
            <tr>
                <td>Totals</td>
                <td>{vehicleTotals.timePlayed}</td>
                <td>{vehicleTotals.kills}</td>
                <td>{vehicleTotals.deaths}</td>
                <td>{vehicleTotals.roadKills}</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Averages</td>
                <td>{vehicleAverage.time}</td>
                <td>{vehicleAverage.kills}</td>
                <td>{vehicleAverage.deaths}</td>
                <td>{vehicleAverage.roadKills}</td>
                <td>{vehicleAverage.ratio}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible clear">
    <div class="mws-panel-header">
        <span>Weapon Data</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="weaponData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Kills</th>
                <th style="width: 15%">Deaths</th>
                <th style="width: 15%">Acc</th>
                <th style="width: 15%">Ratio</th>
            </tr>
            </thead>
            <tbody>
            {weaponData}
                <tr>
                    <td>{name}</td>
                    <td>{time}</td>
                    <td>{kills}</td>
                    <td>{deaths}</td>
                    <td>
                        <span style="border-bottom: 1px dotted #000;" rel="tooltip"  title="Shots Fired: {fired}">
                            {accuracy}%
                        </span>
                    </td>
                    <td>{ratio}</td>
                </tr>
            {/weaponData}
            </tbody>
            <tfoot>
            <tr>
                <td>Totals</td>
                <td>{weaponTotals.timePlayed}</td>
                <td>{weaponTotals.kills}</td>
                <td>{weaponTotals.deaths}</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Averages</td>
                <td>{weaponAverage.time}</td>
                <td>{weaponAverage.kills}</td>
                <td>{weaponAverage.deaths}</td>
                <td>
                    <span style="border-bottom: 1px dotted #000;" rel="tooltip"  title="Average Shots Fired: {weaponAverage.fired}">
                        {weaponAverage.accuracy}%
                    </span>
                </td>
                <td>{weaponAverage.ratio}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Player Earned Awards</span>
    </div>
    <div class="mws-panel-body">
        <ul class="thumbnails mws-gallery">
            {badges}
            <li>
                <span class="thumbnail">
                    <img src="/ASP/frontend/images/awards/color/badges/{id}_{level}.png"/>
                </span>
            </li>
            {/badges}
            {medals}
            <li>
                <span class="thumbnail">
                    <img src="/ASP/frontend/images/awards/color/medals/{id}.png"/>
                </span>
            </li>
            {/medals}
            {ribbons}
            <li>
                <span class="thumbnail">
                    <img src="/ASP/frontend/images/awards/color/ribbons/{id}.png"/>
                </span>
            </li>
            {/ribbons}
        </ul>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible clear">
    <div class="mws-panel-header">
        <span>Player Victims</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="victimData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Kills</th>
            </tr>
            </thead>
            <tbody>
            {victims}
                <tr>
                    <td>
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/>
                        <a href="/ASP/players/history/{id}/{round.id}" rel="tooltip" title="View Player Round Info">{name}</a>
                    </td>
                    <td>{count}</td>
                </tr>
            {/victims}
            </tbody>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Deaths By</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="enemyData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Kills</th>
            </tr>
            </thead>
            <tbody>
            {enemies}
                <tr>
                    <td>
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/>
                        <a href="/ASP/players/history/{id}/{round.id}" rel="tooltip" title="View Player Round Info">{name}</a>
                    </td>
                    <td>{count}</td>
                </tr>
            {/enemies}
            </tbody>
        </table>
    </div>
</div>
<?php endif ?>