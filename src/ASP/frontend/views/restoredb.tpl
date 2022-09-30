<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-tools">Restore Database</span>
	</div>
	<div class="mws-panel-body">
		<div class="content">
			<div class="mws-panel-content">
				<p>
					This option allows you restore your "Gamespy" Statistics Database tables from a previous backup. 
					This does not restore the database schema, just the data. Before you restore the data, please ensure you 
					have loaded the relevant database schema. As part of this process, ALL extisting data will be lost!
					<br /><br />
					<font color="red"><b>Warning:</b></font> Running this script will CLEAR ALL data from your existing database, please ensure you have a proper backup BEFORE proceeding. 
				</p>
			</div>
			<form id="restoreForm" class="mws-form" method="POST" action="?task=restoredb">
				<input type="hidden" name="action" value="restore" />
				<div class="mws-form-inline">
					<div class="mws-form-row">
						<label>Select Backup:</label>
						<div class="mws-form-item small">
							<select class="required" name="backupname">
								<option></option>
								{options}
									<option value="{name}">{name}</option>
								{/options}
							</select>
						</div>
					</div>
					<div class="mws-button-row">
						<input type="submit" value="Submit" class="mws-button red" />
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
						Restoring System Database... Please allow 30 seconds for this process to complete.
						<br />
						<br /><font color="red">DO NOT</font> close or refresh this window.
					</center>
				</p>
			</div>
		</div>
	</div>
</div>