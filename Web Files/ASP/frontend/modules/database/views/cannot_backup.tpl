<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-box-remove"></i> Backup Database</span>
    </div>
    <div class="mws-panel-body">
        <div id="install-error" style="text-align: center; margin: 10px 0;">
            <img src="/ASP/frontend/modules/install/images/Cross.png" style="margin-top: 10px">
            <div style="font-size: 200%; font-weight: bold; margin-top: 20px">
                Database cannot be backed up using the ASP when the database is not on the same host!!!
            </div>
            <div style="font-size: 120%; margin-top: 20px">
                Either place the ASP on the same server as the database or backup the database using a MySQL editor.
            </div>
            <br /><br />
            <input type="button" id="button-to-home" class="btn btn-success" value="Return To Home" data-target="/ASP/">
        </div>
    </div>
</div>