<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span><i class="icon-map-marker"></i> {round.map_display_name}</span>
    </div>
    <div class="mws-panel-body" style="text-align: center;">
        <span class="thumbnail" style="max-height: 210px">
            <img src="/ASP/frontend/images/maps/{round.name}.png" style="max-height: 210px">
        </span>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-book"></i> Round Information</span>
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
        </ul>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-flag"></i> Round Summary</span>
    </div>
    <div class="mws-panel-body">
        <div class="table-container">
            <div class="left">
                <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team1_army_id}.png"/>
                <br />
                {round.team1name}
            </div>
            <div class="center">
                <span style="font-weight: 900; font-size: 32px;">{round.tickets1} : {round.tickets2}</span>
            </div>
            <div class="right">
                <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team2_army_id}.png"/>
                <br />
                {round.team2name}
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key">
                    <img src="/ASP/frontend/images/icons/flag_blue.png" style="height: 24px"/> Winning Team
                </span>
                <span class="val">
					<span class="text-nowrap">{round.winningTeamName}</span>
				</span>
            </li>
            <li>
                <span class="key">
                    <img src="/ASP/frontend/images/icons/award_star_gold.png" style="height: 24px"/> 1st Place
                </span>
                <span class="val">
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{first_place.rank}.gif"/>
                        <a href="/ASP/players/history/{first_place.id}/{round.id}">{first_place.name}</a>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{first_place.team}.png"/>
                    </span>
				</span>
            </li>
            <li>
                <span class="key">
                    <img src="/ASP/frontend/images/icons/award_star_silver.png" style="height: 24px"/> 2nd Place
                </span>
                <span class="val">
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{second_place.rank}.gif"/>
                        <a href="/ASP/players/history/{second_place.id}/{round.id}">{second_place.name}</a>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{second_place.team}.png"/>
                    </span>
				</span>
            </li>
            <li>
                <span class="key">
                    <img src="/ASP/frontend/images/icons/award_star_bronze.png" style="height: 24px"/> 3rd Place
                </span>
                <span class="val">
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{third_place.rank}.gif"/>
                        <a href="/ASP/players/history/{third_place.id}/{round.id}">{third_place.name}</a>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{third_place.team}.png"/>
                    </span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_8">
    <div class="mws-tabs">
        <ul>
            <li>
                <a href="#tab-1">
                    <span>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team1_army_id}.png"/>
                        All Players
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team2_army_id}.png"/>
                    </span>
                </a>
            </li>
            <li>
                <a href="#tab-2">
                    <span>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team1_army_id}.png"/>
                         {round.team1name}
                    </span>
                </a>
            </li>
            <li>
                <a href="#tab-3">
                    <span>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team2_army_id}.png"/>
                        {round.team2name}
                    </span>
                </a>
            </li>
        </ul>
        <div id="tab-1" class="mws-panel-body no-padding">
            <table id="all" class="mws-datatable-fn mws-table">
                <thead>
                    <tr>
                        <th style="width: 5%">Rank</th>
                        <th>Name</th>
                        <th style="width: 5%">Team</th>
                        <th style="width: 7%">Score</th>
                        <th style="width: 7%">Time</th>
                        <th style="width: 7%">SPM</th>
                        <th style="width: 7%">Kills</th>
                        <th style="width: 7%">Deaths</th>
                        <th style="width: 10%">Skill Score</th>
                        <th style="width: 10%">Team Score</th>
                        <th style="width: 10%">Cmd Score</th>
                    </tr>
                </thead>
                <tbody>
                {players}
                    <tr>
                        <td><img class="center" src="/ASP/frontend/images/ranks/rank_{rank_id}.gif"/></td>
                        <td>
                            <a href="/ASP/players/history/{player_id}/{round.id}"
                               rel="tooltip"
                               data-placement="right"
                               title="Click to view Player Round Details">
                                {name}
                            </a>
                        </td>
                        <td><img class="center" src="/ASP/frontend/images/armies/small/{army_id}.png"/></td>
                        <td>{score}</td>
                        <td data-order="{time}">{time_formatted}</td>
                        <td>{spm}</td>
                        <td>{kills}</td>
                        <td>{deaths}</td>
                        <td>{skillscore}</td>
                        <td>{teamscore}</td>
                        <td>{cmdscore}</td>
                    </tr>
                {/players}
                </tbody>
            </table>
        </div>
        <div id="tab-2" class="mws-panel-body no-padding">
            <table id="team1" class="mws-table">
                <thead>
                    <tr>
                        <th style="width: 5%">Rank</th>
                        <th>Name</th>
                        <th style="width: 7%">Score</th>
                        <th style="width: 7%">Time</th>
                        <th style="width: 7%">SPM</th>
                        <th style="width: 7%">Kills</th>
                        <th style="width: 7%">Deaths</th>
                        <th style="width: 10%">Skill Score</th>
                        <th style="width: 10%">Team Score</th>
                        <th style="width: 10%">Cmd Score</th>
                    </tr>
                </thead>
                <tbody>
                {players1}
                    <tr>
                        <td><img class="center" src="/ASP/frontend/images/ranks/rank_{rank_id}.gif"/></td>
                        <td>
                            <a href="/ASP/players/history/{player_id}/{round.id}"
                               rel="tooltip"
                               data-placement="right"
                               title="Click to view Player Round Details">
                                {name}
                            </a>
                        </td>
                        <td>{score}</td>
                        <td data-order="{time}">{time_formatted}</td>
                        <td>{spm}</td>
                        <td>{kills}</td>
                        <td>{deaths}</td>
                        <td>{skillscore}</td>
                        <td>{teamscore}</td>
                        <td>{cmdscore}</td>
                    </tr>
                {/players1}
                </tbody>
            </table>
        </div>
        <div id="tab-3" class="mws-panel-body no-padding">
            <table id="team2" class="mws-table">
                <thead>
                    <tr>
                        <th style="width: 5%">Rank</th>
                        <th>Name</th>
                        <th style="width: 7%">Score</th>
                        <th style="width: 7%">Time</th>
                        <th style="width: 7%">SPM</th>
                        <th style="width: 7%">Kills</th>
                        <th style="width: 7%">Deaths</th>
                        <th style="width: 10%">Skill Score</th>
                        <th style="width: 10%">Team Score</th>
                        <th style="width: 10%">Cmd Score</th>
                    </tr>
                </thead>
                <tbody>
                {players2}
                    <tr>
                        <td><img class="center" src="/ASP/frontend/images/ranks/rank_{rank_id}.gif"/></td>
                        <td>
                            <a href="/ASP/players/history/{player_id}/{round.id}"
                               rel="tooltip"
                               data-placement="right"
                               title="Click to view Player Round Details">
                                {name}
                            </a>
                        </td>
                        <td>{score}</td>
                        <td data-order="{time}">{time_formatted}</td>
                        <td>{spm}</td>
                        <td>{kills}</td>
                        <td>{deaths}</td>
                        <td>{skillscore}</td>
                        <td>{teamscore}</td>
                        <td>{cmdscore}</td>
                    </tr>
                {/players2}
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="clear"></div>

