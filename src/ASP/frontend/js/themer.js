$(document).ready(function() {
	var backgroundPattern = "images/core/bg/paper.png";
	var logo = "bf2logo.png";
	var baseColor = "#35353a";
	var highlightColor = "#c72a0e";
	var textColor = "#c72a0e";
	var textGlowColor = {r: 17, g: 17, b: 17, a: 0.5};
	
	var patterns = {
		Paper: {
			name: "Paper", 
			img: "frontend/images/core/bg/paper.png"
		}, 
		Blueprint: {
			name: "Blueprint", 
			img: "frontend/images/core/bg/blueprint.png"
		}, 
		Bricks: {
			name: "Bricks", 
			img: "frontend/images/core/bg/bricks.png"
		}, 
		Carbon: {
			name: "Carbon", 
			img: "frontend/images/core/bg/carbon.png"
		}, 
		Circuit: {
			name: "Circuit", 
			img: "frontend/images/core/bg/circuit.png"
		}, 
		Holes: {
			name: "Holes", 
			img: "frontend/images/core/bg/holes.png"
		}, 
		Mozaic: {
			name: "Mozaic", 
			img: "frontend/images/core/bg/mozaic.png"
		}, 
		Roof: {
			name: "Roof", 
			img: "frontend/images/core/bg/roof.png"
		}, 
		Stripes: {
			name: "Stripes", 
			img: "frontend/images/core/bg/stripes.png"
		}
	};
	
	var presets = {
		B2: {
			name: "Battlefield 2",
			logo: "bf2logo.png",
			baseColor: "35353a", 
			highlightColor: "ed1427", 
			textColor: "ed1427", 
			textGlowColor: {r: 17, g: 17, b: 17, a: 0.5}
		},
		SF: {
			name: "Special Forces",
			logo: "bf2sflogo.png",
			baseColor: "35353a", 
			highlightColor: "46bc3b", 
			textColor: "46bc3b", 
			textGlowColor: {r: 17, g: 17, b: 17, a: 0.5}
		},
		EF: {
			name: "Euro Force", 
			logo: "bf2eflogo.png",
			baseColor: "35353a", 
			highlightColor: "348de7", 
			textColor: "348de7", 
			textGlowColor: {r: 17, g: 17, b: 17, a: 0.5}
		},
		AF: {
			name: "Armored Fury",
			logo: "bf2aflogo.png",			
			baseColor: "35353a", 
			highlightColor: "e79d3a", 
			textColor: "e79d3a", 
			textGlowColor: {r: 17, g: 17, b: 17, a: 0.5}
		}
	};
	
	var backgroundTargets = 
	[
		"body", 
		"div#mws-container"
	];
	
	var baseColorTargets = 
	[
		"div#mws-sidebar-bg", 
	 	"div#mws-header", 
		".mws-panel .mws-panel-header", 
		"div#mws-error-container", 
		"div#mws-login", 
		"div#mws-login .mws-login-lock", 
		".ui-accordion .ui-accordion-header", 
		".ui-tabs .ui-tabs-nav", 
		".ui-datepicker", 
		".fc-event-skin", 
		".ui-dialog .ui-dialog-titlebar", 
		"div.jGrowl div.jGrowl-notification, div.jGrowl div.jGrowl-closer", 
		"div#mws-user-tools .mws-dropdown-menu .mws-dropdown-box", 
		"div#mws-user-tools .mws-dropdown-menu.toggled a.mws-dropdown-trigger"
	];
	
	var borderColorTargets = 
	[
	 	"div#mws-header"
	];
	
	var highlightColorTargets = 
	[
		"div#mws-searchbox input.mws-search-submit", 
		".mws-panel .mws-panel-header .mws-collapse-button span", 
		"div.dataTables_wrapper .dataTables_paginate div", 
		"div.dataTables_wrapper .dataTables_paginate span.paginate_active", 
		".mws-table tbody tr.odd:hover td", 
		".mws-table tbody tr.even:hover td", 
		".fc-state-highlight", 
		".ui-slider-horizontal .ui-slider-range", 
		".ui-slider-vertical .ui-slider-range", 
		".ui-progressbar .ui-progressbar-value", 
		".ui-datepicker td.ui-datepicker-current-day", 
		".ui-datepicker .ui-datepicker-prev .ui-icon", 
		".ui-datepicker .ui-datepicker-next .ui-icon", 
		".ui-accordion-header .ui-icon", 
		".ui-dialog-titlebar-close .ui-icon",
	];
	
	var textTargets = 
	[
		".mws-panel .mws-panel-header span", 
		"div#mws-navigation ul li.active a", 
		"div#mws-navigation ul li.active span", 
		"div#mws-user-tools #mws-username", 
		"div#mws-navigation ul li span.mws-nav-tooltip", 
		"div#mws-user-tools #mws-user-info #mws-user-functions #mws-username", 
		".ui-dialog .ui-dialog-title", 
		".ui-state-default", 
		".ui-state-active", 
		".ui-state-hover", 
		".ui-state-focus", 
		".ui-state-default a", 
		".ui-state-active a", 
		".ui-state-hover a", 
		".ui-state-focus a",
		"div#title",
		"div#dbver"
	];
	
	$("#mws-themer-getcss").bind("click", function(event) {
		$("#mws-themer-css-dialog textarea").val(generateCSS("../"));
		$("#mws-themer-css-dialog").dialog("open");
		event.preventDefault();
	});
	
	var presetDd = $('<select id="mws-theme-presets"></select>');
	for(var i in presets) {
		var option = $("<option></option>").text(presets[i].name).val(i);
		presetDd.append(option);
	}
	$("#mws-theme-presets-container").append(presetDd);
	
	presetDd.bind('change', function(event) {
		setTheme(presetDd.val());
		
		event.preventDefault();
	});
	
	
	var patternDd = $('<select id="mws-theme-patterns"></select>');
	for(var i in patterns) {
		var option = $("<option></option>").text(patterns[i].name).val(i);
		patternDd.append(option);
	}
	$("#mws-theme-pattern-container").append(patternDd);
	
	patternDd.bind('change', function(event) {
		var bg_id = patterns[patternDd.val()].img;
		updateBackground(bg_id, true);
		
		// Set cookie for new BG
		var date = new Date();
		date.setTime( date.getTime() + (30 * 24 * 60 * 60 * 1000) );
		var expires = "; expires=" + date.toGMTString();
		document.cookie = "themeBg=" + patternDd.val() + expires + "; path=/";
		
		event.preventDefault();
	});
	
	$("div#mws-themer #mws-themer-toggle").bind("click", function(event) {
		if($(this).hasClass("opened")) {
			$(this).toggleClass("opened").parent().animate({right: "0"}, "slow");
		} else {
			$(this).toggleClass("opened").parent().animate({right: "256"}, "slow");
		}
	});
	
	$("div#mws-themer #mws-textglow-op").slider({
		range: "min", 
		min:0, 
		max: 100, 
		value: 50, 
		slide: function(event, ui) {
			alpha = ui.value * 1.0 / 100.0;
			updateTextGlowColor(null, alpha);
		}
	});
	
	$("div#mws-themer #mws-themer-css-dialog").dialog({
		autoOpen: false, 
		title: "Theme CSS", 
		width: 500, 
		modal: true, 
		resize: false, 
		buttons: {
			"Close": function() { $(this).dialog("close"); }
		}
	});
	
	$("#mws-base-cp").ColorPicker({
		color: baseColor, 
		onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
		},
		onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
		},
		onChange: function (hsb, hex, rgb) {			
			updateBaseColor(hex, true);
		}
	});
	
	$("#mws-highlight-cp").ColorPicker({
		color: highlightColor, 
		onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
		},
		onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
		},
		onChange: function (hsb, hex, rgb) {			
			updateHighlightColor(hex, true);
		}
	});
	
	$("#mws-text-cp").ColorPicker({
		color: textColor, 
		onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
		},
		onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
		},
		onChange: function (hsb, hex, rgb) {			
			updateTextColor(hex, true);
		}
	});
	
	$("#mws-textglow-cp").ColorPicker({
		color: textGlowColor, 
		onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
		},
		onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
		},
		onChange: function (hsb, hex, rgb) {
			updateTextGlowColor(rgb, textGlowColor["a"], true);
		}
	});
	
	function setTheme(id)
	{
		var theme = presets[id];
		updateBaseColor(theme.baseColor);
		updateHighlightColor(theme.highlightColor);
		updateTextColor(theme.textColor);
		
		updateTextGlowColor(theme.textGlowColor, theme.textGlowColor.a);
		
		// add logo
		updateLogo(theme.logo);
		
		// Set cookie for theme
		var date = new Date();
		date.setTime( date.getTime() + (30 * 24 * 60 * 60 * 1000) );
		var expires = "; expires=" + date.toGMTString();
		document.cookie = "theme=" + id + expires + "; path=/";
		
		attachStylesheet();
	}
	
	function loadTheme()
	{
		// Load theme preset
		var name = "theme=";
		var pattern = "themeBg=";
		var value = null;
		var BG = null;
		
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) 
		{
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(name) == 0)
			{
				value = c.substring(name.length, c.length);
			}
			else if(c.indexOf(pattern) == 0)
			{
				BG = c.substring(pattern.length, c.length);
			}
		}
		
		if(value == null) {
			value = 'B2';
		}
		
		if(BG == null) {
			BG = 'Carbon';
		}
		
		// Set theme
		setTheme(value);
		$('#mws-theme-presets').find('option[value="' + value + '"]').attr('selected', 'selected'); 
		
		// Update background
		var bg_id = patterns[BG].img;
		updateBackground(bg_id, true);
		$('#mws-theme-patterns').find('option[value="' + BG + '"]').attr('selected', 'selected');
	}
	
	function updateBackground(bg, attach)
	{
		backgroundPattern = bg;
		
		if(attach == true)
			attachStylesheet();
	}
	
	function updateBaseColor(hex, attach)
	{
		baseColor = "#" + hex;
		$("#mws-base-cp").css('backgroundColor', baseColor);
		
		if(attach === true)
			attachStylesheet();
	}
	
	function updateHighlightColor(hex, attach)
	{
		highlightColor = "#" + hex;
		$("#mws-highlight-cp").css('backgroundColor', highlightColor);
		
		if(attach === true)
			attachStylesheet();
	}
	
	function updateTextColor(hex, attach)
	{
		textColor = "#" + hex;
		$("#mws-text-cp").css('backgroundColor', textColor);
		
		if(attach === true)
			attachStylesheet();
	}
	
	function updateTextGlowColor(rgb, alpha, attach)
	{
		if(rgb != null) {
			textGlowColor.r = rgb["r"];
			textGlowColor.g = rgb["g"];
			textGlowColor.b = rgb["b"];
			textGlowColor.a = alpha;
		} else {
			textGlowColor.a = alpha;
		}
		
		$("div#mws-themer #mws-textglow-op").slider("value", textGlowColor.a * 100);
		$("#mws-textglow-cp").css('backgroundColor', '#' + rgbToHex(textGlowColor.r, textGlowColor.g, textGlowColor.b));
		
		if(attach === true)
			attachStylesheet();
	}
	
	function updateLogo(image)
	{
		src = 'frontend/images/' + image;
		$('#logo').attr("src", src);
	}
	
	function attachStylesheet(basePath)
	{
		if($("#mws-stylesheet-holder").size() == 0) {
			$('body').append('<div id="mws-stylesheet-holder"></div>');
		}
		
		$("#mws-stylesheet-holder").html($('<style type="text/css">' + generateCSS(basePath) + '</style>'));
	}
	
	function generateCSS(basePath)
	{
		if(!basePath)
			basePath = "";
			
		var css = 
			backgroundTargets.join(", \n") + "\n" + 
			"{\n"+
			"	background-image:url('" + basePath + backgroundPattern + "');\n"+
			"}\n\n"+			
			baseColorTargets.join(", \n") + "\n" + 
			"{\n"+
			"	background-color:" + baseColor + ";\n"+
			"}\n\n"+
			borderColorTargets.join(", \n") + "\n" + 
			"{\n"+
			"	border-color:" + highlightColor + ";\n"+
			"}\n\n"+
			textTargets.join(", \n") + "\n" + 
			"{\n"+
			"	color:" + textColor + ";\n"+
			"	text-shadow:0 0 6px rgba(" + getTextGlowArray().join(", ") + ");\n"+
			"}\n\n"+
			highlightColorTargets.join(", \n") + "\n" + 
			"{\n"+
			"	background-color:" + highlightColor + ";\n"+
			"}\n";
			
		return css;
	}
	
	function getTextGlowArray()
	{
		var array = new Array();
		for(var i in textGlowColor)
			array.push(textGlowColor[i]);
			
		return array;
	}
	
	function rgbToHex(r, g, b)
	{
		var rgb = b | (g << 8) | (r << 16);
		return rgb.toString(16);
	}
	
	loadTheme();
});