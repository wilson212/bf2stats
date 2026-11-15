<div id="jui-global-message" class="alert loading">Fetching Server Status and Information...</div>
<div class="mws-panel grid_2 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-camera"></i> Server Sponsor Logo</span>
    </div>
    <div class="mws-panel-body" style="text-align: center">
        <span class="thumbnail">
            <img id="server-image" src="/ASP/frontend/images/maps/default.png" style="max-height: 240px">
        </span>
    </div>
</div>
<div class="mws-panel grid_3 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-book"></i> Server Details</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="edit-details" href="#" class="btn">
                    <i class="icol-pencil"></i> Edit Details
                </a>
                <a id="refresh" href="#" class="btn"><i class="icol-arrow-refresh"></i> Re-Query</a>
                <a href="/ASP/servers/history/{server.id}" class="btn"><i class="icol-clock"></i> Round History</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-monitor"></i> Server Name</span>
                <span class="val">
                    <span class="text-nowrap"><span id="sName">{server.name}</span> (ID: <span id="serverId">{server.id}</span>)</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-globe"></i> Server Address</span>
                <span class="val">
					<span id="sAddress" class="text-nowrap">{server.ip}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-cord"></i> Game Port</span>
                <span class="val">
					<span id="sGamePort" class="text-nowrap">{server.gameport}</i></span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-direction"></i> Query Port</span>
                <span class="val">
					<span id="sQueryPort" class="text-nowrap">{server.queryport}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-history-2"></i> Last Seen</span>
                <span class="val">
					<span class="text-nowrap">{server.last_seen}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-broadcast"></i> Status</span>
                <span class="val">
					<span class="text-nowrap">
                        <label id="status" class="label label-inactive">Loading...</label>
                    </span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div class="mws-panel grid_3 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-business-card"></i> Server Ranked Information</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="go-btn" href="/ASP/providers/view/{server.provider_id}" class="btn">
                    <i class="icol-user-business-boss"></i> View Stats Provider
                </a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-user"></i> Provider Name</span>
                <span class="val">
                    <span id="currentAuthId" class="text-nowrap">{server.provider_name}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-check"></i> Ranked Status</span>
                <span class="val">
					<label id="authorized" class="label label-{server.server_auth_badge}">{server.server_auth_text}</label>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-pushpin"></i> Plasma Server</span>
                <span class="val">
                    <label id="plasma" class="label label-{server.plasma_badge}">{server.plasma_text}</label>
				</span>
            </li>
            <li>
                <span class="key" ><i class="icon-retweet"></i>
                    <span style="border-bottom: 1px dotted #000;" rel="tooltip" data-placement="right" title="Ranked Games by this Server">Games Processed</span>
                </span>
                <span class="val">
					<span class="text-nowrap">{server.snapshots}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-history-2"></i>
                    <span style="border-bottom: 1px dotted #000;" rel="tooltip" data-placement="right" title="By this server">Last Ranked Game</span>
                </span>
                <span class="val">
					<span class="text-nowrap">{server.last_update}</span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div id="graph" class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-graph"></i> Games Processed</span>
    </div>
    <div class="mws-panel-body">
        <div id="mws-line-chart" style="width:100%; height:360px; "></div>
        <div class="mws-form-row">
            <div style="text-align: center; margin-top: 15px">
                <div class="mws-form-item">
                    <div id="mws-ui-button-radio">
                        <input type="radio" id="weekRadio" name="radio" checked="checked"><label for="weekRadio">Last Week</label>
                        <input type="radio" id="monthRadio" name="radio"><label for="monthRadio">Last 6 Weeks</label>
                        <input type="radio" id="yearRadio" name="radio"><label for="yearRadio">Last Year</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="details"></div>

<!-- Edit Server Ajax Model -->
<div id="edit-server-form">
    <form id="mws-validate-server" class="mws-form" method="post" action="/ASP/servers/edit">
        <input id="post-action" type="hidden" name="action" value="edit">
        <input id="server-id" type="hidden" name="serverId" value="{server.id}">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div id="jui-message" class="alert" style="display: none;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Server Name</label>
                <div class="mws-form-item">
                    <input type="text" name="serverName" class="required large" value="{server.name}">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Server IPv4 Address</label>
                <div class="mws-form-item">
                    <input type="text" name="serverIp" class="required large" value="{server.ip}">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Game Port</label>
                <div class="mws-form-item">
                    <input type="text" id="s1" name="serverPort" class="required mws-spinner"  value="{server.gameport}">
                    <label for="s1" class="error" generated="true" style="display:none"></label>
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Query Port</label>
                <div class="mws-form-item">
                    <input type="text" id="s2" name="serverQueryPort" class="required mws-spinner" value="{server.queryport}">
                    <label for="s2" class="error" generated="true" style="display:none"></label>
                </div>
            </div>
        </div>
    </form>
</div>