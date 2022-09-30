<!-- Server Status and Info -->
<div class="mws-panel grid_8 mws-collapsible">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-map"> {server.hostname}</span>
	</div>
	<div class="mws-panel-body">
        <div class="mws-panel-content">
            <p>
                <table width="550px" cellspacing="50px">
                    <tr>
                        <td rowspan="20" valign="middle">
                            <table>
                                <thead>
                                    <th colspan="2"><center>Current Map: {server.mapname} {server.bf2_mapsize} </center></th>
                                </thead>
                                <tr>
                                    <td>
                                        <br /><img src="frontend/images/maps/{map_image}.png">
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td valign="middle">
                            <table width="240px" cellspacing="5px">
                                <thead>
                                    <th colspan="2"><center>Server Information</center></th>
                                </thead>
                                <tr>
                                    <td>
                                        <br />Status
                                    </td>
                                    <td style="text-align:right">
                                        <br />{server.gamemode}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Game Type
                                    </td>
                                    <td style="text-align:right">
                                        {server.gametype}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Mod
                                    </td>
                                    <td style="text-align:right">
                                        {server.gamevariant}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Number of Players
                                    </td>
                                    <td style="text-align:right">
                                        {server.numplayers}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Max Players
                                    </td>
                                    <td style="text-align:right">
                                        {server.maxplayers}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Time Limit
                                    </td>
                                    <td style="text-align:right">
                                        {server.timelimit}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Score Limit
                                    </td>
                                    <td style="text-align:right">
                                        {server.bf2_scorelimit}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Ticket Ratio
                                    </td>
                                    <td style="text-align:right">
                                        {server.bf2_ticketratio}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Team Ratio
                                    </td>
                                    <td style="text-align:right">
                                        <?php echo round('{server.bf2_teamratio}', 0); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Auto Balance Teams
                                    </td>
                                    <td style="text-align:right">
                                        <?php 
                                            if({server.bf2_autobalanced} == 0)
                                            { 
                                                echo "<font color='red'>No</font>"; 
                                            }else{
                                                echo "<font color='green'>Yes</font>";
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Player Spawn Time
                                    </td>
                                    <td style="text-align:right">
                                        <?php echo round('{server.bf2_spawntime}', 0); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Friendly Fire
                                    </td>
                                    <td style="text-align:right">
                                        <?php 
                                            if({server.bf2_friendlyfire} == 0)
                                            { 
                                                echo "Disabled"; 
                                            }else{
                                                echo "Enabled";
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Punk Buster
                                    </td>
                                    <td style="text-align:right">
                                        <?php 
                                            if({server.bf2_anticheat} == 0)
                                            { 
                                                echo "<font color='red'>No</font>"; 
                                            }else{
                                                echo "<font color='green'>Yes</font>";
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                    </tr>
                </table>
            </p>
        </div>
	</div>
</div>

<!-- TEAM 1 -->
<div class="mws-panel grid_4 mws-collapsible">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-{server.bf2_team1}"> {server.team1_name}</span>
	</div>
	<div class="mws-panel-body">
		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Score</th>
					<th>Kills</th>
					<th>Deaths</th>
					<th>Ping</th>
				</tr>
			</thead>
			<tbody>
				{players_1}
					<tr>
						<td>{name}</td>
						<td>{score}</td>
						<td>{kills}</td>
						<td>{deaths}</td>
						<td>{ping}</td>
					</tr>
				{/players_1}
			</tbody>
		</table>
	</div>
</div>

<!-- Team 2 -->
<div class="mws-panel grid_4 mws-collapsible">
	<div class="mws-panel-header">
		<span class="mws-i-24 i-{server.bf2_team2}"> {server.team2_name}</span>
	</div>
	<div class="mws-panel-body">
		<table class="mws-datatable-fn mws-table">
			<thead>
				<tr>
					<th>Name</th>
					<th>Score</th>
					<th>Kills</th>
					<th>Deaths</th>
					<th>Ping</th>
				</tr>
			</thead>
			<tbody>
				{players_2}
					<tr>
						<td>{name}</td>
						<td>{score}</td>
						<td>{kills}</td>
						<td>{deaths}</td>
						<td>{ping}</td>
					</tr>
				{/players_2}
			</tbody>
		</table>
	</div>
</div>