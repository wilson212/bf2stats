<div id="jui-global-message" style="display: none"></div>
<div class="mws-panel grid_3 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-business-card"></i> Stats Provider Information</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="auth-provider" href="#" class="btn" <?php echo ({provider.authorized} == 0) ? '' : ' style="display: none"'; ?>>
                    <i class="icol-accept"></i> Authorize
                </a>
                <a id="unauth-provider" href="#" class="btn" <?php echo ({provider.authorized} == 1) ? '' : ' style="display: none"'; ?>>
                    <i class="icol-cross-shield-2"></i> Un-Authorize
                </a>
                <a id="plasma-provider" href="#" class="btn" <?php echo ({provider.plasma} == 0) ? '' : ' style="display: none"'; ?>>
                <i class="icol-sort-date"></i> Plasma
                </a>
                <a id="unplasma-provider" href="#" class="btn" <?php echo ({provider.plasma} == 1) ? '' : ' style="display: none"'; ?>>
                    <i class="icol-sort"></i> Un-Plasma
                </a>
                <a href="/ASP/providers/history/{provider.id}" class="btn"><i class="icol-clock"></i> Round History</a>
                <a id="auth-addresses" href="#" class="btn dropdown-toggle" data-toggle="dropdown">
                    <i class="icol-cog"></i> Options
                    <span class="caret"></span>
                </a>

                <ul id="dropdown" class="dropdown-menu pull-right" data-toggle="dropdown">
                    <li><a id="edit-details" href="#"><i class="icol-pencil"></i> Edit Details</a></li>
                    <li><a id="edit-addresses" href="#"><i class="icol-application-osx-terminal"></i> Edit IP Addresses</a></li>
                    <li class="divider"></li>
                    <li><a id="gen-auth-id" href="#"><i class="icol-star-2"></i> Generate New Auth ID</a></li>
                    <li><a id="gen-auth-token" href="#"><i class="icol-key"></i> Generate New Auth Token</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-globe"></i> Provider Name</span>
                <span class="val">
                    <span id="sName" class="text-nowrap">{provider.name}</span> (ID: <span id="providerId">{provider.id}</span>)</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-user"></i> Auth ID</span>
                <span class="val">
                    <span id="currentAuthId" class="text-nowrap">{provider.auth_id}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-key-2"></i> Auth Token</span>
                <span class="val">
					<span id="currentAuthToken" class="text-nowrap">{provider.auth_token}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-check"></i> Ranked Status</span>
                <span class="val">
					<label id="authorized" class="label label-{provider.auth_badge}">{provider.auth_text}</label>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-pushpin"></i> Plasma Servers</span>
                <span class="val">
                    <label id="plasma" class="label label-{provider.plasma_badge}">{provider.plasma_text}</label>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-server"></i> Game Server IP's</span>
                <span id="addresses" class="val">
                {addresses}
                    <label class="label label-info">{value}</label>
                {/addresses}
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-history-2"></i> Last Ranked Game</span>
                <span class="val">
					<span class="text-nowrap">{provider.last_update}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<!-- Graph -->
<div id="graph" class="mws-panel grid_5 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-graph"></i> Games Processed</span>
    </div>
    <div class="mws-panel-body">
        <div id="mws-line-chart" style="width:100%; height:255px; "></div>
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

<div class="mws-panel grid_8 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Registered Stats Servers</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
            <tr>
                <th style="width: 4%">Id</th>
                <th>Server Name</th>
                <th style="width: 11%">Server IP Address</th>
                <th style="width: 7%">Game Port</th>
                <th style="width: 7%">Query Port</th>
                <th style="width: 8%">Address Status</th>
                <th style="width: 12%">Last Ranked Game</th>
                <th style="width: 6%">Games</th>
                <th style="width: 6%">Actions</th>
            </tr>
            </thead>
            <tbody>
            {servers}
                <tr id="tr-server-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>{ip}</td>
                    <td>{gameport}</td>
                    <td>{queryport}</td>
                    <td><span class="badge badge-{auth_badge}">{auth_text}</span></td>
                    <td data-order="{lastupdate}">{last_update}</td>
                    <td>{snapshots}</td>
                    <td>
                    <span class="btn-group">
                        <a id="go-btn" href="/ASP/servers/view/{id}" rel="tooltip" title="View Server" class="btn btn-small"><i class="icon-eye-open"></i></a>
                        <a id="delete-btn-{id}" href="#" rel="tooltip" title="Delete Server" class="btn btn-small"
                        <?php echo ({snapshots} == 0) ? '' : ' disabled="disabled"'; ?>><i class="icon-trash"></i></a>
                    </span>
                    </td>
                </tr>
            {/servers}
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Server Ajax Model -->
<div id="edit-provider-form">
    <form id="mws-validate-provider" class="mws-form" method="post" action="/ASP/providers/add">
        <input id="post-action" type="hidden" name="action" value="edit">
        <input id="provider-id" type="hidden" name="providerId" value="{provider.id}">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div id="jui-message" class="alert" style="display: none;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Provider Name</label>
                <div class="mws-form-item">
                    <input type="text" name="providerName" class="required large" value="{provider.name}">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Edit Token Addresses Ajax Model -->
<div id="edit-token-form">
    <form id="mws-validate-token" class="mws-form" method="post" action="/ASP/providers/token">
        <input id="token-action" type="hidden" name="action" value="address">
        <input id="token-id" type="hidden" name="providerId" value="{provider.id}">
        <div id="mws-validate-error2" class="mws-form-message error" style="display:none;"></div>
        <div id="jui-message2" class="alert" style="display: none;"></div>
        <div class="mws-form-row">
            CIDR ranges are supported.
        </div>
        <div class="mws-form-row">
            <label class="mws-form-label">Authorized Server Ip Addresses</label>
            <div class="mws-form-item">
                <select id="ips" name="items" multiple data-role="tagsinput"></select>
            </div>
        </div>
    </form>
</div>

<!-- Change Auth Confirmation Model -->
<div id="mws-jui-dialog">
    <div class="mws-dialog-inner"></div>
</div>