<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-eye-open"></i> BattleSpy Reports</span>
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
                    <th style="width: 5%">ID</th>
                    <th>Date</th>
                    <th>Server Name</th>
                    <th>Map Name</th>
                    <th style="width: 7%">Messages</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>
            <tbody>
            {reports}
                <tr id="tr-report-{id}">
                    <td class="checkbox-column">
                        <input id="report-{id}" type="checkbox">
                    </td>
                    <td>{id}</td>
                    <td>{date}</td>
                    <td>{server}</td>
                    <td>{mapname}</td>
                    <td>{count}</td>
                    <td>
                        <span class="btn-group">
                            <a id="go-btn" href="/ASP/battlespy/report/{id}" rel="tooltip" title="View Report" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a id="delete-btn-{id}" href="#" rel="tooltip" title="Delete Report" class="btn btn-small">
                                <i class="icon-trash"></i>
                            </a>
                        </span>
                    </td>
                </tr>
            {/reports}
            </tbody>
        </table>

        <!-- Delete Server Confirmation Model -->
        <div id="mws-jui-dialog">
            <div class="mws-dialog-inner"></div>
        </div>
    </div>
</div>