<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-check">Validate Ranks</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				This option allows you validate that the players in your database have the correct rank depending on thier score and awards. Rank information may get out of sync due to a variety of factors.
				Rank information is pulled out of the Rank Data File ("/ASP/system/data/ranks.php").
				<br /><br />
				<center>
					<input type="button" id="validate" class="mws-button blue" value="Validate Ranks" />
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
						Validating Ranks... Processing time could take up to 5 minutes based on the player base, and how many players have incorrect ranks.
						<br />
						<br /><font color="red">DO NOT</font> close or refresh this window.
					</center>
				</p>
			</div>
		</div>
	</div>
</div>