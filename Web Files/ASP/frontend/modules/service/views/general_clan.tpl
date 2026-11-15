<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_5">
    <div class="mws-panel-header">
        <span><i class="icon-star"></i> Current 4-Star General</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="player-table" style="width: 100%;">
            <tr>
                <td id="rank" style="width: 320px">
                    <span class="thumbnail">
                        <img src="/ASP/frontend/images/ranks/large2/rank_21.png">
                    </span>
                </td>
                <td style="max-width: 99%">
                    <span class="thumbnail thumbnail-value clearfix" style="height: 300px">
                    <ul class="mws-summary clearfix">
                        <li>
                            <!-- Hidden fields for JavaScript -->
                            <span id="playerCurrentId" style="display: none">{player.id}</span>
                            <!-- End Hidden fields for JavaScript -->
                            <span class="key"><i class="icon-user"></i> Player Name</span>
                            <span class="val">
                                <span class="text-nowrap">
                                    <a href="/ASP/players/view/{player.id}" target="_blank">{player.name}</a>
                                </span>
                            </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-flag"></i> Player Country</span>
                            <span class="val">
                                <img id="flag" style="margin: -3px 10px 0 0; height: 16px;" src="/ASP/frontend/images/flags/{player.country}.png"/>
                                <span id="fullCountryName" class="text-nowrap">{player.country}</span>
                                (Last IP: {player.lastip})
                            </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-check"></i> Account Status</span>
                            <span class="val">
                                <span class="text-nowrap">
                                    <label id="status" class="label label-{player.badge}">{player.statustext}</label>
                                </span>
                            </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-calendar"></i> Join Date</span>
                            <span class="val">
                                <span class="text-nowrap">{player.joined}</span>
                            </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-history-2"></i> Last Seen</span>
                            <span class="val">
                                <span class="text-nowrap">{player.lastonline}</i></span>
                            </span>
                        </li>
                        <li>
                            <span class="key"><i class="icon-retweet"></i> Games Played As</span>
                            <span class="val">
                                <span class="text-nowrap">{player.games}</span>
                            </span>
                        </li>
                    </ul>
                    </span>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-book"></i> 4-Star General Summary</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-watch2"></i> Last Selection</span>
                <span class="val">
					<span class="text-nowrap">{last_selection}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-hour-glass"></i> Next Selection Due</span>
                <span class="val">
					<span class="text-nowrap" style="color: {next_color}; ">{next_due}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-power-2"></i> Last Eligible Refresh</span>
                <span class="val">
					<span class="text-nowrap" style="color: {last_color}; ">{last_rebuild}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-fire"></i> Number of Eligible</span>
                <span class="val">
					<span class="text-nowrap">{records}</span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-users"></i> Eligible 4-Star General List</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="rebuild-btn" href="#" class="btn" style="color: {last_color}; "><i class="icol-application-lightning"></i> Rebuild List</a>
                <a id="show-bots" href="#" class="btn"><i class="icol-eye"></i> Show Bot Players</a>
                <a id="hide-bots" href="#" class="btn" style="display: none"><i class="icol-delete"></i> Hide Bot Players</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
            <tr>
                <th style="width: 3%"><span class="loading-cell"></span></th>
                <th style="width: 5%">Rank</th>
                <th style="width: 20%">Name</th>
                <th>Country</th>
                <th>Global Score</th>
                <th>Score Per Min.</th>
                <th>Rising Star Score</th>
                <th>Games Played (7 days)</th>
                <th>Last Seen</th>
                <th style="width: 7%">Actions</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Hidden Ajax Thing -->
<div id="ajax-dialog">
    <div class="mws-dialog-inner">
        <div style="text-align: center;">
            <img src="/ASP/frontend/images/core/loading32.gif" />
            <br />
            <br />
            Rebuilding 4-Star General Eligibility DataTable... Please allow up to 60 seconds for this process to complete.
            <br />
            <br />
            <span style="color: red; ">DO NOT</span> refresh this window.
        </div>
    </div>
</div>

<!-- Select Player Confirmation Model -->
<div id="mws-jui-dialog">
    <div class="mws-dialog-inner">
        <div id="jui-local-message" class="alert" style="display: none;"></div>
        <p id="question">
            Are you sure you want to promote <span id="selectPlayerName"></span> as the next 4-Star General?
        </p>
    </div>
</div>