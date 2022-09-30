<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-refresh">Upgrade System Database</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				This option allows you to upgrade your existing "Gamespy" database to operate with new version of the Battlefied 2 Private Statistics system. 
				This option is generally only available when the installed database version differs from the expected version.<br /><br /> 
				<font color="red">Note:</font> Please ensure you a have a full backup of your existing database before proceeding! 
				<br /><br />
				<center>
					<input type="button" id="upgrade" class="mws-button blue" {disabled} value="{button_text}" />
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