<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_4">
    <div class="mws-panel-header">
        <span>Player Information</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="edit-player" href="#" class="btn"><i class="icol-key"></i> Edit Account</a>
                <a id="ban-player" href="#" class="btn" <?php echo ({player.permban} == 0) ? '' : ' style="display: none"'; ?>>
                    <i class="icol-cross-shield-2"></i> Ban Player
                </a>
                <a id="unban-player" href="#" class="btn" <?php echo ({player.permban} == 1) ? '' : ' style="display: none"'; ?>>
                    <i class="icol-accept"></i> Un-Ban Player
                </a>
                <a href="/ASP/players/history/{id}" class="btn"><i class="icol-clock"></i> View Round History</a>
                <a id="dlDropDown" href="#" class="btn dropdown-toggle" data-toggle="dropdown">
                    <i class="icol-arrow-refresh"></i> Reset <span class="caret"></span>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a id="reset-stats" href="#"><i class="icol-trophy"></i> Reset Player Stats and Awards</a>
                        <a id="reset-awards" href="#"><i class="icol-medal-gold-1"></i> Reset Player Awards</a>
                        <a id="reset-unlocks" href="#"><i class="icol-target"></i> Reset Player Unlocks</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <!-- Hidden fields for JavaScript -->
                <span id="playerCurrentId" style="display: none">{id}</span>
                <span id="playerCurrentRank" style="display: none">{player.rank_id}</span>
                <span id="playerCurrentIso" style="display: none">{player.country}</span>
                <span id="playerCurrentEmail" style="display: none">{player.email}</span>
                <span id="labelStatus" style="display: none">{player.badge}</span>
                <!-- End Hidden fields for JavaScript -->
                <span class="key"><i class="icon-user"></i> Player Name</span>
                <span class="val">
					<span class="text-nowrap">
                        <span id="changeableName">{player.name}</span>
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-rating3"></i> Player Rank</span>
                <span class="val">
					<span class="text-nowrap">
                        <img id="rankIcon" style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{player.rank_id}.gif"/>
                        <span id="changeableRank">{player.rankName}</span>
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-flag"></i> Player Country</span>
                <span class="val">
                    <img id="flag" style="margin: -3px 10px 0 0; height: 16px;" src="/ASP/frontend/images/flags/{player.country}.png"/>
					<span id="fullCountryName" class="text-nowrap">{player.country}</span>
                    (Last IP: {player.lastip})
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-check"></i> Account Status</span>
                <span class="val">
					<span class="text-nowrap">
                        <label id="status" class="label label-{player.badge}">{player.statustext}</label>
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-calendar"></i> Join Date</span>
                <span class="val">
					<span class="text-nowrap">{player.joined}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-history-2"></i> Last Seen</span>
                <span class="val">
					<span class="text-nowrap">{player.lastonline}</i></span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-watch2"></i> Time Played</span>
                <span class="val">
					<span class="text-nowrap">{player.timeplayed}</i></span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-gun-forbidden"></i> Kicked & Banned</span>
                <span class="val">
					<span class="text-nowrap">{player.kicked} / {player.banned}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Overall Performance</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-stats-up"></i> Global Score</span>
                <span class="val">
					<span class="text-nowrap">{player.score}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-gun"></i> Combat Score</span>
                <span class="val">
					<span class="text-nowrap">{player.skillscore}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-tools"></i> Team Score</span>
                <span class="val">
					<span class="text-nowrap">{player.teamscore}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-star"></i> Command Score</span>
                <span class="val">
					<span class="text-nowrap">{player.cmdscore}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-target2"></i> Total Kills</span>
                <span class="val">
					<span class="text-nowrap">{player.kills}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-heart-broken3"></i> Total Deaths</span>
                <span class="val">
					<span class="text-nowrap">{player.deaths}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-balance"></i> K/D Ratio</span>
                <span class="val">
					<span class="text-nowrap">{player.ratio} (<span style="color: {player.ratioColor}">{player.ratio2}</span>)</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-meter-fast"></i> Score Per Min.</span>
                <span class="val">
					<span class="text-nowrap">{player.spm}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-screenshot"></i> Accuracy</span>
                <span class="val">
					<span class="text-nowrap">{weaponAverage.accuracy}%</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Team Work</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-aid-kit"></i> Heals</span>
                <span class="val">
					<span class="text-nowrap">{player.heals}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-pulse"></i> Revives</span>
                <span class="val">
					<span class="text-nowrap">{player.revives}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-ammo"></i> Resupplies</span>
                <span class="val">
					<span class="text-nowrap">{player.resupplies}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-wrench"></i> Repairs</span>
                <span class="val">
					<span class="text-nowrap">{player.repairs}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-flag"></i> Flag Captures</span>
                <span class="val">
					<span class="text-nowrap"
                          style="border-bottom: 1px dotted #000;"
                          rel="tooltip"
                          data-placement="right"
                          title="Captures / Assists">
                        {player.captures} / {player.captureassists}
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-flag2"></i> Neutralizes</span>
                <span class="val">
					<span class="text-nowrap"
                          style="border-bottom: 1px dotted #000;"
                          rel="tooltip"
                          data-placement="right"
                          title="Neutralizes / Assists">
                        {player.neutralizes} / {player.neutralizeassists}
                    </span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-shield"></i> Flag Defends</span>
                <span class="val">
					<span class="text-nowrap">{player.defends}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-steering-wheel"></i> Driver Specials</span>
                <span class="val">
					<span class="text-nowrap">{player.driverspecials}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-screenshot"></i> Kill Assists</span>
                <span class="val">
					<span class="text-nowrap">{player.damageassists}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Game Statistics</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-retweet"></i> Rounds Played</span>
                <span class="val">
					<span class="text-nowrap">{player.total_rounds}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-trophy"></i> Wins</span>
                <span class="val">
					<span class="text-nowrap">{player.wins}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-ban-circle"></i> Losses</span>
                <span class="val">
					<span class="text-nowrap">{player.losses}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-chart"></i> W/L Ratio</span>
                <span class="val">
                    <span class="text-nowrap">{player.WLRatio} (<span style="color: {player.WLRatioColor}">{player.WLRatio2}</span>)</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Time Statistics</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-stars"></i> Commander</span>
                <span class="val">
					<span class="text-nowrap">{player.cmdtime}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-star"></i> Squad Leader</span>
                <span class="val">
					<span class="text-nowrap">{player.sqltime}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-star-empty"></i> Squad Member</span>
                <span class="val">
					<span class="text-nowrap">{player.sqmtime}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-rocket"></i> Lone Wolf</span>
                <span class="val">
					<span class="text-nowrap">{player.lwtime}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Negative Statistics</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-skull"></i> Team Kills</span>
                <span class="val">
					<span class="text-nowrap">{player.teamkills}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-pandage"></i> Team Damage</span>
                <span class="val">
					<span class="text-nowrap">{player.teamdamage}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-truck"></i> Team Vehicle Dam.</span>
                <span class="val">
					<span class="text-nowrap">{player.teamvehicledamage}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-heart-broken3"></i> Suicides</span>
                <span class="val">
					<span class="text-nowrap">{player.suicides}</span>
				</span>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_2">
    <div class="mws-panel-header">
        <span>Misc Statistics</span>
    </div>
    <div class="mws-panel-body no-padding">
        <ul class="mws-summary clearfix">
            <li>
                <span class="key"><i class="icon-screenshot"></i> Best Kill Streak</span>
                <span class="val">
					<span class="text-nowrap">{player.killstreak}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-trophy-2"></i> Best Round Score</span>
                <span class="val">
					<span class="text-nowrap">{player.bestscore}</span>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-skull"></i> Favorite Victim</span>
                <span class="val">
                    <?php if ($favVictim['id'] == 0): ?>
                    <span class="text-nowrap">
                        None (0)
                    </span>
                    <?php else: ?>
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{favVictim.rank}.gif"/>
                        <a href="/ASP/players/view/{favVictim.id}">
                            {favVictim.name}
                        </a> ({favVictim.count})
                    </span>
                    <?php endif ?>
				</span>
            </li>
            <li>
                <span class="key"><i class="icon-skull2"></i> Worst Enemy</span>
                <?php if ($worstOp['id'] == 0): ?>
                <span class="text-nowrap">
                        None (0)
                    </span>
                <?php else: ?>
                <span class="val">
					<span class="text-nowrap">
                        <img style="margin: -3px 0 0 0" src="/ASP/frontend/images/ranks/rank_{worstOp.rank}.gif"/>
                        <a href="/ASP/players/view/{worstOp.id}">
                            {worstOp.name}
                        </a> ({worstOp.count})
                    </span>
				</span>
                <?php endif ?>
            </li>
        </ul>
    </div>
