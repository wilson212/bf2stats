<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-eye-open"></i> BattleSpy Config</span>
    </div>
    <div class="mws-panel-body no-padding">
        <form id="configForm" class="mws-form" method="POST" action="/ASP/config/save">
            <input type="hidden" name="action" value="save_config" />
            <div id="mws-validate-error" class="mws-form-message error" style="display:none;"></div>
            <div class="mws-form-inline">
                <fieldset>
                    <legend>BattleSpy Description</legend>
                    <div class="mws-form-row">
                        <p>
                            This area allows you to alter the configuration of the BattleSpy Anti-Cheat module.
                            BattleSpy is designed to flag players who exceed certain thresholds (set by you, the admin)
                            and log them into the database for further investigation. It will be up to you to investigate
                            the matter and decide what action to take (BattleSpy will never ban or suspend a player).
                            If there are no issues when a round is processed, no report will be generated.
                            <b>Hover over each field's label to get a description of each setting.</b>
                            Set any value to 0 to disable that particular flag.
                        </p>
                    </div>
                </fieldset>

                <!-- Stats Processing -->
                <fieldset class="mws-form-inline">
                    <legend>BattleSpy Configuration</legend>
                    <div id="js_message" class="alert loading" style="display: none;"></div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Enable BattleSpy"
                               data-content="Enabling this will enable the BattleSpy Anti-Cheat module.">
                            BattleSpy Enabled:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__battlespy_enable" title="">
                                <option value="1" <?php if('{battlespy_enable}' == '1') echo 'selected="selected"'; ?>>Yes</option>
                                <option value="0" <?php if('{battlespy_enable}' == '0') echo 'selected="selected"'; ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Promotion Verification"
                               data-content="Sets whether to verify player promotions based on global score and time played after each round.">
                            Promotion Verification:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" name="cfg__battlespy_rank_check" title="">
                                <option value="1" <?php if('{battlespy_rank_check}' == '1') echo 'selected="selected"'; ?>>Yes</option>
                                <option value="0" <?php if('{battlespy_rank_check}' == '0') echo 'selected="selected"'; ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max Score Per Minute"
                               data-content="Sets the maximum score per minute a player can achieve in a round without being flagged.">
                            Max Score Per Min:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s1"
                                       class="required mws-spinner"
                                       name="cfg__battlespy_max_spm"
                                       value="{battlespy_max_spm}"
                                       title="">
                                <label for="s1" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max Kills Per Minute"
                               data-content="Sets the maximum kills per minute a player can achieve in a round without being flagged.">
                            Max Kills Per Min:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s2"
                                       class="required mws-spinner"
                                       name="cfg__battlespy_max_kpm"
                                       value="{battlespy_max_kpm}"
                                       title="">
                                <label for="s2" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max Kills Per Target"
                               data-content="Sets the maximum kills per target a player can achieve in a round without being flagged.">
                            Max Kills Per Target:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s3"
                                       class="required mws-spinner"
                                       name="cfg__battlespy_max_target_kills"
                                       value="{battlespy_max_target_kills}"
                                       title="">
                                <label for="s3" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max Team Kills"
                               data-content="Sets the maximum team kills a player can achieve in a round without being flagged.">
                            Max Team Kills:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s4"
                                       class="required mws-spinner"
                                       name="cfg__battlespy_max_team_kills"
                                       value="{battlespy_max_team_kills}"
                                       title="">
                                <label for="s4" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max Awards"
                               data-content="Sets the maximum amount of awards a player can earn in a round without being flagged.">
                            Max Awards:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s5"
                                       class="required mws-spinner"
                                       name="cfg__battlespy_max_awards"
                                       value="{battlespy_max_awards}"
                                       title="">
                                <label for="s5" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max Weapon Accuracy"
                               data-content="Sets the maximum weapon accuracy a player can achieve in a round without being flagged. At least 3 bullets must be fired.">
                            Max Weapon Accuracy:
                        </label>
                        <div class="mws-form-item">
                            <div class="small">
                                <input type="text" id="s6"
                                       class="required mws-spinner"
                                       name="cfg__battlespy_max_accuracy"
                                       value="{battlespy_max_accuracy}"
                                       title="">
                                <label for="s6" class="error" generated="true" style="display:none"></label>
                            </div>
                        </div>
                    </div>
                    <div class="mws-form-row">
                        <label class="mws-form-label"
                               rel="popover"
                               data-trigger="hover"
                               data-placement="right"
                               data-original-title="Max Accuracy Weapons"
                               data-content="Sets which weapons can be flagged with max accuracy.">
                            Max Accuracy Weapons:
                        </label>
                        <div class="mws-form-item">
                            <select class="small" multiple="multiple" size="{weaponsCount}" name="cfg__battlespy_weapons[]">
                            {weapons}
                                <option value="{id}" {selected}>{name}</option>
                            {/weapons}
                            </select>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="mws-button-row">
                <input type="submit" value="Submit" class="btn btn-danger">
                <input type="reset" value="Reset" class="btn">
            </div>
        </form>
        <div id="mws-jui-dialog">
            <div class="mws-dialog-inner"></div>
        </div>
    </div>
</div>