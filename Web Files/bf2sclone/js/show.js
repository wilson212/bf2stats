var agt   = navigator.userAgent.toLowerCase();
var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));

function hide_mine(elmnt) {
	if( !is_ie ) return;
	var a = elmnt.getElementsByTagName("div");
	var div = a[0];
	elmnt.style.zIndex = 1;
	div.style.display = "none";
}

function show_mine(elmnt) {
	if( !is_ie ) return;
	var a = elmnt.getElementsByTagName("div");
	var div = a[0];
	elmnt.style.zIndex = 100;
	div.style.display = "block";
}