<div class="mws-panel grid_4 mws-collapsible" style="min-width: 330px">
    <div class="mws-panel-header">
        <span><i class="icon-pacman"></i> Top Player Scores</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            {topSkillPlayers}
            <li>
                <span class="key">{category}</span>
                <span class="val">
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/>
                        <a href="/ASP/players/history/{id}/{round.id}">{name}</a>
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/armies/small/{team}.png"/>
                        ({value})
                    </span>
				</span>
            </li>
            {/topSkillPlayers}
        </ul>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-accessibility-2"></i> Top Kit Players</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            {topKitPlayers}
            <li>
                <span class="key">{iteration.key}</span>
                <span class="val">
                    <span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/>
                        <a href="/ASP/players/history/{pid}/{round.id}">{name}</a>
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/armies/small/{team}.png"/>
                        ({score}) - [{time_string}] {kills} kills, {deaths} deaths
                    </span>
                </span>
            </li>
            {/topKitPlayers}
        </ul>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-chopper"></i> Top Vehicle Players</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            {topVehiclePlayers}
            <li>
                <span class="key">{iteration.key}</span>
                <span class="val">
                    <span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/>
                        <a href="/ASP/players/history/{pid}/{round.id}">{name}</a>
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/armies/small/{team}.png"/>
                        ({score}) - [{time_string}] {kills} kills, {deaths} deaths
                    </span>
                </span>
            </li>
            {/topVehiclePlayers}
        </ul>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-hand-down"></i> Game Commanders</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            {commanders}
            <li>
                <span class="key" style="width: 250px">
                    <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{team}.png"/>
                    <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/>
                    <a href="/ASP/players/history/{id}/{round.id}">{name}</a>
                </span>
                <span class="val">
                    <span class="text-nowrap">{score} points [{time_string}]</span>
                </span>
            </li>
            {/commanders}
        </ul>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible" style="min-width: 330px">
    <div class="mws-panel-header">
        <span><i class="icon-trophy"></i> Player Earned Awards</span>
    </div>
    <div class="mws-panel-body">
        <ul class="thumbnails mws-gallery">
        {awards}
            <li>
                <span class="thumbnail"
                      rel="popover"
                      data-trigger="hover"
                      data-placement="bottom"
                      data-original-title="{name}"
                      data-content="<div><b>Player</b>: <img style='margin: -3px 0 0 0' src='/ASP/frontend/images/ranks/rank_{rank}.gif'/> {player_name}<img style='margin: -3px 10px 0 5px' src='/ASP/frontend/images/armies/small/{team}.png'/></div>">
                    <?php if ({type} == 0): ?>
                    <img src="/ASP/frontend/images/awards/color/ribbons/{id}.png">
                    <?php elseif ({type} == 1): ?>
                    <img src="/ASP/frontend/images/awards/color/badges/{id}_{level}.png">
                    <?php elseif ({type} == 2): ?>
                    <img src="/ASP/frontend/images/awards/color/medals/{id}.png">
                    <?php endif ?>
                </span>
            </li>
        {/awards}
        </ul>
    </div>
</div>