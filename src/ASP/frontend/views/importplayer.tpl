<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-alert">Import Player</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				This area will allow you to Import player data from the Official EA ranked server database. You cannot import a player if they exist already on your server.
				 (IE: Player ID is already taken)<br /><br />
				<font color="red">Note:</font> During this test, Lots of data will be loaded into your database. If for any reason the import fails, you must go back and delete
				the player data from the "Manage Players" menu.
				<br /><br />
			</p>
		</div>
		
		<form id="importForm" class="mws-form" method="POST" action="?task=importplayer">
			<input type="hidden" name="action" value="import" />
			<div class="mws-form-inline">
				<div class="mws-form-row">
						<label>Player ID:</label>
						<div class="mws-form-item small">
							<input type="text" class="mws-textinput required" name="pid"/>
						</div>
				</div>
				<div class="mws-button-row">
					<input type="submit" value="Import Player" class="mws-button red" />
				</div>
			</div>
		</form>
		
		<!-- Hidden Ajax Thing -->
		<div id="ajax-dialog">
			<div class="mws-dialog-inner"></div>
		</div>
	</div>
</div>