</div>

<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-graph"></i> Games Played</span>
    </div>
    <div class="mws-panel-body">
        <div id="mws-games-chart" style="height: 320px; margin: auto 20px;"></div>
        <div class="mws-form-row">
            <div style="text-align: center; margin-top: 15px">
                <div class="mws-form-item">
                    <div id="mws-ui-button-radio">
                        <input type="radio" id="weekRadio" name="radio" checked="checked"><label for="weekRadio">Last Week</label>
                        <input type="radio" id="monthRadio" name="radio"><label for="monthRadio">Last 6 Weeks</label>
                        <input type="radio" id="yearRadio" name="radio"><label for="yearRadio">Last Year</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible clear">
    <div class="mws-panel-header">
        <span>Kit Data</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="kitData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Kills</th>
                <th style="width: 15%">Deaths</th>
                <th style="width: 15%">Ratio</th>
            </tr>
            </thead>
            <tbody>
                {kitData}
                <tr>
                    <td>{name}</td>
                    <td>{time}</td>
                    <td>{kills}</td>
                    <td>{deaths}</td>
                    <td>{ratio}</td>
                </tr>
                {/kitData}
            </tbody>
            <tfoot>
                <tr>
                    <td>Totals</td>
                    <td>{kitTotals.time}</td>
                    <td>{kitTotals.kills}</td>
                    <td>{kitTotals.deaths}</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Averages</td>
                    <td>{kitAverage.time}</td>
                    <td>{kitAverage.kills}</td>
                    <td>{kitAverage.deaths}</td>
                    <td>{kitAverage.ratio}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Vehicle Data</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="vehicleData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Kills</th>
                <th style="width: 15%">Deaths</th>
                <th style="width: 15%">RK</th>
                <th style="width: 15%">Ratio</th>
            </tr>
            </thead>
            <tbody>
            {vehicleData}
                <tr>
                    <td>{name}</td>
                    <td>{time}</td>
                    <td>{kills}</td>
                    <td>{deaths}</td>
                    <td>{roadKills}</td>
                    <td>{ratio}</td>
                </tr>
            {/vehicleData}
            </tbody>
            <tfoot>
            <tr>
                <td>Totals</td>
                <td>{vehicleTotals.time}</td>
                <td>{vehicleTotals.kills}</td>
                <td>{vehicleTotals.deaths}</td>
                <td>{vehicleTotals.roadKills}</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Averages</td>
                <td>{vehicleAverage.time}</td>
                <td>{vehicleAverage.kills}</td>
                <td>{vehicleAverage.deaths}</td>
                <td>{vehicleAverage.roadKills}</td>
                <td>{vehicleAverage.ratio}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible clear">
    <div class="mws-panel-header">
        <span>Army Data</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="armyData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Wins</th>
                <th style="width: 15%">Losses</th>
                <th style="width: 15%">Best</th>
                <th style="width: 15%">Ratio</th>
            </tr>
            </thead>
            <tbody>
            {armyData}
                <tr>
                    <td>{name}</td>
                    <td>{time}</td>
                    <td>{wins}</td>
                    <td>{losses}</td>
                    <td>{best}</td>
                    <td>{ratio}</td>
                </tr>
            {/armyData}
            </tbody>
            <tfoot>
                <tr>
                    <td>Totals</td>
                    <td>{armyTotals.time}</td>
                    <td>{armyTotals.wins}</td>
                    <td>{armyTotals.losses}</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Averages</td>
                    <td>{armyAverage.time}</td>
                    <td>{armyAverage.wins}</td>
                    <td>{armyAverage.losses}</td>
                    <td>{armyAverage.best}</td>
                    <td>{armyAverage.ratio}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Weapon Data</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="weaponData" class="mws-table">
            <thead>
            <tr>
                <th>Name</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Kills</th>
                <th style="width: 15%">Deaths</th>
                <th style="width: 15%">Acc</th>
                <th style="width: 15%">Ratio</th>
            </tr>
            </thead>
            <tbody>
            {weaponData}
                <tr>
                    <td>{name}</td>
                    <td>{time}</td>
                    <td>{kills}</td>
                    <td>{deaths}</td>
                    <td>
                        <span style="border-bottom: 1px dotted #000;" rel="tooltip"  title="Shots Fired: {fired}">
                            {accuracy}%
                        </span>
                    </td>
                    <td>{ratio}</td>
                </tr>
            {/weaponData}
            </tbody>
            <tfoot>
                <tr>
                    <td>Totals</td>
                    <td>{weaponTotals.time}</td>
                    <td>{weaponTotals.kills}</td>
                    <td>{weaponTotals.deaths}</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>Averages</td>
                    <td>{weaponAverage.time}</td>
                    <td>{weaponAverage.kills}</td>
                    <td>{weaponAverage.deaths}</td>
                    <td>
                        <span style="border-bottom: 1px dotted #000;" rel="tooltip"  title="Average Shots Fired: {weaponAverage.fired}">
                            {weaponAverage.accuracy}%
                        </span>
                    </td>
                    <td>{weaponAverage.ratio}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_8">
    <div class="mws-tabs">
        <ul>
            <li><a href="#tab-1">Badges</a></li>
            <li><a href="#tab-2">Medals</a></li>
            <li><a href="#tab-3">Ribbons</a></li>
            <li><a href="#tab-4">Unlocks</a></li>
        </ul>
        <div id="tab-1">
            <ul class="thumbnails mws-gallery">
                {badges}
                    <?php if ({level} != 0): ?>
                    <li>
                    <span class="thumbnail"
                          rel="popover"
                          data-trigger="hover"
                          data-placement="bottom"
                          data-original-title="{prefix} {name}"
                          data-content="<div><b>Earned</b>: {last}</div>">
                        <img src="/ASP/frontend/images/awards/color/badges/{id}_{level}.png"/>
                    </span>
                    </li>
                    <?php else: ?>
                    <li>
                        <span class="thumbnail"
                              rel="popover"
                              data-trigger="hover"
                              data-placement="bottom"
                              data-original-title="{name}"
                              data-content="Player has not earned this award yet!">
                            <img style="opacity: 0.5; filter: alpha(opacity=50);" src="/ASP/frontend/images/awards/grey/badges/{id}.png"/>
                        </span>
                    </li>
                    <?php endif ?>
                {/badges}
            </ul>
        </div>
        <div id="tab-2">
            <ul class="thumbnails mws-gallery">
            {medals}
                <?php if ({level} != 0): ?>
                <li>
                    <span class="thumbnail"
                          rel="popover"
                          data-trigger="hover"
                          data-placement="bottom"
                          data-original-title="{name}"
                          data-content="<div><b>Award Count</b>: {level}</div><div><b>First</b>: {first}</div><div><b>Last</b>: {last}</div>">
                        <img src="/ASP/frontend/images/awards/color/medals/{id}.png"/>
                    </span>
                </li>
                <?php else: ?>
                <li>
                    <span class="thumbnail"
                          rel="popover"
                          data-trigger="hover"
                          data-placement="bottom"
                          data-original-title="{name}"
                          data-content="Player has not earned this award yet!">
                        <img style="opacity: 0.5; filter: alpha(opacity=50);" src="/ASP/frontend/images/awards/grey/medals/{id}.png"/>
                    </span>
                </li>
                <?php endif ?>
            {/medals}
            </ul>
        </div>
        <div id="tab-3">
            <ul class="thumbnails mws-gallery">
                {ribbons}
                    <?php if ({level} != 0): ?>
                    <li>
                    <span class="thumbnail"
                          rel="popover"
                          data-trigger="hover"
                          data-placement="bottom"
                          data-original-title="{name}"
                          data-content="<div><b>Earned</b>: {last}</div>">
                        <img src="/ASP/frontend/images/awards/color/ribbons/{id}.png"/>
                    </span>
                    </li>
                    <?php else: ?>
                    <li>
                        <span class="thumbnail"
                              rel="popover"
                              data-trigger="hover"
                              data-placement="bottom"
                              data-original-title="{name}"
                              data-content="Player has not earned this award yet!">
                            <img style="opacity: 0.5; filter: alpha(opacity=50);" src="/ASP/frontend/images/awards/grey/ribbons/{id}.png"/>
                        </span>
                    </li>
                    <?php endif ?>
                {/ribbons}
            </ul>
        </div>
        <div id="tab-4">
            <ul class="thumbnails mws-gallery">
                {unlocks}
                    <?php if ({level} != 0): ?>
                    <li>
                    <span class="thumbnail"
                          rel="popover"
                          data-trigger="hover"
                          data-placement="bottom"
                          data-original-title="{name}"
                          data-content="<div><b>Kit Name</b>: {kit}</div><div><b>Earned</b>: {timestamp}</div>">
                        <img src="/ASP/frontend/images/unlocks/color/{id}.png"/>
                    </span>
                    </li>
                    <?php else: ?>
                    <li>
                        <span class="thumbnail"
                              rel="popover"
                              data-trigger="hover"
                              data-placement="bottom"
                              data-original-title="{name}"
                              data-content="<div><b>Kit Name</b>: {kit}</div><div><b>Earned</b>: Player has not selected this unlock yet! </div>">
                            <img src="/ASP/frontend/images/unlocks/grey/{id}.png"/>
                        </span>
                    </li>
                    <?php endif ?>
                {/unlocks}
            </ul>
        </div>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Top 20 Favorite Maps</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="mapData" class="mws-table">
            <thead>
            <tr>
                <th>Map</th>
                <th style="width: 15%">Time</th>
                <th style="width: 15%">Wins</th>
                <th style="width: 15%">Losses</th>
                <th style="width: 15%">Ratio</th>
                <th style="width: 15%">Best</th>
            </tr>
            </thead>
            <tbody>
            {mapData}
                <tr>
                    <td><a href="/ASP/mapinfo/view/{id}" class="black-link">{name}</a></td>
                    <td>{time}</td>
                    <td>{wins}</td>
                    <td>{losses}</td>
                    <td>{ratio}</td>
                    <td>{best}</td>
                </tr>
            {/mapData}
            </tbody>
            <tfoot>
            <tr>
                <td>Totals</td>
                <td>{mapTotals.time}</td>
                <td>{mapTotals.wins}</td>
                <td>{mapTotals.losses}</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Averages</td>
                <td>{mapAverage.time}</td>
                <td>{mapAverage.wins}</td>
                <td>{mapAverage.losses}</td>
                <td>{mapAverage.ratio}</td>
                <td>{mapAverage.best}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="mws-panel grid_4 mws-collapsible">
    <div class="mws-panel-header">
        <span>Top 20 Favorite Servers</span>
    </div>
    <div class="mws-panel-body no-padding">
        <table id="serverData" class="mws-table">
            <thead>
            <tr>
                <th style="width: 15%">Server Id</th>
                <th>Server Name</th>
                <th style="width: 20%">Games Played</th>
            </tr>
            </thead>
            <tbody>
            {serverData}
                <tr>
                    <td>{id}</td>
                    <td><a href="/ASP/servers/view/{id}" class="black-link">{name}</a></td>
                    <td>{count}</td>
                </tr>
            {/serverData}
            </tbody>
        </table>
    </div>
