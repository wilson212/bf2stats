<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-running-man">Manage Players</span>
	</div>
	<div class="mws-panel-body">
		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th>Player ID</th>
					<th>Nick</th>
					<th>Clan</th>
					<th>Rank</th>
					<th>Score</th>
					<th>Country</th>
					<th>Permban</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<!-- Hidden Ajax Thing -->
		<div id="ajax-dialog">
			<div class="mws-dialog-inner">
				<form id="mws-validate" class="mws-form" action="?task=manageplayers&ajax=player" method="POST">
					<input type="hidden" name="action" value="update" />
					<input type="hidden" name="id" id="player-id"/>
					<div id="ajax-message" style="display: none;"></div>
					
					<div class="mws-form-inline">
						<div class="mws-form-row">
							<label>Player ID:</label>
							<div class="mws-form-item large">
								<input id="pid" type="text" name="pid" class="mws-textinput" disabled="disabled" />
							</div>
						</div>
						<div class="mws-form-row">
							<label>Player Nick:</label>
							<div class="mws-form-item large">
								<input id="player-nick" type="text" name="nick" class="mws-textinput" disabled="disabled" />
							</div>
						</div>
						<div class="mws-form-row">
							<label>Clan Tag:</label>
							<div class="mws-form-item large">
								<input id="player-clantag" type="text" name="clantag" class="mws-textinput" />
							</div>
						</div>
						<div class="mws-form-row">
							<label>Rank:</label>
							<div class="mws-form-item large">
								<select id="player-rank" name="rank">
									<option value="0">Private</option>
									<option value="1">Private First Class</option>
									<option value="2">Lance Corporal</option>
									<option value="3">Corporal</option>
									<option value="4">Sergeant</option>
									<option value="5">Staff Sergeant</option>
									<option value="6">Gunnery Sergeant</option>
									<option value="7">Master Sergeant</option>
									<option value="8">1st Sergeant</option>
									<option value="9">Master Gunnery Sergeant</option>
									<option value="10">Sergeant Major</option>
									<option value="11">Sergeant Major of the Corps</option>
									<option value="12">2nd Lieutenant</option>
									<option value="13">1st Lieutenant</option>
									<option value="14">Captain</option>
									<option value="15">Major</option>
									<option value="16">Lieutenant Colonel</option>
									<option value="17">Colonel</option>
									<option value="18">Brigadier General</option>
									<option value="19">Major General</option>
									<option value="20">Lieutenant General</option>
									<option value="21">General</option>
								</select>
							</div>
						</div>
						<div class="mws-form-row">
							<label>Perm Ban:</label>
							<div class="mws-form-item large">
								<select id="player-ban" name="permban">
									<option value="0">No</option>
									<option value="1">Yes</option>
								</select>
							</div>
						</div>
						<div class="mws-form-row">
							<label>Reset Unlocks:</label>
							<div class="mws-form-item clearfix">
								<ul class="mws-form-list inline">
									<li><input name="reset" type="checkbox">  <label>Check to force user to re-select thier unlocks.</li>
								</ul>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>