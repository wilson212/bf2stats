<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span class="mws-i-24 i-create">Edit Config</span>
    </div>
    <div class="mws-panel-body">
        <div class="mws-panel-content">
            <p>
                This area allows you to alter the configuration of the Battlefield 2 Private Statistics system. This only alters the global settings defined on the "Gamespy" 
                database server. To alter in-game configurations, please edit the "python/bf2/BF2StatisticsConfig.py" file on your game server. 
            </p>
        </div>
        <form id="configForm" class="mws-form" method="POST" action="?task=editconfig">
            <input type="hidden" name="action" value="save_config" />
            
            <div class="mws-form-inline">
                <!-- Hidden Message -->
                <div class="mws-form-row">
                    <div id="js_message" style="display: none;"></div>
                </div>
                
                <h3 style="margin-left: 50px;">Database Settings</h3>
                <div class="mws-form-row">
                    <label>Database Host:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__db_host" value="{config.db_host}" title="MySQL Database Host. Typically LOCALHOST."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Database Port:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__db_port" value="{config.db_port}" title="MySQL database port. Typically 3306."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Database Name:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__db_name" value="{config.db_name}" title="Database Name to store stats."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Database Username:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__db_user" value="{config.db_user}" title="Username with rights to Database."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Database Password:</label>
                    <div class="mws-form-item small">
                        <input type="password" class="mws-textinput" name="cfg__db_pass" value="{config.db_pass}" title="Password for Database Username."/>
                    </div>
                </div>
                
                <!-- Stats Processing -->
                <h3 style="margin-left: 50px; margin-top: 35px;">Stats Processing Options</h3>
                <div class="mws-form-row">
                    <label>Ignore AI Players:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__stats_ignore_ai" title="Ignore AI Player (Bots) stats? Enabling this will force AI player stats to *NOT* be saved at the end of the round.">
                            <option value="1" <?php if('{config.stats_ignore_ai}' == '1') echo 'selected="selected"'; ?>>Yes</option>
                            <option value="0" <?php if('{config.stats_ignore_ai}' == '0') echo 'selected="selected"'; ?>>No</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Min. Game Time (Round):</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__stats_min_game_time" value="{config.stats_min_game_time}" title="Minimum game time of total round in SNAPSHOT before processing (Seconds)?"/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Min. Game Time (Player):</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__stats_min_player_game_time" value="{config.stats_min_player_game_time}" title="Minimum game time for each player in SNAPSHOT before processing (Seconds)?"/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Min. Players:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__stats_players_min" value="{config.stats_players_min}" title="Minimum players in SNAPSHOT before processing? Includes Bots."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Max. Players:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__stats_players_max" value="{config.stats_players_max}" title="Maximum players in SNAPSHOT before stopping processing (used to stop data hole loops). Includes Bots."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Rank Checking:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__stats_rank_check" title="Enable Rank Checking? Leave off, unless you are having problems with ranks being reset to 0.">
                            <option value="1" <?php if('{config.stats_rank_check}' == '1') echo 'selected="selected"'; ?>>Enabled</option>
                            <option value="0" <?php if('{config.stats_rank_check}' == '0') echo 'selected="selected"'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Rank Tenure:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__stats_rank_tenure" value="{config.stats_rank_tenure}" title="Minimum time to hold special ranks (ie, Sergeant Major of the Corps (SMoC) & General (GEN)). in DAYS"/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Auto Process SMOC:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__stats_process_smoc" title="Enabeling this option will enable SMOC promotion checking everytime a snapshot is pushed to the server. Rank tenure and score will be used to determine if a new 
                            Sergeant Major gets promoted to SMOC">
                            <option value="1" <?php if('{config.stats_process_smoc}' == '1') echo 'selected="selected"'; ?>>Enabled</option>
                            <option value="0" <?php if('{config.stats_process_smoc}' == '0') echo 'selected="selected"'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>GEN Rank Tenure:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__stats_process_gen" title="Enableing this option will only allow 1 general to have the 4 star rank at a time, Much like the SMOC rank. Rank tenure will be used to determine if a new 3 
                            star GEN (with the highest global score), gets promoted to 4 stars">
                            <option value="1" <?php if('{config.stats_process_gen}' == '1') echo 'selected="selected"'; ?>>Enabled</option>
                            <option value="0" <?php if('{config.stats_process_gen}' == '0') echo 'selected="selected"'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Awards Require Round Completion:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__stats_awds_complete" title="Require players to complete rounds before processing awards?">
                            <option value="1" <?php if('{config.stats_awds_complete}' == '1') echo 'selected="selected"'; ?>>Enabled</option>
                            <option value="0" <?php if('{config.stats_awds_complete}' == '0') echo 'selected="selected"'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Lan Override:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__stats_lan_override" value="{config.stats_lan_override}" title="Local Players IP 'Over-ride' for Country Code Lookup. Enter a properly formated non-private IP."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Player Override:</label>
                    <div class="mws-form-item small">
                        <textarea name="cfg__stats_local_pids" rows="50%" cols="100%" 
                            title="Individual Players IP 'Override' for Country Code Lookup. Enter one per line."><?php echo implode("\n", Config::Get('stats_local_pids')); ?>
                        </textarea>
                    </div>
                </div>
                
                <!-- Global Config -->
                <h3 style="margin-left: 50px; margin-top: 35px;">Global Game Server Configuration</h3>
                <div class="mws-form-row">
                    <label>Authorized Server Ip Adresses:</label>
                    <div class="mws-form-item small">
                        <textarea name="cfg__game_hosts" rows="50%" cols="100%" 
                            title="Authorised Game Servers. Enter one IPv4 Address per line (Supports CIDR x.x.x.x/y notation)."><?php echo implode("\n", Config::Get('game_hosts')); ?>
                        </textarea>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Custom MapID:</label>
                    <div class="mws-form-item small">
                        <input name="cfg__game_custom_mapid" type="text" class="mws-textinput required" value="{config.game_custom_mapid}" 
                            title="Default Custom MapID. This will be used for the first custom map detetced, all others will increment from this value (Default: 700).
                            NOTE: All Custom MapID's will be assigned based on the HIGHEST existing MapID.
                            WARNING: Set this ONLY once or you may lose access to you custom map data!"
                        />
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Offline Player ID:</label>
                    <div class="mws-form-item small">
                        <input name="cfg__game_default_pid" type="text" class="mws-textinput required" value="{config.game_default_pid}" title="Default Offline Player ID (PID). 
                            This will be used for the first offline player detected, all others will decrement from this value (Default: 29000000). NOTE: All offline PID's will be assigned based 
                            on the LOWEST existing PID."
                        />
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Unlocks Option:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__game_unlocks" title="Global Unlocks handling">
                            <option value="0" <?php if('{config.game_unlocks}' == '0') echo 'selected="selected"'; ?>>Earned</option>
                            <option value="1" <?php if('{config.game_unlocks}' == '1') echo 'selected="selected"'; ?>>All Unlocked</option>
                            <option value="-1" <?php if('{config.game_unlocks}' == '-1') echo 'selected="selected"'; ?>>Disabled</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Bonus Unlocks:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__game_unlocks_bonus" title="Allow bonus Unlocks based on Kit Badges?">
                            <option value="0" <?php if('{config.game_unlocks_bonus}' == '0') echo 'selected="selected"'; ?>>&lt;None&gt;</option>
                            <option value="1" <?php if('{config.game_unlocks_bonus}' == '1') echo 'selected="selected"'; ?>>Basic</option>
                            <option value="2" <?php if('{config.game_unlocks_bonus}' == '2') echo 'selected="selected"'; ?>>Veteran</option>
                            <option value="3" <?php if('{config.game_unlocks_bonus}' == '3') echo 'selected="selected"'; ?>>Expert</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Min Rank for unlocks:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__game_unlocks_bonus_min" title="Minimum Rank before allowing bonus unlocks">
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
                
                <!-- Admin Config -->
                <h3 style="margin-left: 50px; margin-top: 35px;">Admin Config</h3>
                <div class="mws-form-row">
                    <label>Admin Username:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__admin_user" value="{config.admin_user}" title="Username for access to BF2 Stats Admin System. NOTE: You will be forced to re-logon after this has been saved."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Admin Password:</label>
                    <div class="mws-form-item small">
                        <input type="password" class="mws-textinput required" name="cfg__admin_pass" value="{config.admin_pass}" title="Password for access to BF2 Stats Admin System. NOTE: You will be forced to re-logon after this has been saved."/>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Auth. Admin Ips:</label>
                    <div class="mws-form-item small">
                        <textarea name="cfg__admin_hosts" rows="50%" cols="100%" title="Authorised IP Addresses for Admin System (Localhost is ALWAYS enabled). 
                            Enter one IPv4 Address per line (Supports CIDR x.x.x.x/y notation)."><?php echo implode("\n", Config::Get('admin_hosts')); ?>
                        </textarea>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Stats Logging:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__debug_lvl" title="Stats Debug Logging Level (Includes all message above selected option).">
                            <option value="0" <?php if('{config.debug_lvl}' == '0') echo 'selected="selected"'; ?>>Security (0)</option>
                            <option value="1" <?php if('{config.debug_lvl}' == '1') echo 'selected="selected"'; ?>>Errors (1)</option>
                            <option value="2" <?php if('{config.debug_lvl}' == '2') echo 'selected="selected"'; ?>>Warning (2)</option>
                            <option value="3" <?php if('{config.debug_lvl}' == '3') echo 'selected="selected"'; ?>>Notice (3)</option>
                            <option value="4" <?php if('{config.debug_lvl}' == '4') echo 'selected="selected"'; ?>>Detailed (4)</option>
                        </select>
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>DB Backup Path:</label>
                    <div class="mws-form-item small">
                        <input type="text" class="mws-textinput required" name="cfg__admin_backup_path" value="{config.admin_backup_path}" title="Path to store database backup data (Include trailing '/'). 
                            This should be an absolute path as it is MySQL using it, not PHP (execpt for restores, then PHP needs it)."
                        />
                    </div>
                </div>
                <div class="mws-form-row">
                    <label>Ignore AI Players:</label>
                    <div class="mws-form-item small">
                        <select name="cfg__admin_ignore_ai" title="Ignore AI players in player lists?">
                            <option value="1" <?php if('{config.admin_ignore_ai}' == '1') echo 'selected="selected"'; ?>>Yes</option>
                            <option value="0" <?php if('{config.admin_ignore_ai}' == '0') echo 'selected="selected"'; ?>>No</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mws-button-row">
                <input type="submit" value="Submit" class="mws-button red" />
                <input type="reset" value="Reset" class="mws-button gray" />
            </div>
        </form>
    </div>
</div>