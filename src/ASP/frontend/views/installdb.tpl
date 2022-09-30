<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-repeat">Install BF2 Private Stats Database</span>
	</div>
	<div class="mws-panel-body">
		<div class="content">
			<div class="mws-panel-content">
				<p>
					This option allows you to load the required database tables for your new Battlefield 2 Private Statistics system. You only have to do this once. This process will also, 
					update your configuration file with the database server details you enter. 
					<br /><br />
					<font color="red"><b>Note:</b></font> You MUST create the database, prior to running this script!
				</p>
			</div>
			<form id="installForm" class="mws-form" method="POST" action="?task=installdb">
				<input type="hidden" name="action" value="install" />
			
				<div class="mws-form-inline">	
					<div class="mws-form-row">
						<label>Database Host:</label>
						<div class="mws-form-item small">
							<input type="text" class="mws-textinput required" name="cfg__db_host" title="MySQL Database Host. Typically LOCALHOST."/>
						</div>
					</div>
					<div class="mws-form-row">
						<label>Database Port:</label>
						<div class="mws-form-item small">
							<input type="text" class="mws-textinput required" name="cfg__db_port" title="MySQL database port. Typically 3306."/>
						</div>
					</div>
					<div class="mws-form-row">
						<label>Database Name:</label>
						<div class="mws-form-item small">
							<input type="text" class="mws-textinput required" name="cfg__db_name" title="Database Name to store stats."/>
						</div>
					</div>
					<div class="mws-form-row">
						<label>Database Username:</label>
						<div class="mws-form-item small">
							<input type="text" class="mws-textinput required" name="cfg__db_user" title="Username with rights to Database."/>
						</div>
					</div>
					<div class="mws-form-row">
						<label>Database Password:</label>
						<div class="mws-form-item small">
							<input type="password" class="mws-textinput" name="cfg__db_pass" title="Password for Database Username."/>
						</div>
					</div>
					<div class="mws-button-row">
						<input type="submit" value="Install" class="mws-button red" />
						<input type="reset" value="Reset" class="mws-button gray" />
					</div>
				</div>
			</form>
		</div>
		
		<!-- Hidden Ajax Thing -->
		<div id="ajax-dialog">
			<div class="mws-dialog-inner">
				<p>
					<center>
						<img src="frontend/images/core/loading32.gif" />
						<br />
						<br />
						Installing System... This Process can take up to 5 minutes to complete on slower servers.
						<br />
						<br /><font color="red">DO NOT</font> close or refresh this window.
					</center>
				</p>
			</div>
		</div>
	</div>
</div>