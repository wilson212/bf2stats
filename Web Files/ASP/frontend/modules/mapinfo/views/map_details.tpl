<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span id="mapId" style="display: none">{map.id}</span>
        <span><i class="icon-map-marker"></i> {map.displayname}</span>
    </div>
    <div class="mws-panel-body" style="text-align: center">
        <span class="thumbnail">
            <img src="/ASP/frontend/images/maps/{map.lcname}.png">
        </span>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-book"></i> Map Information</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="edit-map" href="#" class="btn"><i class="icol-pencil"></i> Edit Map Name</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-globe"></i>Map Name</span>
                <span class="val">
					<span id="currentMapName" class="text-nowrap">{map.displayname}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-retweet"></i> Games Played</span>
                <span class="val">
					<span class="text-nowrap">{map.count}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-stats-up"></i> Total Score</span>
                <span class="val">
					<span class="text-nowrap">{map.score}</i></span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-target2"></i> Total Kills</span>
                <span class="val">
					<span class="text-nowrap">{map.kills}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-heart-broken3"></i> Total Deaths</span>
                <span class="val">
					<span class="text-nowrap">{map.deaths}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-watch2"></i> Total Time Played</span>
                <span class="val">
					<span class="text-nowrap">{map.time_display}</span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-chart"></i> Team Win / Loss Ratio</span>
    </div>
    <div class="mws-panel-body">
        <div class="mws-panel-content">
            <div id="mws-pie-1" style="width:100%; height:255px; "></div>
        </div>
    </div>
</div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-flag"></i> Top Players</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="topPlayers" class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width: 3%"><span class="loading-cell"></span></th>
                    <th style="width: 5%">PID</th>
                    <th style="width: 5%">Rank</th>
                    <th style="width: 25%">Name</th>
                    <th>Country</th>
                    <th>Total Map Score</th>
                    <th>Total Time Played</th>
                    <th style="width: 9%">Games Played</th>
                    <th style="width: 9%">Total Kills</th>
                    <th style="width: 9%">Total Deaths</th>
                    <th style="width: 5%">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Edit Map Name Ajax Model -->
<div id="editor-form">
    <form id="mws-validate" class="mws-form" method="post" action="/ASP/mapinfo/edit">
        <input id="post-action" type="hidden" name="action" value="edit">
        <input id="mapId" type="hidden" name="mapId" value="{map.id}">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Map Display Name</label>
                <div class="mws-form-item">
                    <input type="text" name="mapName" class="required large" title="">
                </div>
            </div>
        </div>
    </form>
</div>