</div>

<div class="mws-panel grid_8 mws-collapsible">
    <div class="mws-panel-header">
        <span>Time To Advancement</span>
    </div>
    <div class="mws-panel-body">
        <table id="rank-advancement">
            {nextRanks}
            <tr>
                <td id="rank">
                    <span class="thumbnail">
                        <img src="/ASP/frontend/images/ranks/large2/rank_{iteration.key}.png">
                    </span>
                </td>
                <td>
                    <span class="thumbnail thumbnail-value">
                        Next Rank: <strong>{title}</strong>
                        <br />{missing_desc}
                        <ul>
                        {missing_awards}
                            <li>{name}</li>
                        {/missing_awards}
                        </ul>
                        <div class="progressbar">
                            <div class="mws-progressbar-val ui-progressbar ui-widget ui-widget-content ui-corner-all"
                                 role="progressbar"
                                 aria-valuemin="0"
                                 aria-valuemax="100"
                                 aria-valuenow="{percent_complete}">
                                <div class="ui-progressbar-value ui-widget-header ui-corner-left" style="width: {percent_complete}%;">
                                    <span style="display: inline; font-weight: bold;">{percent_complete}%</span>
                                </div>
                            </div>
                        </div>
                        <p style="margin-top: 5px;">
                            Score: {player.score} of {points}. At your historical rate, you should earn {points_needed}
                            in {days_complete} days (or {time_complete} straight).
                        </p>
                    </span>
                </td>
            </tr>
            {/nextRanks}
        </table>
    </div>
