<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-arrow-up-right"></i> Update Database</span>
    </div>
    <div class="mws-panel-body">
        <!-- Initial Message -->
        <div id="initial-message">
            This option allows you to update your "Gamespy" Database schema to match that of the current ASP version.
            Please ensure you have a full backup of your database BEFORE proceeding!!<br /><br />
            <span style="color: red; ">WARNING:</span> Until the database schema is fully updated, you may experience errors
            when trying to process or view statistics.
            <br /><br />
            <div style="text-align: center;">
                <button class="btn btn-primary" type="button" id="update">Update Database Schema</button>
            </div>
        </div>

        <!-- Update Success Message -->
        <div id="update-success" style="text-align: center; margin: 10px 0; display: none">
            <img src="/ASP/frontend/modules/install/images/Check.png" style="margin-top: 10px">
            <div style="font-size: 200%; font-weight: bold; margin-top: 20px">
                The Database schema is currently up to date!
            </div>
            <div style="font-size: 120%; margin-top: 20px">
                Good job soldier! Your Battlefield 2 ASP Stats System is ready to use again!
            </div>
            <br /><br />
            <input type="button" id="button-to-home" class="btn btn-success" value="Return To Home" data-target="/ASP/">
        </div>

        <!-- Update Failed -->
        <div id="update-failed" style="text-align: center; margin: 10px 0; display: none;">
            <img src="/ASP/frontend/modules/install/images/Cross.png" style="margin-top: 10px">
            <div style="font-size: 200%; font-weight: bold; margin-top: 20px">
                Database Schema Update Failed!
            </div>
            <div id="fail-message" style="font-size: 120%; margin-top: 20px"></div>
            <br /><br />
            <input type="button" id="button-to-start-over" class="btn btn-danger" value="Start Over" data-target="/ASP/database/update">
        </div>
    </div>
</div>

<!-- Add New Item Ajax Model -->
<div id="ajax-dialog">
    <div class="mws-dialog-inner">
        <div style="text-align: center;">
            <img src="/ASP/frontend/images/core/loading32.gif" />
            <br />
            <br />
            Updating Database Schema... Please allow up to 30 seconds for this process to complete.
            <br />
            <br />
            <span style="color: red; ">DO NOT</span> refresh this window.
        </div>
    </div>
</div>