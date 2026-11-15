<div id="jui-message" style="display: none;"></div>
<div class="alert info">
    <span class="close-bt"></span>
    Here you can specify stat keys to match that of the Battlefield 2 server's constants.py file, which allows for a
    mod support. Please note that you cannot delete hardcoded stats keys in the base Battlefield 2 game.
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-flag"></i> Armies</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new-army" href="#" class="btn"><i class="icol-add"></i> Add New Army</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="army"  class="mws-table">
            <thead>
            <tr>
                <th style="width: 7%;">ID</th>
                <th>Name</th>
                <th style="width: 10%;">Actions</th>
            </tr>
            </thead>
            <tody>
                {armies}
                <tr id="tr-army-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>
                        <span class="btn-group">
                            <a id="edit-army-{id}" href="#"  rel="tooltip" title="Edit Name" class="btn btn-small"><i class="icon-pencil"></i></a>
                            <a id="{bid}-army-{id}" href="#" rel="tooltip" title="{title}" class="btn btn-small"><i class="icon-trash"></i></a>
                        </span>
                    </td>
                </tr>
                {/armies}
            </tody>
        </table>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-accessibility-2"></i> Kit Types</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new-kit" href="#" class="btn"><i class="icol-add"></i> Add New Kit Type</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="kit"  class="mws-table">
            <thead>
            <tr>
                <th style="width: 7%;">ID</th>
                <th>Name</th>
                <th style="width: 10%;">Actions</th>
            </tr>
            </thead>
            <tody>
                {kits}
                <tr id="tr-kit-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>
                        <span class="btn-group">
                            <a id="edit-kit-{id}" href="#"  rel="tooltip" title="Edit Name" class="btn btn-small"><i class="icon-pencil"></i></a>
                            <a id="{bid}-kit-{id}" href="#" rel="tooltip" title="{title}" class="btn btn-small"><i class="icon-trash"></i></a>
                        </span>
                    </td>
                </tr>
                {/kits}
            </tody>
        </table>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-sign-post"></i> Game Modes</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new-game_mode" href="#" class="btn"><i class="icol-add"></i> Add New Game Mode</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="game_mode"  class="mws-table">
            <thead>
            <tr>
                <th style="width: 7%;">ID</th>
                <th>Name</th>
                <th style="width: 10%;">Actions</th>
            </tr>
            </thead>
            <tody>
                {modes}
                    <tr id="tr-vehicle-{id}">
                        <td>{id}</td>
                        <td>{name}</td>
                        <td>
                        <span class="btn-group">
                            <a id="edit-game_mode-{id}" href="#"  rel="tooltip" title="Edit Name" class="btn btn-small"><i class="icon-pencil"></i></a>
                            <a id="{bid}-game_mode-{id}" href="#" rel="tooltip" title="{title}" class="btn btn-small"><i class="icon-trash"></i></a>
                        </span>
                        </td>
                    </tr>
                {/modes}
            </tody>
        </table>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-steering-wheel"></i> Vehicle Types</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new-vehicle" href="#" class="btn"><i class="icol-add"></i> Add New Vehicle Type</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="vehicle"  class="mws-table">
            <thead>
            <tr>
                <th style="width: 7%;">ID</th>
                <th>Name</th>
                <th style="width: 10%;">Actions</th>
            </tr>
            </thead>
            <tody>
                {vehicles}
                <tr id="tr-vehicle-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>
                        <span class="btn-group">
                            <a id="edit-vehicle-{id}" href="#"  rel="tooltip" title="Edit Name" class="btn btn-small"><i class="icon-pencil"></i></a>
                            <a id="{bid}-vehicle-{id}" href="#" rel="tooltip" title="{title}" class="btn btn-small"><i class="icon-trash"></i></a>
                        </span>
                    </td>
                </tr>
                {/vehicles}
            </tody>
        </table>
    </div>
</div>
<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span><i class="icon-rocket"></i> Weapon Types</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new-weapon" href="#" class="btn"><i class="icol-add"></i>  Add New Weapon Type</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="weapon" class="mws-table">
            <thead>
            <tr>
                <th style="width: 7%;">ID</th>
                <th>Name</th>
                <th style="width: 10%;">Actions</th>
            </tr>
            </thead>
            <tody>
                {weapons}
                <tr id="tr-weapon-{id}">
                    <td>{id}</td>
                    <td>{name}</td>
                    <td>
                        <span class="btn-group">
                            <a id="edit-weapon-{id}" href="#"  rel="tooltip" title="Edit Name" class="btn btn-small"><i class="icon-pencil"></i></a>
                            <a id="{bid}-weapon-{id}" href="#" rel="tooltip" title="{title}" class="btn btn-small"><i class="icon-trash"></i></a>
                        </span>
                    </td>
                </tr>
                {/weapons}
            </tody>
        </table>
    </div>
</div>

<!-- Add New Item Ajax Model -->
<div id="editor-form">
    <form id="mws-validate" class="mws-form" method="post" action="/ASP/gamedata/add">
        <input id="post-action" type="hidden" name="action" value="add">
        <input id="itemId" type="hidden" name="itemId" value="0">
        <input id="itemType" type="hidden" name="itemType" value="">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Item Name</label>
                <div class="mws-form-item">
                    <input type="text" name="itemName" class="required large" title="">
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Delete Server Confirmation Model -->
<div id="mws-jui-dialog">
    <div class="mws-dialog-inner">
        Are you sure you want to delete this item? Deleting stats keys from the database can erase huge portions
        of player history and data. <b>This action cannot be undone. Are you absolutely sure?</b>
    </div>
</div>
