<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-box-incoming-2">Backup System Database</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				This option allows you backup your "Gamespy" Statistics Database tables. This does not backup the database schema, just the data. To restore, 
				simply reload the relevant database schema and import the latest backup files.<br /><br /> 
				<font color="red">IMPORTANT:</font> This does not replace a proper MySQL Backup Job, but it does save your data for later recovery.  
				<br /><br />
				<center>
					<input type="button" id="backup" class="mws-button blue" value="Backup Datbase Tables" />
				</center>
			</p>
		</div>
		
		<!-- Hidden Ajax Thing -->
		<div id="ajax-dialog">
			<div class="mws-dialog-inner">
				<p>
					<center>
						<img src="frontend/images/core/loading32.gif" />
						<br />
						<br />
						Backing Up System Table Data... Please allow up to 30 seconds for this process to complete.
						<br />
						<br /><font color="red">DO NOT</font> refresh this window.
					</center>
				</p>
			</div>
		</div>
	</div>
</div>