<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-shuffle">Merge Players</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				The option allows you to merge data from two players in one single player. This would
                generally be used because a player is issued with a new PlayerID and do not wish to lose their existing stats<br /><br />
				<font color="red"><b>WARNING:</b></font> Only the Target player is left, the source player is deleted as part of this process!
				<br /><br />
			</p>
		</div>
		
		<form id="mergeForm" class="mws-form" method="POST" action="?task=mergeplayers">
			<input type="hidden" name="action" value="merge" />
			<div class="mws-form-inline">
				<div class="mws-form-row">
						<label>Source Player ID:</label>
						<div class="mws-form-item small">
							<input type="text" class="mws-textinput required" name="source_pid"/>
						</div>
                        
                        <label>Target Player ID:</label>
						<div class="mws-form-item small">
							<input type="text" class="mws-textinput required" name="target_pid"/>
						</div>
				</div>
				<div class="mws-button-row">
					<input type="submit" value="Merge Players" class="mws-button red" />
				</div>
			</div>
		</form>
		
		<!-- Hidden Ajax Thing -->
		<div id="ajax-dialog">
			<div class="mws-dialog-inner"></div>
		</div>
	</div>
</div>