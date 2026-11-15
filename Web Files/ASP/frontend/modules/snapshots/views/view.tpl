<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span><i class="icon-map-marker"></i> {round.map_display_name}</span>
    </div>
    <div class="mws-panel-body no-padding" style="text-align: center">
        <img src="/ASP/frontend/images/maps/{round.mapname}.png">
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
                <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team1ArmyId}.png"/>
                <br />
                {round.team1name}
            </div>
            <div class="center">
                <span style="font-weight: 900; font-size: 32px;">{round.team1Tickets} : {round.team2Tickets}</span>
            </div>
            <div class="right">
                <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team2ArmyId}.png"/>
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
                        <a href="/ASP/players/view/{first_place.id}">{first_place.name}</a>
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
                        <a href="/ASP/players/view/{second_place.id}">{second_place.name}</a>
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
                        <a href="/ASP/players/view/{third_place.id}">{third_place.name}</a>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{third_place.team}.png"/>
                    </span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-eye-open"></i> BattleSpy Report Messages</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="battlespy" class="mws-datatable-fn mws-table">
            <thead>
            <tr>
                <th style="width: 7%">Severity</th>
                <th style="width: 5%">Rank</th>
                <th>Player Name</th>
                <th style="width: 60%">Message</th>
                <th style="width: 5%">Actions</th>
            </tr>
            </thead>
            <tbody>
            {battlespy_messages}
                <tr>
                    <td data-sort="{severity}">
                        <span class="badge badge-{badge}">{severity_name}</span>
                    </td>
                    <td><img class="center" src="/ASP/frontend/images/ranks/rank_{player_rank}.gif"></td>
                    <td>{player_name}</td>
                    <td>{message}</td>
                    <td>
                        <span class="btn-group">
                            <a id="go-btn" href="/ASP/players/view/{player_id}" rel="tooltip" title="View Player" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                        </span>
                    </td>
                </tr>
            {/battlespy_messages}
            </tbody>
        </table>
    </div>
</div>

<div class="mws-panel grid_8">
    <div class="mws-tabs">
        <ul>
            <li>
                <a href="#tab-1">
                    <span>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team1ArmyId}.png"/>
                        All Players
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team2ArmyId}.png"/>
                    </span>
                </a>
            </li>
            <li>
                <a href="#tab-2">
                    <span>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team1ArmyId}.png"/>
                        {round.team1name}
                    </span>
                </a>
            </li>
            <li>
                <a href="#tab-3">
                    <span>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{round.team2ArmyId}.png"/>
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
                        <td><img class="center" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/></td>
                        <td>
                            <a href="/ASP/players/view/{id}"
                               rel="tooltip"
                               data-placement="right"
                               title="Click to view Player">
                                {name}
                            </a>
                        </td>
                        <td><img class="center" src="/ASP/frontend/images/armies/small/{armyId}.png"/></td>
                        <td>{roundScore}</td>
                        <td data-order="{time}">{timeFormatted}</td>
                        <td>{scorePerMin}</td>
                        <td>{kills}</td>
                        <td>{deaths}</td>
                        <td>{skillScore}</td>
                        <td>{teamScore}</td>
                        <td>{commandScore}</td>
                    </tr>
                {/players}
                </tbody>
            </table>
        </div>
        <div id="tab-2" class="mws-panel-body no-padding">
            <table id="team1" class="mws-datatable-fn mws-table">
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
                        <td><img class="center" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/></td>
                        <td>
                            <a href="/ASP/players/view/{id}"
                               rel="tooltip"
                               data-placement="right"
                               title="Click to view Player">
                                {name}
                            </a>
                        </td>
                        <td>{roundScore}</td>
                        <td data-order="{time}">{timeFormatted}</td>
                        <td>{scorePerMin}</td>
                        <td>{kills}</td>
                        <td>{deaths}</td>
                        <td>{skillScore}</td>
                        <td>{teamScore}</td>
                        <td>{commandScore}</td>
                    </tr>
                {/players1}
                </tbody>
            </table>
        </div>
        <div id="tab-3" class="mws-panel-body no-padding">
            <table id="team2" class="mws-datatable-fn mws-table">
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
                        <td><img class="center" src="/ASP/frontend/images/ranks/rank_{rank}.gif"/></td>
                        <td>
                            <a href="/ASP/players/view/{id}"
                               rel="tooltip"
                               data-placement="right"
                               title="Click to view Player">
                                {name}
                            </a>
                        </td>
                        <td>{roundScore}</td>
                        <td data-order="{roundTime}">{timeFormatted}</td>
                        <td>{scorePerMin}</td>
                        <td>{kills}</td>
                        <td>{deaths}</td>
                        <td>{skillScore}</td>
                        <td>{teamScore}</td>
                        <td>{commandScore}</td>
                    </tr>
                {/players2}
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                        <a href="/ASP/players/view/{id}">{name}</a>
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
                        <a href="/ASP/players/view/{pid}">{name}</a>
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
                    <a href="/ASP/players/view/{id}">{name}</a>
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
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            {awards}
                <li>
                <span class="key" style="width: 250px">
                    <?php if ({award_type} == 0): ?>
                    <img style="margin: -2px 0 0 0; max-height: 24px;" src="/ASP/frontend/images/icons/ribbon.png"/>
                    <?php elseif ({award_type} == 1): ?>
                    <img style="margin: -2px 0 0 0; max-height: 24px;" src="/ASP/frontend/images/icons/badge_{award_level}.png"/>
                    <?php elseif ({award_type} == 2): ?>
                    <img style="margin: -2px 0 0 0; max-height: 24px;" src="/ASP/frontend/images/icons/medal.png"/>
                    <?php elseif ({award_type} == 3): ?>
                    <img style="margin: -2px 0 0 0; max-height: 24px;" src="/ASP/frontend/images/icons/award_star_gold.png"/>
                    <?php elseif ({award_type} == 4): ?>
                    <img style="margin: -2px 0 0 0; max-height: 24px;" src="/ASP/frontend/images/icons/award_star_silver.png"/>
                    <?php elseif ({award_type} == 5): ?>
                    <img style="margin: -2px 0 0 0; max-height: 24px;" src="/ASP/frontend/images/icons/award_star_bronze.png"/>
                    <?php endif ?>
                    {award_name}
                </span>
                    <span class="val">
                    <span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{player_rank}.gif"/>
                        <a href="/ASP/players/{player_id}">{player_name}</a>
                        <img style="margin: -3px 10px 0 5px" src="/ASP/frontend/images/armies/small/{player_team}.png"/>
                    </span>
                </span>
                </li>
            {/awards}
        </ul>
    </div>
</div>