</div>

<!-- Add New Player Ajax Model -->
<div id="edit-player-form">
    <form id="mws-validate" class="mws-form" method="post" action="/ASP/players/add">
        <input id="post-action" type="hidden" name="action" value="edit">
        <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
        <div id="jui-message" class="alert" style="display: none; width: 92%; margin-bottom: 20px;"></div>
        <div class="mws-form-inline">
            <div class="mws-form-row">
                <label class="mws-form-label">Player Name</label>
                <div class="mws-form-item">
                    <input type="text" name="playerName" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Player Email</label>
                <div class="mws-form-item">
                    <input type="text" name="playerEmail" class="required large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label id="passwordLabel" class="mws-form-label">Update Password</label>
                <div class="mws-form-item">
                    <input type="text" name="playerPassword" class="large" title="">
                </div>
            </div>
            <div class="mws-form-row">
                <label class="mws-form-label">Rank</label>
                <div class="mws-form-item">
                    <select id="rankSelect" name="playerRank" class="large required" title="">
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
                <label class="mws-form-label">Country</label>
                <div class="mws-form-item">
                    <select id="country" name="playerCountry" class="mws-select2 large required" title="">
                        <option value="AF">Afghanistan</option>
                        <option value="AX">Åland Islands</option>
                        <option value="AL">Albania</option>
                        <option value="DZ">Algeria</option>
                        <option value="AS">American Samoa</option>
                        <option value="AD">Andorra</option>
                        <option value="AO">Angola</option>
                        <option value="AI">Anguilla</option>
                        <option value="AQ">Antarctica</option>
                        <option value="AG">Antigua and Barbuda</option>
                        <option value="AR">Argentina</option>
                        <option value="AM">Armenia</option>
                        <option value="AW">Aruba</option>
                        <option value="AU">Australia</option>
                        <option value="AT">Austria</option>
                        <option value="AZ">Azerbaijan</option>
                        <option value="BS">Bahamas</option>
                        <option value="BH">Bahrain</option>
                        <option value="BD">Bangladesh</option>
                        <option value="BB">Barbados</option>
                        <option value="BY">Belarus</option>
                        <option value="BE">Belgium</option>
                        <option value="BZ">Belize</option>
                        <option value="BJ">Benin</option>
                        <option value="BM">Bermuda</option>
                        <option value="BT">Bhutan</option>
                        <option value="BO">Bolivia, Plurinational State of</option>
                        <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                        <option value="BA">Bosnia and Herzegovina</option>
                        <option value="BW">Botswana</option>
                        <option value="BV">Bouvet Island</option>
                        <option value="BR">Brazil</option>
                        <option value="IO">British Indian Ocean Territory</option>
                        <option value="BN">Brunei Darussalam</option>
                        <option value="BG">Bulgaria</option>
                        <option value="BF">Burkina Faso</option>
                        <option value="BI">Burundi</option>
                        <option value="KH">Cambodia</option>
                        <option value="CM">Cameroon</option>
                        <option value="CA">Canada</option>
                        <option value="CV">Cape Verde</option>
                        <option value="KY">Cayman Islands</option>
                        <option value="CF">Central African Republic</option>
                        <option value="TD">Chad</option>
                        <option value="CL">Chile</option>
                        <option value="CN">China</option>
                        <option value="CX">Christmas Island</option>
                        <option value="CC">Cocos (Keeling) Islands</option>
                        <option value="CO">Colombia</option>
                        <option value="KM">Comoros</option>
                        <option value="CG">Congo</option>
                        <option value="CD">Congo, the Democratic Republic of the</option>
                        <option value="CK">Cook Islands</option>
                        <option value="CR">Costa Rica</option>
                        <option value="CI">Côte d'Ivoire</option>
                        <option value="HR">Croatia</option>
                        <option value="CU">Cuba</option>
                        <option value="CW">Curaçao</option>
                        <option value="CY">Cyprus</option>
                        <option value="CZ">Czech Republic</option>
                        <option value="DK">Denmark</option>
                        <option value="DJ">Djibouti</option>
                        <option value="DM">Dominica</option>
                        <option value="DO">Dominican Republic</option>
                        <option value="EC">Ecuador</option>
                        <option value="EG">Egypt</option>
                        <option value="SV">El Salvador</option>
                        <option value="GQ">Equatorial Guinea</option>
                        <option value="ER">Eritrea</option>
                        <option value="EE">Estonia</option>
                        <option value="ET">Ethiopia</option>
                        <option value="FK">Falkland Islands (Malvinas)</option>
                        <option value="FO">Faroe Islands</option>
                        <option value="FJ">Fiji</option>
                        <option value="FI">Finland</option>
                        <option value="FR">France</option>
                        <option value="GF">French Guiana</option>
                        <option value="PF">French Polynesia</option>
                        <option value="TF">French Southern Territories</option>
                        <option value="GA">Gabon</option>
                        <option value="GM">Gambia</option>
                        <option value="GE">Georgia</option>
                        <option value="DE">Germany</option>
                        <option value="GH">Ghana</option>
                        <option value="GI">Gibraltar</option>
                        <option value="GR">Greece</option>
                        <option value="GL">Greenland</option>
                        <option value="GD">Grenada</option>
                        <option value="GP">Guadeloupe</option>
                        <option value="GU">Guam</option>
                        <option value="GT">Guatemala</option>
                        <option value="GG">Guernsey</option>
                        <option value="GN">Guinea</option>
                        <option value="GW">Guinea-Bissau</option>
                        <option value="GY">Guyana</option>
                        <option value="HT">Haiti</option>
                        <option value="HM">Heard Island and McDonald Islands</option>
                        <option value="VA">Holy See (Vatican City State)</option>
                        <option value="HN">Honduras</option>
                        <option value="HK">Hong Kong</option>
                        <option value="HU">Hungary</option>
                        <option value="IS">Iceland</option>
                        <option value="IN">India</option>
                        <option value="ID">Indonesia</option>
                        <option value="IR">Iran, Islamic Republic of</option>
                        <option value="IQ">Iraq</option>
                        <option value="IE">Ireland</option>
                        <option value="IM">Isle of Man</option>
                        <option value="IL">Israel</option>
                        <option value="IT">Italy</option>
                        <option value="JM">Jamaica</option>
                        <option value="JP">Japan</option>
                        <option value="JE">Jersey</option>
                        <option value="JO">Jordan</option>
                        <option value="KZ">Kazakhstan</option>
                        <option value="KE">Kenya</option>
                        <option value="KI">Kiribati</option>
                        <option value="KP">Korea, Democratic People's Republic of</option>
                        <option value="KR">Korea, Republic of</option>
                        <option value="KW">Kuwait</option>
                        <option value="KG">Kyrgyzstan</option>
                        <option value="LA">Lao People's Democratic Republic</option>
                        <option value="LV">Latvia</option>
                        <option value="LB">Lebanon</option>
                        <option value="LS">Lesotho</option>
                        <option value="LR">Liberia</option>
                        <option value="LY">Libya</option>
                        <option value="LI">Liechtenstein</option>
                        <option value="LT">Lithuania</option>
                        <option value="LU">Luxembourg</option>
                        <option value="MO">Macao</option>
                        <option value="MK">Macedonia, the former Yugoslav Republic of</option>
                        <option value="MG">Madagascar</option>
                        <option value="MW">Malawi</option>
                        <option value="MY">Malaysia</option>
                        <option value="MV">Maldives</option>
                        <option value="ML">Mali</option>
                        <option value="MT">Malta</option>
                        <option value="MH">Marshall Islands</option>
                        <option value="MQ">Martinique</option>
                        <option value="MR">Mauritania</option>
                        <option value="MU">Mauritius</option>
                        <option value="YT">Mayotte</option>
                        <option value="MX">Mexico</option>
                        <option value="FM">Micronesia, Federated States of</option>
                        <option value="MD">Moldova, Republic of</option>
                        <option value="MC">Monaco</option>
                        <option value="MN">Mongolia</option>
                        <option value="ME">Montenegro</option>
                        <option value="MS">Montserrat</option>
                        <option value="MA">Morocco</option>
                        <option value="MZ">Mozambique</option>
                        <option value="MM">Myanmar</option>
                        <option value="NA">Namibia</option>
                        <option value="NR">Nauru</option>
                        <option value="NP">Nepal</option>
                        <option value="NL">Netherlands</option>
                        <option value="NC">New Caledonia</option>
                        <option value="NZ">New Zealand</option>
                        <option value="NI">Nicaragua</option>
                        <option value="NE">Niger</option>
                        <option value="NG">Nigeria</option>
                        <option value="NU">Niue</option>
                        <option value="NF">Norfolk Island</option>
                        <option value="MP">Northern Mariana Islands</option>
                        <option value="NO">Norway</option>
                        <option value="OM">Oman</option>
                        <option value="PK">Pakistan</option>
                        <option value="PW">Palau</option>
                        <option value="PS">Palestinian Territory, Occupied</option>
                        <option value="PA">Panama</option>
                        <option value="PG">Papua New Guinea</option>
                        <option value="PY">Paraguay</option>
                        <option value="PE">Peru</option>
                        <option value="PH">Philippines</option>
                        <option value="PN">Pitcairn</option>
                        <option value="PL">Poland</option>
                        <option value="PT">Portugal</option>
                        <option value="PR">Puerto Rico</option>
                        <option value="QA">Qatar</option>
                        <option value="RE">Réunion</option>
                        <option value="RO">Romania</option>
                        <option value="RU">Russian Federation</option>
                        <option value="RW">Rwanda</option>
                        <option value="BL">Saint Barthélemy</option>
                        <option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
                        <option value="KN">Saint Kitts and Nevis</option>
                        <option value="LC">Saint Lucia</option>
                        <option value="MF">Saint Martin (French part)</option>
                        <option value="PM">Saint Pierre and Miquelon</option>
                        <option value="VC">Saint Vincent and the Grenadines</option>
                        <option value="WS">Samoa</option>
                        <option value="SM">San Marino</option>
                        <option value="ST">Sao Tome and Principe</option>
                        <option value="SA">Saudi Arabia</option>
                        <option value="SN">Senegal</option>
                        <option value="RS">Serbia</option>
                        <option value="SC">Seychelles</option>
                        <option value="SL">Sierra Leone</option>
                        <option value="SG">Singapore</option>
                        <option value="SX">Sint Maarten (Dutch part)</option>
                        <option value="SK">Slovakia</option>
                        <option value="SI">Slovenia</option>
                        <option value="SB">Solomon Islands</option>
                        <option value="SO">Somalia</option>
                        <option value="ZA">South Africa</option>
                        <option value="GS">South Georgia and the South Sandwich Islands</option>
                        <option value="SS">South Sudan</option>
                        <option value="ES">Spain</option>
                        <option value="LK">Sri Lanka</option>
                        <option value="SD">Sudan</option>
                        <option value="SR">Suriname</option>
                        <option value="SJ">Svalbard and Jan Mayen</option>
                        <option value="SZ">Swaziland</option>
                        <option value="SE">Sweden</option>
                        <option value="CH">Switzerland</option>
                        <option value="SY">Syrian Arab Republic</option>
                        <option value="TW">Taiwan, Province of China</option>
                        <option value="TJ">Tajikistan</option>
                        <option value="TZ">Tanzania, United Republic of</option>
                        <option value="TH">Thailand</option>
                        <option value="TL">Timor-Leste</option>
                        <option value="TG">Togo</option>
                        <option value="TK">Tokelau</option>
                        <option value="TO">Tonga</option>
                        <option value="TT">Trinidad and Tobago</option>
                        <option value="TN">Tunisia</option>
                        <option value="TR">Turkey</option>
                        <option value="TM">Turkmenistan</option>
                        <option value="TC">Turks and Caicos Islands</option>
                        <option value="TV">Tuvalu</option>
                        <option value="UG">Uganda</option>
                        <option value="UA">Ukraine</option>
                        <option value="AE">United Arab Emirates</option>
                        <option value="GB">Great Britain</option>
                        <option value="UK">United Kingdom</option>
                        <option value="US">United States</option>
                        <option value="UM">United States Minor Outlying Islands</option>
                        <option value="UY">Uruguay</option>
                        <option value="UZ">Uzbekistan</option>
                        <option value="VU">Vanuatu</option>
                        <option value="VE">Venezuela, Bolivarian Republic of</option>
                        <option value="VN">Viet Nam</option>
                        <option value="VG">Virgin Islands, British</option>
                        <option value="VI">Virgin Islands, U.S.</option>
                        <option value="WF">Wallis and Futuna</option>
                        <option value="EH">Western Sahara</option>
                        <option value="YE">Yemen</option>
                        <option value="ZM">Zambia</option>
                        <option value="ZW">Zimbabwe</option>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Reset Player Confirmation Model -->
<div id="mws-jui-dialog">
    <div class="mws-dialog-inner"></div>
</div>