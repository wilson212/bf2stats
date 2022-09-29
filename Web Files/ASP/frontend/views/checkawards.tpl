<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-check">Check Player Backend Awards</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				This option allows you validate that your backend awards are functioning as designed. Also, if you have recently 
                added / changed the criteria's this script will allow you to remove/add awards to existing players.
				Award information is pulled out of the Awards Data File ("/ASP/system/data/awards.php").
				<br /><br />
				<center>
					<input type="button" id="check-awards" class="mws-button blue" value="Check Player Awards" />
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
						Validating Player Awards... Processing time could take up to 5 minutes based on the player base.
						<br />
						<br /><font color="red">DO NOT</font> close or refresh this window.
					</center>
				</p>
			</div>
		</div>
	</div>
</div>