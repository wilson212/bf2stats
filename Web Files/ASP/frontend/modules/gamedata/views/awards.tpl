<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-trophy-2"></i> Awards</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new" href="#" class="btn"><i class="icol-add"></i> Add New Award</a>
                <a id="refresh" href="#" class="btn"><i class="icol-arrow-refresh"></i> Refresh Table</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width: 7%;">Award ID</th>
                    <th>Name</th>
                    <th>Snapshot Code</th>
                    <th>Type</th>
                    <th>Times Awarded</th>
                    <th>Backend Awarded</th>
                    <th style="width: 7%;">Actions</th>
                </tr>
            </thead>
            <tody>
                {awards}
                <tr id="tr-award-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>{code}</td>
                    <td>{type}</td>
                    <td>{count}</td>
                    <td>{backend}</td>
                    <td>
                        <span class="btn-group">
                            <a id="edit-{id}" href="#"  rel="tooltip" title="Edit Award" class="btn btn-small"><i class="icon-pencil"></i></a>
                            <a id="delete-{id}" href="#" rel="tooltip" title="Delete Award" class="btn btn-small"><i class="icon-trash"></i></a>
                        </span>
                    </td>
                </tr>
                {/awards}
            </tody>
        </table>
    </div>
</div>

<!-- Add New Player Ajax Model -->
<div id="editor-form">
    <form id="mws-validate" class="mws-form" method="post" action="/ASP/gamedata/addAward">
        <input id="post-action" type="hidden" name="action" value="add">
        <input id="originalId" type="hidden" name="originalId" value="0">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div id="jui-message" class="alert" style="display: none; width: 92%; margin-bottom: 20px;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Award Name</label>
                <div class="mws-form-item">
                    <input type="text" name="awardName" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Snapshot Code</label>
                <div class="mws-form-item">
                    <input type="text" name="awardCode" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Backend Awarded</label>
                <div class="mws-form-item">
                    <select id="awardBackend" name="awardBackend" class="large required" title="">
                        <option value="0">False</option>
                        <option value="1">True</option>
                    </select>
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Award Type</label>
                <div class="mws-form-item">
                    <select id="awardType" name="awardType" class="large required" title="">
                        <option value="0">Ribbon</option>
                        <option value="1">Badge</option>
                        <option value="2">Medal</option>
                    </select>
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Award Unique ID</label>
                <div class="mws-form-item">
                    <input type="text" id="awardId" name="awardId" class="required mws-spinner" value="">
                    <label for="awardId" class="error" generated="true" style="display:none"></label>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Award Confirmation Model -->
<div id="mws-jui-dialog">
    <div class="mws-dialog-inner"></div>
</div>