<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Registered Stats Servers</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
            <tr>
                <th style="width: 4%">Id</th>
                <th>Server Name</th>
                <th>Provider</th>
                <th style="width: 12%">IP Address</th>
                <th style="width: 7%">Game Port</th>
                <th style="width: 7%">Query Port</th>
                <th style="width: 6%">Games</th>
                <th style="width: 8%">Address Status</th>
                <th style="width: 6%">Plasma</th>
                <th style="width: 9%">Actions</th>
            </tr>
            </thead>
            <tbody>
            {servers}
            <tr id="tr-server-{id}">
                <td>{id}</td>
                <td>{name}</td>
                <td>{provider_name}</td>
                <td>{ip}</td>
                <td>{gameport}</td>
                <td>{queryport}</td>
                <td>{snapshots}</td>
                <td><span class="badge badge-{auth_badge}">{auth_text}</span></td>
                <td><span class="badge badge-{plasma_badge}">{plasma_text}</span></td>
                <td>
                    <span class="btn-group">
                        <a id="view-btn-{id}" href="/ASP/servers/view/{id}" rel="tooltip" title="View Server" class="btn btn-small"><i class="icon-eye-open"></i></a>
                        <a id="edit-btn-{id}" href="#"  rel="tooltip" title="Edit Server" class="btn btn-small"><i class="icon-pencil"></i></a>
                        <a id="go-btn-{id}" href="/ASP/providers/view/{provider_id}" rel="tooltip" title="View Provider" class="btn btn-small"><i class="icon-business-card"></i></a>
                        <a id="delete-btn-{id}" href="#" rel="tooltip" title="Delete Server" class="btn btn-small"
                        <?php echo ({snapshots} == 0) ? '' : ' disabled="disabled"'; ?>><i class="icon-trash"></i></a>
                    </span>
                </td>
            </tr>
            {/servers}
            </tbody>
        </table>

        <!-- Add New Server Ajax Model -->
        <div id="add-server-form">
            <form id="mws-validate" class="mws-form" method="post" action="/ASP/servers/edit">
                <input id="post-action" type="hidden" name="action" value="edit">
                <input id="server-id" type="hidden" name="serverId" value="0">
                <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
                <div id="jui-message" class="alert" style="display: none;"></div>
                <div class="mws-form-inline">
                    <div class="mws-form-row">
                        <label class="mws-form-label">Server Name</label>
                        <div class="mws-form-item">
                            <input type="text" name="serverName" class="required large">
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label">Server IPv4 Address</label>
                        <div class="mws-form-item">
                            <input type="text" name="serverIp" class="required large">
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label">Game Port</label>
                        <div class="mws-form-item">
                            <input type="text" id="s1" name="serverPort" class="required mws-spinner" value="16567">
                            <label for="s1" class="error" generated="true" style="display:none"></label>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label">Query Port</label>
                        <div class="mws-form-item">
                            <input type="text" id="s2" name="serverQueryPort" class="required mws-spinner" value="29900">
                            <label for="s2" class="error" generated="true" style="display:none"></label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Delete Server Confirmation Model -->
        <div id="mws-jui-dialog">
            <div class="mws-dialog-inner"></div>
        </div>
    </div>
</div>