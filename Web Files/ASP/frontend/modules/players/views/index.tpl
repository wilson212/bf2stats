<div id="jui-global-message" class="alert" style="display: none;"></div>
<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-users"></i> Player List</span>
    </div>
    <div class="mws-panel-toolbar">
        <div class="btn-toolbar">
            <div class="btn-group">
                <a id="add-new" href="#" class="btn"><i class="icol-add"></i> Add New Player</a>
                <a id="refresh" href="#" class="btn"><i class="icol-arrow-refresh"></i> Refresh Table</a>
                <a id="import-bots" href="#" class="btn"><i class="icol-application-get"></i> Import Bot Players</a>
                <a id="show-bots" href="#" class="btn" style="display: none"><i class="icol-lightbulb"></i> Show Bot Players</a>
                <a id="hide-bots" href="#" class="btn"><i class="icol-lightbulb-off"></i> Hide Bot Players</a>
                <a id="delete-bots" href="#" class="btn"><i class="icol-drive-delete"></i> Delete Bot Players</a>
            </div>
        </div>
    </div>
    <div class="mws-panel-body no-padding">
        <form class="mws-form">
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label">Additional Filters: </label>
                    <div class="mws-form-item">
                        <div class="mws-form-cols">
                            <div class="mws-form-col-2-8">
                                <select id="filterRank" name="filterRank" class="mws-select2">
                                    <option value="99">&rarr; Filter By Rank &larr;</option>
                                    {ranks}
                                    <option value="{iteration.id}">{value}</option>
                                    {/ranks}
                                </select>
                            </div>
                            <div class="mws-form-col-2-8">
                                <select id="filterCountry" name="filterCountry" class="mws-select2">
                                    <option value="99">&rarr; Filter By Country &larr;</option>
                                </select>
                            </div>
                            <div class="mws-form-col-2-8">
                                <select id="filterStatus" name="filterStatus" class="mws-select2">
                                    <option value="99">&rarr; Filter By Account Status &larr;</option>
                                    <option value="0">Active</option>
                                    <option value="1">Online</option>
                                    <option value="2">Inactive</option>
                                    <option value="3">Banned</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="mws-panel-body no-padding">
        <table class="mws-datatable-fn mws-table">
            <thead>
                <tr>
                    <th style="width: 3%"><span class="loading-cell"></span></th>
                    <th style="width: 7%">PID</th>
                    <th style="width: 5%">Rank</th>
                    <th style="width: 20%">Name</th>
                    <th>Country</th>
                    <th>Global Score</th>
                    <th>Join Date</th>
                    <th>Last Seen</th>
                    <th style="width: 5%">Status</th>
                    <th style="width: 7%">Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Import Bots Form -->
    <div id="import-form">
        <form id="mws-validate-2" class="mws-form" method="post" action="/ASP/players/import" enctype="multipart/form-data">
            <div id="mws-validate-error-2" class="mws-form-message error" style="display:none;"></div>
            <div id="jui-message-2" class="alert" style="display: none; width: 92%; margin-bottom: 20px;"></div>
            <div style="margin-bottom: 20px; font-weight: 500">
                This form allows you to insert bot players into the database, using the "/mods/bf2/ai/botNames.ai" file.
            </div>
            <div class="mws-form-inline">
                <div class="mws-form-row">
                    <label class="mws-form-label">Bot Names File</label>
                    <div class="mws-form-item">
                        <input class="required" type="file" id="botNamesFile" name="botNamesFile" accept=".ai">
                        <label for="botNamesFile" class="error" generated="true" style="display:none"></label>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Add New Player Ajax Model -->
    <div id="add-player-form">
        <form id="mws-validate" class="mws-form" method="post" action="/ASP/players/add">
            <input id="post-action" type="hidden" name="action" value="add">
            <input id="server-id" type="hidden" name="playerId" value="0">
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
                    <label id="emailLabel" class="mws-form-label">Update Email</label>
                    <div class="mws-form-item">
                        <input type="text" name="playerEmail" class="large" title="">
                    </div>
                </div>
                <div class="mws-form-row">
                    <label id="passwordLabel" class="mws-form-label">Update Password</label>
                    <div class="mws-form-item">
                        <input type="text" name="playerPassword" class="large" title="" autocomplete="off">
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
                            <option value="GB">United Kingdom</option>
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

    <!-- Delete Player Confirmation Model -->
    <div id="mws-jui-dialog">
        <div class="mws-dialog-inner"></div>
    </div>
</div>