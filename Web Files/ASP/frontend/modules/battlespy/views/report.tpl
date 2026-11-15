<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span><i class="icon-map-marker"></i> {round.map_display_name}</span>
        <span id="reportId" style="display: none">{report.id}</span>
    </div>
    <div class="mws-panel-body" style="text-align: center">
        <span class="thumbnail">
            <img src="/ASP/frontend/images/maps/<?php echo strtolower('{round.name}'); ?>.png">
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
					<span class="text-nowrap">{round.map_display_name}</span>
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
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group dropup">
                <a href="/ASP/roundinfo/view/{round.id}" target="_blank" class="btn"><i class="icol-magnifier"></i> View Round Details</a>
            </div>
        </div>
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
                    <img src="/ASP/frontend/images/icons/timer.png" style="height: 24px"/> Round Time
                </span>
                <span class="val">
					<span class="text-nowrap">{report.roundTime}</i></span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-eye-open"></i> BattleSpy Report Messages</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="delete-selected" href="#" class="btn"><i class="icol-cross"></i> Delete Selected Messages</a>
                <a id="delete-report" href="#" class="btn"><i class="icol-delete"></i> Delete Full Report</a>
                <a id="refresh" href="#" class="btn"><i class="icol-arrow-refresh"></i> Refresh</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
            <tr>
                <th class="checkbox-column">
                    <input id="select-all" type="checkbox">
                </th>
                <th style="width: 7%">Severity</th>
                <th style="width: 5%">Rank</th>
                <th>Player Name</th>
                <th style="width: 60%">Message</th>
                <th style="width: 7%">Actions</th>
            </tr>
            </thead>
            <tbody>
            {messages}
                <tr id="tr-report-{id}">
                    <td class="checkbox-column">
                        <input id="report-{id}" type="checkbox">
                    </td>
                    <td><span class="badge badge-{badge}">{severity_name}</span></td>
                    <td><img class="center" src="/ASP/frontend/images/ranks/rank_{rank_id}.gif"></td>
                    <td>{name}</td>
                    <td>{message}</td>
                    <td>
                        <span class="btn-group">
                            <a id="go-btn" href="/ASP/players/view/{player_id}" rel="tooltip" title="View Player" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a id="details-btn" href="/ASP/players/history/{player_id}/{round.id}" rel="tooltip" title="View Player Round Info" class="btn btn-small">
                                <i class="icon-chart"></i>
                            </a>
                            <a id="delete-btn-{id}" href="#" rel="tooltip" title="Delete Message" class="btn btn-small">
                                <i class="icon-trash"></i>
                            </a>
                        </span>
                    </td>
                </tr>
            {/messages}
            </tbody>
        </table>

        <!-- Delete Server Confirmation Model -->
        <div id="mws-jui-dialog">
            <div class="mws-dialog-inner"></div>
        </div>
    </div>
</div>