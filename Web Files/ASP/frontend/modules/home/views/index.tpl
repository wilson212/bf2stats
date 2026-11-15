<!-- Statistics Button Container -->
<div class="mws-stat-container clearfix">

	<!-- Statistic Item -->
	<a class="mws-stat" href="/ASP/roundinfo">
		<!-- Statistic Icon (edit to change icon) -->
		<span class="mws-stat-icon icol32-monitor-lightning"></span>

		<!-- Statistic Content -->
		<span class="mws-stat-content">
			<span class="mws-stat-title">Total Games Processed</span>
			<span class="mws-stat-value">{num_rounds}</span>
		</span>
	</a>

	<a class="mws-stat" href="/ASP/snapshots/failed">
		<!-- Statistic Icon (edit to change icon) -->
		<span class="mws-stat-icon icol32-lightning-delete"></span>

		<!-- Statistic Content -->
		<span class="mws-stat-content">
			<span class="mws-stat-title">Total Failed Snapshots</span>
			<span class="mws-stat-value">{failed_snapshots}</span>
		</span>
	</a>

	<a class="mws-stat" href="/ASP/players">
		<!-- Statistic Icon (edit to change icon) -->
		<span class="mws-stat-icon icol32-group"></span>

		<!-- Statistic Content -->
		<span class="mws-stat-content">
			<span class="mws-stat-title">Total Number of Players</span>
			<span class="mws-stat-value">{num_players}</span>
		</span>
	</a>

	<a class="mws-stat" href="/ASP/players">
		<!-- Statistic Icon (edit to change icon) -->
		<span class="mws-stat-icon icol32-user-add"></span>

		<!-- Statistic Content -->
		<span class="mws-stat-content">
			<span class="mws-stat-title">New Players (7 days)</span>
			<span class="mws-stat-value{new_player_raise}">{num_new_players}</span>
		</span>
	</a>

	<a class="mws-stat" href="/ASP/players">
		<!-- Statistic Icon (edit to change icon) -->
		<span class="mws-stat-icon icol32-status-away"></span>

		<!-- Statistic Content -->
		<span class="mws-stat-content">
			<span class="mws-stat-title">Active Players (7 days)</span>
			<span class="mws-stat-value{active_player_raise}">{num_active_players}</span>
		</span>
	</a>

	<a class="mws-stat" href="/ASP/servers">
		<!-- Statistic Icon (edit to change icon) -->
		<span class="mws-stat-icon icol32-application-osx-terminal"></span>

		<!-- Statistic Content -->
		<span class="mws-stat-content">
			<span class="mws-stat-title">Active Servers (7 Days)</span>
			<span class="mws-stat-value{active_server_raise}">{num_active_servers}</span>
		</span>
	</a>
</div>

<!-- Panels Start -->

<div class="mws-panel grid_5">
	<div class="mws-panel-header">
		<span><i class="icon-graph"></i> Games Processed</span>
	</div>
	<div class="mws-panel-body">
		<div id="mws-dashboard-chart" style="height: 275px; margin: auto 20px;"></div>
        <div class="mws-form-row">
            <div style="text-align: center; margin-top: 15px">
                <div class="mws-form-item">
                    <div id="mws-ui-button-radio">
                        <input type="radio" id="radio1" name="radio" checked="checked"><label for="radio1">Last Week</label>
                        <input type="radio" id="radio2" name="radio"><label for="radio2">Last 6 Weeks</label>
                        <input type="radio" id="radio3" name="radio"><label for="radio3">Last Year</label>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>

<div class="mws-panel grid_3">
	<div class="mws-panel-header">
		<span><i class="icon-book"></i> Web Server Summary</span>
	</div>
	<div class="mws-panel-body no-padding">
		<ul class="mws-summary clearfix">
			<li>
				<span class="key"><i class="icon-monitor"></i> Apache Info</span>
				<span class="val">
					<span class="text-nowrap">{server_version}</span>
				</span>
			</li>
			<li>
				<span class="key"><i class="icon-fire"></i> PHP Version</span>
				<span class="val">
					<span class="text-nowrap">{php_version}</span>
				</span>
			</li>
			<li>
				<span class="key"><i class="icon-database"></i> MySQL Version</span>
				<span class="val">
					<span class="text-nowrap">{db_version}</i></span>
				</span>
			</li>
			<li>
				<span class="key"><i class="icon-table"></i> Stats Data Size</span>
				<span class="val">
					<span class="text-nowrap">{db_size} MB</span>
				</span>
			</li>
			<li>
				<span class="key"><i class="icon-windows"></i> Operating System</span>
				<span class="val">
					<span class="text-nowrap">{server_name}</span>
				</span>
			</li>
			<li>
				<span class="key"><i class="icon-key"></i> Last Sign In</span>
				<span class="val">
					<span class="text-nowrap">{last_login}</span>
				</span>
			</li>
		</ul>
	</div>
</div>

<div class="mws-panel grid_8">
	<div class="mws-panel-header">
		<span><i class="icon-rating3"></i> Player Rank Distribution</span>
	</div>
	<div class="mws-panel-body">
		<div id="mws-dashboard-chart2" style="height: 430px; margin: auto 20px;"></div>
	</div>
</div>