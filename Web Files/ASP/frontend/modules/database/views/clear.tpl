<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-trash"></i> Clear Database</span>
    </div>
    <div class="mws-panel-body">
        This option allows you to clear your "Gamespy" Database of ALL collected statistics data. Please ensure you have a full backup of your database BEFORE proceeding!!<br /><br />
        <span style="color: red; ">WARNING:</span> This will destroy ALL existing statistics!! Use with EXTREME caution!!!
    </div>
    <div class="mws-panel-body no-padding">
        <form id="clearDatabase" class="mws-form" action="/ASP/database/clear" method="post">
            <input id="action" type="hidden" name="action" value="clear">
            <fieldset class="mws-form">
                <legend>Clearing Options (Stats data on these tables will still be reset).</legend>
                <div class="mws-form-row">
                    <div class="mws-form-item clearfix">
                        <ul class="mws-form-list">
                            <li><input name="accounts" type="checkbox" checked="checked"> <label>Preserve Player Accounts</label></li>
                            <li><input name="providers" type="checkbox" checked="checked"> <label>Preserve Providers and Auth ID's/Tokens</label></li>
                            <li><input name="servers" type="checkbox" checked="checked"> <label>Preserve Servers</label></li>
                        </ul>
                    </div>
                </div>
            </fieldset>
            <div class="mws-button-row">
                <input type="submit" value="Submit" class="btn btn-danger">
            </div>
        </form>
    </div>
</div>

<!-- Add New Item Ajax Model -->
<div id="ajax-dialog">
    <div class="mws-dialog-inner">
        <div style="text-align: center;">
            <img src="/ASP/frontend/images/core/loading32.gif" />
            <br />
            <br />
            Clearing Stats Data... Please allow up to 30 seconds for this process to complete.
            <br />
            <br />
            <span style="color: red; ">DO NOT</span> refresh this window.
        </div>
    </div>
</div>