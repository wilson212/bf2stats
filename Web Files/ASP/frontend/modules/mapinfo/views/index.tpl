<div id="jui-message" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-flag"></i> Maps Played</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width: 5%">ID</th>
                    <th>Folder Name</th>
                    <th>Display Name</th>
                    <th>Total Score</th>
                    <th>Total Time Played</th>
                    <th style="width: 9%">Games Played</th>
                    <th style="width: 9%">Total Kills</th>
                    <th style="width: 9%">Total Deaths</th>
                    <th style="width: 5%">Actions</th>
                </tr>
            </thead>
            <tody>
            {maps}
                <tr id="tr-map-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>{displayname}</td>
                    <td>{score}</td>
                    <td data-sort="{time}">{time_display}</td>
                    <td>{count}</td>
                    <td>{kills}</td>
                    <td>{deaths}</td>
                    <td>
                        <span class="btn-group">
                            <a id="go-map-{id}" href="/ASP/mapinfo/view/{id}"  rel="tooltip" title="View Map" class="btn btn-small">
                                <i class="icon-eye-open"></i>
                            </a>
                            <a id="edit-map-{id}" href="#"  rel="tooltip" title="Edit Map Name" class="btn btn-small">
                                <i class="icon-pencil"></i>
                            </a>
                        </span>
                    </td>
                </tr>
            {/maps}
            </tody>
        </table>
    </div>
</div>

<!-- Edit Map Name Ajax Model -->
<div id="editor-form">
    <form id="mws-validate" class="mws-form" method="post" action="/ASP/mapinfo/edit">
        <input id="post-action" type="hidden" name="action" value="edit">
        <input id="mapId" type="hidden" name="mapId" value="0">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Map Display Name</label>
                <div class="mws-form-item">
                    <input type="text" name="mapName" class="required large" title="">
                </div>
            </div>
        </div>
    </form>
</div>
