<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-table"></i> Ranked Server Providers</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new" href="#" class="btn"><i class="icol-add"></i> Add New Provider</a>
                <a id="delete-selected" href="#" class="btn"><i class="icol-cross"></i> Delete Selected</a>
                <a id="auth-selected" href="#" class="btn"><i class="icol-accept"></i> Authorize Selected</a>
                <a id="unauth-selected" href="#" class="btn"><i class="icol-cross-shield-2"></i> Un-Authorize Selected</a>
                <a id="plasma-selected" href="#" class="btn"><i class="icol-sort-date"></i> Plasma Selected</a>
                <a id="unplasma-selected" href="#" class="btn"><i class="icol-sort"></i> Un-Plasma Selected</a>
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
                <th style="width: 4%">Id</th>
                <th>Provider Name</th>
                <th style="width: 5%">Auth ID</th>
                <th style="width: 12%">Auth Token</th>
                <th style="width: 10%">Games Processed</th>
                <th style="width: 8%">Ranked Status</th>
                <th style="width: 6%">Plasma</th>
                <th style="width: 7%">Actions</th>
            </tr>
            </thead>
            <tbody>
            {providers}
                <tr id="tr-provider-{id}">
                    <td class="checkbox-column">
                        <input id="provider-{id}" type="checkbox">
                    </td>
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>{auth_id}</td>
                    <td>{auth_token}</td>
                    <td>{snapshots}</td>
                    <td><span id="tr-auth-{id}" class="badge badge-{auth_badge}">{auth_text}</span></td>
                    <td><span id="tr-plasma-{id}" class="badge badge-{plasma_badge}">{plasma_text}</span></td>
                    <td>
                    <span class="btn-group">
                        <a id="go-btn" href="/ASP/providers/view/{id}" rel="tooltip" title="View Provider" class="btn btn-small"><i class="icon-eye-open"></i></a>
                        <a id="edit-btn-{id}" href="#"  rel="tooltip" title="Edit Provider" class="btn btn-small"><i class="icon-pencil"></i></a>
                        <a id="auth-btn-{id}" href="#" rel="tooltip" title="Authorize Provider" class="btn btn-small"
                        <?php echo ({authorized} == 0) ? '' : ' style="display: none"'; ?>><i class="icon-ok"></i></a>
                        <a id="unauth-btn-{id}" href="#" rel="tooltip" title="Un-Authorize Provider" class="btn btn-small"
                        <?php echo ({authorized} == 1) ? '' : ' style="display: none"'; ?>><i class="icon-unlink"></i></a>
                        <a id="delete-btn-{id}" href="#" rel="tooltip" title="Delete Provider" class="btn btn-small"
                        <?php echo ({snapshots} == 0) ? '' : ' disabled="disabled"'; ?>><i class="icon-trash"></i></a>
                    </span>
                    </td>
                </tr>
            {/providers}
            </tbody>
        </table>

        <!-- Add New Provider Ajax Model -->
        <div id="add-provider-form">
            <form id="mws-validate" class="mws-form" method="post" action="/ASP/providers/add">
                <input id="post-action" type="hidden" name="action" value="add">
                <input id="server-id" type="hidden" name="providerId" value="0">
                <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
                <div id="jui-message" class="alert" style="display: none;"></div>
                <div class="mws-form-inline">
                    <div class="mws-form-row">
                        <label class="mws-form-label">Provider Name</label>
                        <div class="mws-form-item">
                            <input type="text" name="providerName" class="required large">
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