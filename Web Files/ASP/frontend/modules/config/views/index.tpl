<div id="js_message" class="alert loading" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-tools"></i> Edit System Configuration</span>
    </div>
    <div id="mws-jui-dialog">
        <div class="mws-dialog-inner"><p>HI</p></div>
    </div>
    <div class="mws-panel-body no-padding">
        <form id="configForm" class="mws-form" method="POST" action="/ASP/config/save">
            <input type="hidden" name="action" value="save_config" />
            <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
            <div class="mws-form-inline">
                <fieldset>
                    <div class="mws-form-row">
                        <p>
                            This area allows you to alter the configuration of the Battlefield 2 Private Statistics system.
                            This only alters the global settings defined on the "Gamespy" database server. To alter in-game
                            configurations, please edit the "python/bf2/BF2StatisticsConfig.py" file on your game server.
                            <b>Hover over each field's label to get a description of each setting.</b>
                        </p>
                    </div>
                </fieldset>

                <!-- Admin Config -->
                <fieldset class="mws-form-inline">
                    <legend>Admin Config</legend>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Admin Panel Username"
                               data-content="Username for access to BF2 Stats Admin System. NOTE: You will be forced to re-logon after this has been saved.">
                            Admin Username:
                        </label>
                        <div class="mws-form-item">
                            <input type="text" class="small required" name="cfg__admin_user" value="{config.admin_user}" title="">
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Admin Panel Password"
                               data-content="Password for access to BF2 Stats Admin System. NOTE: You will be forced to re-logon after this has been saved.">
                            Admin Password:
                        </label>
                        <div class="mws-form-item">
                            <input type="password" class="small required" name="cfg__admin_pass" value="{config.admin_pass}" title="">
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Admin Panel IP Whitelist"
                               data-content="Authorised IP Addresses for Admin System (Localhost is ALWAYS enabled). Enter one IP Address per line. CIDR (x.x.x.x/y) notation is supported.">
                            Auth. Admin Ips:
                        </label>
                        <div class="mws-form-item">
                            <input class="small required"
                                    style="width: 100% !important;"
                                    name="cfg__admin_hosts"
                                    value="<?php echo join(',', \System\Config::Get('admin_hosts')); ?>"
                                    title=""
                                    data-role="tagsinput"
                            >
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Admin Timezone"
                               data-content="Sets the timezone to use for date methods.">
                            Admin Timezone:
                        </label>
                        <div class="mws-form-item">
                            <select class="mws-select2 small" name="cfg__admin_timezone" title="">
                                <?php
                                    foreach($timezones as $region => $list)
                                {
                                print '<optgroup label="' . $region . '">' . "\n";
                                    foreach ($list as $timezone => $name)
                                    {
                                    $selected = ($timezone == $config['admin_timezone']) ? " selected='selected'" : "";
                                    print '<option value="'. $timezone .'"'. $selected .'>'. $name .'</option>' . "\n";
                                    }
                                    print '</optgroup>' . "\n";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Stats Logging Level"
                               data-content="Stats Debug Logging Level (Includes all message above selected option).">
                            Stats Logging:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__debug_lvl" title="">
                                <option value="0" <?php if('{config.debug_lvl}' == '0') echo 'selected="selected"'; ?>>Security (0)</option>
                                <option value="1" <?php if('{config.debug_lvl}' == '1') echo 'selected="selected"'; ?>>Errors (1)</option>
                                <option value="2" <?php if('{config.debug_lvl}' == '2') echo 'selected="selected"'; ?>>Warning (2)</option>
                                <option value="3" <?php if('{config.debug_lvl}' == '3') echo 'selected="selected"'; ?>>Notice (3)</option>
                                <option value="4" <?php if('{config.debug_lvl}' == '4') echo 'selected="selected"'; ?>>Detailed (4)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Stats API Access"
                               data-content="Sets the access level for the stats aspx calls.">
                            Stats API Access:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__stats_strict_api" title="">
                                <option value="0" <?php if('{config.stats_strict_api}' == '0') echo 'selected="selected"'; ?>>Public</option>
                                <option value="1" <?php if('{config.stats_strict_api}' == '1') echo 'selected="selected"'; ?>>
                                Restricted to BF2 Client and Servers only
                                </option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <!-- Stats Processing -->
                <fieldset class="mws-form-inline">
                    <legend>Stats Processing Options</legend>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Ignore AI Players"
                               data-content="Enabling this will force AI player stats to *NOT* be saved at the end of the round.">
                            Ignore AI Players:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__stats_ignore_ai" title="">
                                <option value="1" <?php if('{config.stats_ignore_ai}' == '1') echo 'selected="selected"'; ?>>Yes</option>
                                <option value="0" <?php if('{config.stats_ignore_ai}' == '0') echo 'selected="selected"'; ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Min. Round Game Time"
                               data-content="Minimum amount of the the game must have played to be accepted for processing (in seconds).">
                            Min. Game Time (Round):
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s1" name="cfg__stats_min_game_time" class="required mws-spinner" value="{config.stats_min_game_time}" title="">
                                <label for="s1" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Min. Player Game Time"
                               data-content="Minimum amount of that each player must have played to be accepted for processing (in seconds).">
                            Min. Game Time (Player):
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input rel="popover" type="text" id="s2" name="cfg__stats_min_player_game_time" class="required mws-spinner" value="{config.stats_min_player_game_time}">
                                <label for="s2" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Min. Players Played"
                               data-content="Minimum number of players who must play in the round for the game to be accepted for processing.">
                            Min. Players:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s3" name="cfg__stats_players_min" class="required mws-spinner" value="{config.stats_players_min}" title="Minimum players in SNAPSHOT before processing? Includes Bots.">
                                <label for="s3" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max. Players Played"
                               data-content="Maximum number of players who can play in the round for the game to be accepted for processing.">
                            Max. Players:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s4" name="cfg__stats_players_max" class="required mws-spinner" value="{config.stats_players_max}" title="Maximum players in SNAPSHOT before stopping processing (used to stop data hole loops). Includes Bots.">
                                <label for="s4" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Promotions Require Round Completion"
                               data-content="If enabled, a player must complete the round to have his promotion to a higher rank accepted.">
                            Promotions Require Round Completion:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__stats_rank_complete" title="">
                                <option value="1" <?php if('{config.stats_rank_complete}' == '1') echo 'selected="selected"'; ?>>Enabled</option>
                                <option value="0" <?php if('{config.stats_rank_complete}' == '0') echo 'selected="selected"'; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Awards Require Round Completion"
                               data-content="If enabled, a player must complete the round to have his awards accepted.">
                            Awards Require Round Completion:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__stats_awds_complete" title="">
                                <option value="1" <?php if('{config.stats_awds_complete}' == '1') echo 'selected="selected"'; ?>>Enabled</option>
                                <option value="0" <?php if('{config.stats_awds_complete}' == '0') echo 'selected="selected"'; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Lan IP Override"
                               data-content="Local Players IP 'Over-ride' for Country Code Lookup. Enter a properly formatted non-private IP.">
                            Lan IP Override:
                        </label>
                        <div class="mws-form-item">
                            <input type="text" class="small required" name="cfg__stats_lan_override" value="{config.stats_lan_override}" title="">
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Save Snapshots"
                               data-content="Save a copy of the Game's Snapshot after game has successfully processed and saved into the database?">
                            Save Snapshots:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__stats_save_snapshot" title="">
                                <option value="1" <?php if('{config.stats_save_snapshot}' == '1') echo 'selected="selected"'; ?>>Enabled</option>
                                <option value="0" <?php if('{config.stats_save_snapshot}' == '0') echo 'selected="selected"'; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <!-- Global Config -->
                <fieldset class="mws-form-inline">
                    <legend>Global Game Server Configuration</legend>
                    <div class="mws-form-row">
                        <label class="mws-form-label">Unlocks Option:</label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__game_unlocks" title="Global Unlocks handling">
                                <option value="0" <?php if('{config.game_unlocks}' == '0') echo 'selected="selected"'; ?>>Earned</option>
                                <option value="1" <?php if('{config.game_unlocks}' == '1') echo 'selected="selected"'; ?>>All Unlocked</option>
                                <option value="-1" <?php if('{config.game_unlocks}' == '-1') echo 'selected="selected"'; ?>>Disabled</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Bonus Unlocks"
                               data-content="Allow bonus Unlocks based on Kit Badges?">
                            Bonus Unlocks:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__game_unlocks_bonus" title="">
                                <option value="0" <?php if('{config.game_unlocks_bonus}' == '0') echo 'selected="selected"'; ?>>&lt;None&gt;</option>
                                <option value="1" <?php if('{config.game_unlocks_bonus}' == '1') echo 'selected="selected"'; ?>>Basic</option>
                                <option value="2" <?php if('{config.game_unlocks_bonus}' == '2') echo 'selected="selected"'; ?>>Veteran</option>
                                <option value="3" <?php if('{config.game_unlocks_bonus}' == '3') echo 'selected="selected"'; ?>>Expert</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Minimum Rank for Unlocks"
                               data-content="Minimum Rank before allowing bonus unlocks.">
                            Min Rank for unlocks:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__game_unlocks_bonus_min" title="">
                                <option value="0" <?php if('{config.game_unlocks_bonus_min}' == '0') echo 'selected="selected"'; ?>>Private (0)</option>
                                <option value="1" <?php if('{config.game_unlocks_bonus_min}' == '1') echo 'selected="selected"'; ?>>Pvt First Class (1)</option>
                                <option value="2" <?php if('{config.game_unlocks_bonus_min}' == '2') echo 'selected="selected"'; ?>>Lance Corporal (2)</option>
                                <option value="3" <?php if('{config.game_unlocks_bonus_min}' == '3') echo 'selected="selected"'; ?>>Corporal (3)</option>
                                <option value="4" <?php if('{config.game_unlocks_bonus_min}' == '4') echo 'selected="selected"'; ?>>Sergeant (4)</option>
                                <option value="5" <?php if('{config.game_unlocks_bonus_min}' == '5') echo 'selected="selected"'; ?>>Staff Sergeant (5)</option>
                                <option value="6" <?php if('{config.game_unlocks_bonus_min}' == '6') echo 'selected="selected"'; ?>>Gunnery Sergeant (6)</option>
                                <option value="7" <?php if('{config.game_unlocks_bonus_min}' == '7') echo 'selected="selected"'; ?>>&lt;Field Officer&gt; (7-11)</option>
                                <option value="12" <?php if('{config.game_unlocks_bonus_min}' == '12') echo 'selected="selected"'; ?>>&lt;Officer&gt; (12+)</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <!-- Special Tasks Config -->
                <fieldset class="mws-form-inline">
                    <legend>Stats Admin Config</legend>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Rising Star Leaderboard Interval"
                               data-content="Indicates the number of days between Rising Star leaderboard updates.">
                            Rising Star Interval:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s31" name="cfg__stats_risingstar_interval" class="required mws-spinner" value="{config.stats_risingstar_interval}" title="Number of Days">
                                <label for="s31" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="SMOC Selection Interval"
                               data-content="Indicates the number of days between Sergeant Major of the Corps selections.">
                            SMOC Selection Interval:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s32" name="cfg__stats_smoc_interval" class="required mws-spinner" value="{config.stats_smoc_interval}" title="Number of Days">
                                <label for="s32" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="4-Star General Selection Interval"
                               data-content="Indicates the number of days between 4-Star General selections.">
                            4-Star Selection Interval:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s33" name="cfg__stats_general_interval" class="required mws-spinner" value="{config.stats_general_interval}" title="Number of Days">
                                <label for="s33" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="4-Star General Selection Mode"
                               data-content="Defines how the stats system will treat promotions to 4-star general.">
                            4-Star Selection Mode:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__stats_general_mode" title="" disabled="disabled">
                                <option value="0" <?php if('{config.stats_general_mode}' == '0') echo 'selected="selected"'; ?>>Clan Mode (Only 1 4-star General at a time)</option>
                                <option value="1" <?php if('{config.stats_general_mode}' == '1') echo 'selected="selected"'; ?>>EA Mode (Many 4-Star Generals)</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="mws-button-row">
                <input type="submit" value="Submit" class="btn btn-danger">
                <input type="reset" value="Reset" class="btn ">
            </div>
        </form>
    </div>
</div>