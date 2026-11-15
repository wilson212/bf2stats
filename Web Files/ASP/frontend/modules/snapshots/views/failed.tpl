<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-warning-sign"></i> Failed Snapshots</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="delete-selected" href="#" class="btn"><i class="icol-cross"></i> Delete Selected</a>
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
                    <th style="width: 11%">Date</th>
                    <th style="width: 20%">Server Name</th>
                    <th style="width: 38%">Reason Failed</th>
                    <th style="width: 22%">Snapshot File Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            {snapshots}
                <tr id="snapshot-{id}">
                    <td class="checkbox-column">
                        <input id="snapshot-{id}" type="checkbox">
                    </td>
                    <td>{date}</td>
                    <td>{server_name}</td>
                    <td>{reason}</td>
                    <td>{filename}</td>
                    <td>
                        <span class="btn-group">
                            <a id="view-btn-{server_id}" href="/ASP/servers/view/{server_id}" rel="tooltip" title="View Server" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a id="delete-btn-{id}" href="#" rel="tooltip" title="Delete Snapshot" class="btn btn-small">
                                <i class="icon-trash"></i>
                            </a>
                        </span>
                    </td>
                </tr>
            {/snapshots}
            </tbody>
        </table>

        <!-- Delete Server Confirmation Model -->
        <div id="mws-jui-dialog">
            <div class="mws-dialog-inner"></div>
        </div>
    </div>
</div>