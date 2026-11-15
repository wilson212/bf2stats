<div id="jui-global-message" class="alert info">
    <span class="close-bt"></span>
    Here you can assign which mods server's are allowed to play with stats enabled.
    If a server plays a mod not listed here, it will be treated as Un-authorized.
    Be sure the server's constants.py file also is up to date to include authorized mod data.
</div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-sign-post"></i> Game Mods</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new" href="#" class="btn"><i class="icol-add"></i> Add New Game Mod</a>
                <a id="refresh" href="#" class="btn"><i class="icol-arrow-refresh"></i> Refresh Table</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
            <tr>
                <th style="width: 7%;">Mod ID</th>
                <th>Short Name</th>
                <th>Long Name</th>
                <th>Status</th>
                <th style="width: 5%;">Actions</th>
            </tr>
            </thead>
            <tody>
            {mods}
                <tr id="tr-mod-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>{longname}</td>
                    <td>
                        <span id="status-{id}" class="badge badge-{status_badge}">{status_text}</span>
                    </td>
                    <td>
                        <span class="btn-group">
                            <a id="edit-{id}" href="#"  rel="tooltip" title="Edit Details" class="btn btn-small">
                                <i class="icon-pencil"></i>
                            </a>
                        </span>
                    </td>
                </tr>
            {/mods}
            </tody>
        </table>
    </div>
</div>

<!-- Add New/Edit Ajax Model -->
<div id="editor-form">
    <form id="mws-validate" class="mws-form" method="post" action="/ASP/gamedata/addMod">
        <input id="post-action" type="hidden" name="action" value="add">
        <input id="originalId" type="hidden" name="originalId" value="0">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div id="jui-message" class="alert" style="display: none; width: 92%; margin-bottom: 20px;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Short Name</label>
                <div class="mws-form-item">
                    <input type="text" name="shortName" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Long Name</label>
                <div class="mws-form-item">
                    <input type="text" name="longName" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Status</label>
                <div class="mws-form-item">
                    <select id="authorized" name="authorized" class="large required" title="">
                        <option value="1">Authorized</option>
                        <option value="0">Not Authorized</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>