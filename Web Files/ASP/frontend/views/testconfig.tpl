<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-cog-5">Test System</span>
	</div>
	<div class="mws-panel-body">
		<div class="mws-panel-content">
			<p>
				This area will allow you test the setup and configuration of your "Gamespy" database server. It is normal to sometimes see a few <font color="orange"><b>Warn</b></font>'s
				, you can ignore these without too much worry. If you see any <font color="red"><b>FAIL</b></font>'s, then you will need to go back and reconfigure your system.<br /><br />
				<font color="red">Note:</font> During this test, sample data will be loaded into your database. This will be removed after the test. 
				<br /><br />
				<center>
					<input type="button" id="test-config" class="mws-button blue" value="Run System Tests" />
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
						Processing System Tests... Please allow up to 30 seconds for this process to complete.
						<br />
						<br /><font color="red">DO NOT</font> refresh this window.
					</center>
				</p>
			</div>
		</div>
	</div>
</div>