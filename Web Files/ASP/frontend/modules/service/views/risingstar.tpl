<div class="mws-panel grid_5">
    <div class="mws-panel-header">
        <span><i class="icon-list-2"></i> Refresh Rising Star Leaderboard</span>
    </div>
    <div class="mws-panel-body">
        <div class="mws-panel-content">
            <p>
                Once a week, the Rising Star leaderboard will need to be refreshed for BFHQ. This is a very heavy calculation which
                can take up to 60 seconds to completed.
                <br /><br />
                The Rising star leaderboard is calculated by comparing each player's score per minute over the last week,
                and comparing it against their average score per minute since they have started playing. Each player is
                then sorted by score, and stored in a database table for easy access by BFHQ.
                <br /><br />
                <div style="text-align: center;">
                    <input type="button" id="test-config" class="btn btn-primary" value="Refresh Rising Star Leaderboard" />
                </div>
            </p>
        </div>

        <!-- Hidden Ajax Thing -->
        <div id="ajax-dialog">
            <div class="mws-dialog-inner">
                <div style="text-align: center;">
                    <img src="/ASP/frontend/images/core/loading32.gif" />
                    <br />
                    <br />
                    Rebuilding Rising Star DataTable... Please allow up to 60 seconds for this process to complete.
                    <br />
                    <br />
                    <span style="color: red; ">DO NOT</span> refresh this window.
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mws-panel grid_3">
    <div class="mws-panel-header">
        <span><i class="icon-book"></i> Rising Star Summary</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-watch2"></i> Last Refresh Ran</span>
                <span class="val">
					<span class="text-nowrap">{last}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-hour-glass"></i> Next Refresh Due</span>
                <span class="val">
					<span class="text-nowrap" style="color: {next_color}; ">{next}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-fire"></i> Number of Records</span>
                <span class="val">
					<span class="text-nowrap">{records}</span>
				</span>
            </li>
        </ul>
    </div>
</div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-users"></i> Rising Stars List</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width: 3%"><span class="loading-cell"></span></th>
                    <th style="width: 7%">Position</th>
                    <th style="width: 7%">ID</th>
                    <th style="width: 5%">Rank</th>
                    <th style="width: 20%">Name</th>
                    <th>Country</th>
                    <th>Weekly Score</th>
                    <th>Join Date</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>