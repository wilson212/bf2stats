<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-alert">Clear System Database</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				This option allows you clear your "Gamespy" Database of ALL collected statistics data. Please ensure you have a full backup of your database BEFORE proceeding!!<br /><br /> 
				<font color="red">WARNING:</font> This will destroy ALL existing statistics!! Use with EXTREME caution!!!  
				<br /><br />
				<center>
					<input type="button" id="clear" class="mws-button blue" value="Clear Datbase Tables" />
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
						Upgrading System Database... Please allow up to 30 seconds for this process to complete.
						<br />
						<br /><font color="red">DO NOT</font> refresh this window.
					</center>
				</p>
			</div>
		</div>
	</div>
</div>