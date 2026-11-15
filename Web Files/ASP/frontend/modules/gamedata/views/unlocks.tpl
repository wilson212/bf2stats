<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-gun"></i> Unlocks</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new" href="#" class="btn"><i class="icol-add"></i> Add New Unlock</a>
                <a id="refresh" href="#" class="btn"><i class="icol-arrow-refresh"></i> Refresh Table</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
            <tr>
                <th style="width: 7%;">Unlock ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Kit</th>
                <th>Required Unlock</th>
                <th style="width: 7%;">Actions</th>
            </tr>
            </thead>
            <tbody>
                {unlocks}
                <tr id="tr-unlock-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>{desc}</td>
                    <td>{kitname}</td>
                    <td>{reqname}</td>
                    <td>
                        <span class="btn-group">
                            <a id="edit-{id}" href="#"  rel="tooltip" title="Edit Award" class="btn btn-small"><i class="icon-pencil"></i></a>
                            <a id="delete-{id}" href="#" rel="tooltip" title="Delete Award" class="btn btn-small"><i class="icon-trash"></i></a>
                        </span>
                    </td>
                </tr>
                {/unlocks}
            </tbody>
        </table>
    </div>
</div>

<!-- Add New/Edit Ajax Model -->
<div id="editor-form">
    <form id="mws-validate" class="mws-form" method="post" action="/ASP/gamedata/addUnlock">
        <input id="post-action" type="hidden" name="action" value="add">
        <input id="originalId" type="hidden" name="originalId" value="0">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div id="jui-message" class="alert" style="display: none; width: 92%; margin-bottom: 20px;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Unlock ID</label>
                <div class="mws-form-item">
                    <input type="text" id="unlockId" name="unlockId" class="required mws-spinner" value="">
                    <label for="unlockId" class="error" generated="true" style="display:none"></label>
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Unlock Name</label>
                <div class="mws-form-item">
                    <input type="text" name="unlockName" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Unlock Desc</label>
                <div class="mws-form-item">
                    <input type="text" name="unlockDesc" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Kit</label>
                <div class="mws-form-item">
                    <select id="unlockKit" name="unlockKit" class="large required" title="">
                    {kits}
                        <option value="{id}">{name}</option>
                    {/kits}
                    </select>
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Requires Unlock</label>
                <div class="mws-form-item">
                    <select id="unlockRequired" name="unlockRequired" class="large required" title="">
                        <option value="0">None</option>
                        {unlocks}
                            <option value="{id}">{name}</option>
                        {/unlocks}
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Confirmation Model -->
<div id="mws-jui-dialog">
    <div class="mws-dialog-inner"></div>
</div>