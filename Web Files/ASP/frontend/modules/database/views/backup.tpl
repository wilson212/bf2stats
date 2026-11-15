<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-box-remove"></i> Backup Database</span>
    </div>
    <div class="mws-panel-body">
        <p>
            This option allows you backup your "Gamespy" Statistics Database tables. This does not backup the database schema, just the data. To restore,
            simply reload the relevant database schema and import the latest backup files.<br /><br />
            <span style="color: red; ">IMPORTANT:</span> This does not replace a proper MySQL Backup Job, but it does save your data for later recovery.
            <br /><br />
            <div style="text-align: center;">
                <button class="btn btn-primary" type="button" id="backup">Backup Database Tables</button>
            </div>
        </p>
    </div>
</div>

<!-- Add New Item Ajax Model -->
<div id="ajax-dialog">
    <div class="mws-dialog-inner">
        <div style="text-align: center;">
            <img src="/ASP/frontend/images/core/loading32.gif" />
            <br />
            <br />
            Backing Up System Table Data... Please allow up to 30 seconds for this process to complete.
            <br />
            <br />
            <span style="color: red; ">DO NOT</span> refresh this window.
        </div>
    </div>
</div>