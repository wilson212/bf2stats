<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-loading"></i> Snapshots Awaiting Authorization</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="accept-selected" href="#" class="btn"><i class="icol-accept"></i> Accept Selected</a>
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
                <th>Server Name</th>
                <th>Server IP</th>
                <th style="width: 7%">Server Port</th>
                <th>Map Played</th>
                <th>Players</th>
                <th>Date</th>
                <th style="width: 7px;">Actions</th>
            </tr>
            </thead>
            <tbody>
            {snapshots}
                <tr id="snapshot-{name}">
                    <td class="checkbox-column">
                        <input id="snapshot-{name}" type="checkbox">
                    </td>
                    <td>{server}</td>
                    <td>{ipaddress}</td>
                    <td>{port}</td>
                    <td>{map}</td>
                    <td>{players}</td>
                    <td>{date}</td>
                    <td>
                        <span class="btn-group">
                            <a id="view-btn-{name}" href="/ASP/snapshots/view/{name}" target="_blank" rel="tooltip" title="View Snapshot" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a id="delete-btn-{name}" href="#" rel="tooltip" title="Delete Snapshot" class="btn btn-small">
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

<!-- Ajax Model -->
<div id="ajax-dialog">
    <div class="mws-dialog-inner">
        <div style="text-align: center;">
            <img src="/ASP/frontend/images/core/loading32.gif" />
            <br />
            <br />
            Importing Snapshots... Please wait
            <br />
            <br />
            Processing snapshot <span id="progress">0</span> of <span id="count">0</span>.
            <br />
            <br />
            <span style="color: red; ">DO NOT</span> refresh this window.
        </div>
    </div>
</div>