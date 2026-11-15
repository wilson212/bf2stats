<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-install"></i> Restore Database</span>
    </div>
    <div class="mws-panel-body">
        <p>
            This option allows you restore your "Gamespy" Statistics Database tables from a previous backup.
            This does not restore the database schema, just the data. Before you restore the data, please ensure you
            have loaded the relevant database schema. As part of this process, ALL existing data will be lost!
            <br /><br />
            <span style="color: black; "><b>Note:</b></span>
            Only backups that were made using the <b><u>current database version</b></u> will show in this list.
            <br /><br />
            <span style="color: red; "><b>Warning:</b></span>
            Running this script will CLEAR ALL data from your existing database, please ensure you have a proper backup BEFORE proceeding.
            <br /><br />
            <form id="restore-form" class="mws-form" method="post" action="/ASP/database/restore">
                <input id="action" type="hidden" name="action" value="restore">
                <div class="mws-form-inline">
                    <div class="mws-form-row">
                        <label class="mws-form-label">Select Backup</label>
                        <div class="mws-form-item">
                            <select id="backup" name="backup" class="large required" title="">
                            {backups}
                                <option value="{id}">{date}</option>
                            {/backups}
                            </select>
                        </div>
                    </div>
                </div>
            </form>
            <br /><br />
            <div style="text-align: center;">
                <button class="btn btn-primary" type="button" id="restore">Restore Database Tables</button>
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
            Restoring System Database... Please allow 30 seconds for this process to complete.
            <br />
            <br />
            <span style="color: red; ">DO NOT</span> refresh this window.
        </div>
    </div>
</div>