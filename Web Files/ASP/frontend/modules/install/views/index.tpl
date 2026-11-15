<div class="mws-panel grid_8">
    <div id="mws-jui-dialog">
        <div class="mws-dialog-inner"><p>HI</p></div>
    </div>
    <div class="mws-panel-header">
        <span><i class="icon-magic"></i> ASP Installation and Setup</span>
    </div>
    <div class="mws-panel-body no-padding">
        <form class="mws-form wzd-validate" action="/ASP/install" method="post">
            <input type="hidden" name="process" value="config">

            <!-- Page 1 -->
            <fieldset class="wizard-step mws-form-inline">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="username" value="{admin_user}">
                <input type="hidden" name="password" value="{admin_pass}">
                <legend class="wizard-label"><i class="icol-user-business-boss"></i> Admin Settings</legend>
                <div id class="mws-form-row">
                    <label class="mws-form-label">Admin Username <span class="required">*</span></label>
                    <div class="mws-form-item">
                        <input type="text" name="cfg__admin_user" class="required large" value="{admin_user}">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">Admin Password <span class="required">*</span></label>
                    <div class="mws-form-item">
                        <input type="password" name="cfg__admin_pass" class="required large" value="{admin_pass}">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label class="mws-form-label">Admin IP Whitelist</label>
                    <div class="mws-form-item">
                        <textarea name="cfg__admin_hosts" rows="" cols="" class="large autosize">{ip_list}</textarea>
                    </div>
                </div>
            </fieldset>

            <!-- Page 2 -->
            <fieldset class="wizard-step mws-form-inline">
                <legend class="wizard-label"><i class="icol-database"></i> Database Setup</legend>
                <div id="ajax-message-1"></div>
                <div id class="mws-form-row">
                    <div id="table-message" > <!-- class="alert info" style="display: none;"> -->
                        If the connecting database already contains the "_version" table, then the table
                        installation be skipped.
                    </div>
                </div>
                <div id class="mws-form-row">
                    <label class="mws-form-label">Database Host <span class="required">*</span></label>
                    <div class="mws-form-item">
                        <input type="text" name="cfg__db_host" class="required large" value="{db_host}">
                    </div>
                </div>
                <div id class="mws-form-row">
                    <label class="mws-form-label">Database Port <span class="required">*</span></label>
                    <div class="mws-form-item">
                        <input type="text" id="s4" name="cfg__db_port" class="required mws-spinner" value="{db_port}">
                        <label for="s4" class="error" generated="true" style="display:none"></label>
                    </div>
                </div>
                <div id class="mws-form-row">
                    <label class="mws-form-label">Database User <span class="required">*</span></label>
                    <div class="mws-form-item">
                        <input type="text" name="cfg__db_user" class="required large" value="{db_user}">
                    </div>
                </div>
                <div id class="mws-form-row">
                    <label class="mws-form-label">Database Password <span class="required">*</span></label>
                    <div class="mws-form-item">
                        <input type="password" name="cfg__db_pass" class="required large" value="{db_pass}">
                    </div>
                </div>
                <div id class="mws-form-row">
                    <label class="mws-form-label">Database Name <span class="required">*</span></label>
                    <div class="mws-form-item">
                        <input type="text" name="cfg__db_name" class="required large" value="{db_name}">
                    </div>
                </div>
            </fieldset>

            <!-- Page 3 -->
            <fieldset class="wizard-step mws-form-inline" style="min-height: 300px" >
                <legend class="wizard-label"><i class="icol-connect"></i> Test Connection</legend>
                <div class="mws-form-row">
                    <div style="text-align: center; margin-top: 100px; font-size: 150%; color: #00357B">
                        Connecting to MySQL database...
                    </div>
                    <div style="text-align: center; margin-top: 10px;">
                        <i class="icol32-computer"></i>
                        <img src="./frontend/images/core/loading11.gif" style="margin-top: 10px">
                        <i class="icol32-database"></i>
                    </div>
                </div>
            </fieldset>

            <!-- Page 4 -->
            <fieldset class="wizard-step mws-form-inline" style="min-height: 300px" >
                <legend class="wizard-label"><i class="icol-drive-go"></i> Install Tables</legend>
                <div class="mws-form-row">
                    <div style="text-align: center; margin-top: 100px; font-size: 150%; color: #00357B">
                        Installing Stats Tables...
                    </div>
                    <div style="text-align: center; margin-top: 10px;">
                        <i class="icol32-table-go"></i>
                        <img src="./frontend/images/core/loading11.gif" style="margin-top: 10px">
                        <i class="icol32-drive-edit"></i>
                    </div>
                </div>
            </fieldset>

            <!-- Page 5 -->
            <fieldset class="wizard-step mws-form-inline" style="min-height: 350px" >
                <legend class="wizard-label"><i class="icol-accept"></i> Confirmation</legend>
                <div id="install-success" style="text-align: center; margin: 10px 0;">
                    <img src="/ASP/frontend/modules/install/images/Check.png" style="margin-top: 10px">
                    <div style="font-size: 200%; font-weight: bold; margin-top: 20px">
                        System Installed Successfully!
                    </div>
                    <div style="font-size: 120%; margin-top: 20px">
                        Good job soldier! Your Battlefield 2 ASP Stats System Setup is now Complete.
                    </div>
                    <br /><br />
                    <input type="button" id="button-to-home" class="btn btn-success" value="Run System Tests" data-target="/ASP/config/test">
                </div>
                <div id="install-failed" style="text-align: center; margin: 10px 0; display: none;">
                    <img src="/ASP/frontend/modules/install/images/Cross.png" style="margin-top: 10px">
                    <div style="font-size: 200%; font-weight: bold; margin-top: 20px">
                        System Installation Failed!
                    </div>
                    <div id="fail-message" style="font-size: 120%; margin-top: 20px"></div>
                    <br /><br />
                    <input type="button" id="button-to-start-over" class="btn btn-danger" value="Start Over" data-target="/ASP/install">
                </div>
            </fieldset>
        </form>
    </div>
</div>