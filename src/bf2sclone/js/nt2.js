/* Modification of sorttable.js
 * http://www.kryogenix.org/code/browser/sorttable/
 * Original code by Stuart Langridge, November 2003
 * Modified by Andy Edmonds, December 2003
 *  Added alternateRowColors to color alternating rows
 * Modified by caspar.dk, November 2004
 *  Added cookie-memory to remember most recent sorting
 *  Released at http://netfactory.dk/technology/markup/javascript/
 * Modified by NM156, September 2005
 *  Fixed bottom sorting row array
 *  Added regex function to sort the bf2s time(to seconds) format
 */

var SORT_COLUMN_INDEX;

function sortables_init() {
    // Find all tables with class sortable and make them sortable
    if (!document.getElementsByTagName) return;
    tbls = document.getElementsByTagName("table");
    for (ti=0;ti<tbls.length;ti++)
    {
        thisTbl = tbls[ti];
        if (((' '+thisTbl.className+' ').indexOf("sortable") != -1) && (thisTbl.id)) {
            //initTable(thisTbl.id);
            ts_makeSortable(thisTbl);
        }
    }
        alternateRowColors();
}

function ts_makeSortable(table) {
    
    var sort_elm;
    var cookie_index = ts_get_cookie_index(table.id);
    var cookie_direction = ts_get_cookie_direction(table.id);
    
    if (table.rows && table.rows.length > 0) {
        var firstRow = table.rows[0];
    }
    if (!firstRow) return;
    
    // We have a first row: assume it's the header, and make its contents clickable links
    for (var i=0;i<firstRow.cells.length;i++)
    {
        var cell = firstRow.cells[i];
        var txt = ts_getInnerText(cell);
        
        if( (' '+cell.className+' ').indexOf("nosort") != -1 ) {
            // non sortable header
        } else {
            cell.innerHTML = '<a href="#" class="sortheader" onclick="ts_resortTable(this);return false;">'+txt+'<span class="sortarrow">&nbsp;&nbsp;&nbsp;</span></a>';
        }
        
        if(cookie_index==i)
        {
          sort_elm = cell.getElementsByTagName("A")[0];
        }
        
    }
    if(sort_elm)
    {
      if(cookie_direction==1)
      {
        ts_resortTable(sort_elm);
        ts_resortTable(sort_elm);
      }
      else
      {
        ts_resortTable(sort_elm);
      }
    }
}

function ts_getInnerText(el) {
    
    if( el.getAttribute('title') )
        return el.getAttribute('title');
    
    if (typeof el == "string" || typeof el == "undefined") { 
        s1 = new String(el);
        el = s1.replace(/,/g,"");
        return el 
    };
    if (el.innerText) {
        s1 = new String(el.innerText);
        el = s1.replace(/,/g,"");
        return el;
    } 
    var str = "";
    
    var cs = el.childNodes;
    var l = cs.length;
    for (var i = 0; i < l; i++) {
        switch (cs[i].nodeType) {
            case 1: //ELEMENT_NODE
                str += ts_getInnerText(cs[i]);
                break;
            case 3:    //TEXT_NODE
                str += cs[i].nodeValue;
                break;
        }
    }
    s1 = new String(str);
    el = s1.replace(/,/g,"");
    return el;
}

function ts_resortTable(lnk) {
    // get the span
    var span;
    for (var ci=0;ci<lnk.childNodes.length;ci++) {
        if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
    }
    var spantext = ts_getInnerText(span);
    var td = lnk.parentNode;
    var column = td.cellIndex;
    var table = getParent(td,'TABLE');
       
    // Work out a type for the column
    if (table.rows.length <= 1) return;
    var itm = ts_getInnerText(table.rows[1].cells[column]);
    
    s1 = new String(itm);
    itm = s1.replace(/,/g,"");
    
    sortfn = ts_sort_caseinsensitive;
    if (itm.match(/^\d{2,6}[:]\d{2}[:]\d{2}$/)) sortfn = ts_sort_bf2time; //New time sort for BF2Stats - NM
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^[£$]/)) sortfn = ts_sort_currency;

    if (itm.match(/^[\d\.]+$/)) sortfn = ts_sort_numeric;
    SORT_COLUMN_INDEX = column;
    //alert(sortfn);
    var firstRow = new Array();
    var newRows = new Array();
    var newFootRows = new Array();    //New array for 'sortbottom' rows -NM

    for (i=0;i<table.rows[0].length;i++) { firstRow[i] = table.rows[0][i]; }


//    for (j=1;j<table.rows.length;j++) { newRows[j-1] = table.rows[j]; }
    //Changed the row arrays to separate the data rows, from the 'sortbottom' rows. -NM
    var k=0;
    for (j=1; j<table.rows.length; j++) {
        if (!table.rows[j].className || (table.rows[j].className && (table.rows[j].className.indexOf('sortbottom') == -1)))
            newRows[j-1] = table.rows[j];
        else if (table.rows[j].className && (table.rows[j].className.indexOf('sortbottom') != -1)) {
            newFootRows[k] = table.rows[j];
            k++;
        }
    }

    newRows.sort(sortfn);
    //newFootRows.sort(sortfn); //'sortbottom' modification - Don't sort these -NM

    if (span.getAttribute("sortdir") == 'down') {
        ARROW = '&nbsp;&nbsp;&uarr;';
        newRows.reverse();
        span.setAttribute('sortdir','up');
        ts_set_cookie(table.id, SORT_COLUMN_INDEX + ",1");
    } else {
        ARROW = '&nbsp;&nbsp;&darr;';
        span.setAttribute('sortdir','down');
        ts_set_cookie(table.id, SORT_COLUMN_INDEX + ",0");
    }
    
    // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
    // don't do sortbottom rows
    for (i=0;i<newRows.length;i++) { if (!newRows[i].className || (newRows[i].className && (newRows[i].className.indexOf('sortbottom') == -1))) table.tBodies[0].appendChild(newRows[i]);}
    // do sortbottom rows only
    //for (i=0;i<newRows.length;i++) { if (newRows[i].className && (newRows[i].className.indexOf('sortbottom') != -1)) table.tBodies[0].appendChild(newRows[i]);}
    for (i=0;i<newFootRows.length;i++) { table.tBodies[0].appendChild(newFootRows[i]); }  // append the 'sortbottom' to the tables -NM

    // Delete any other arrows there may be showing
    var allspans = document.getElementsByTagName("span");
    for (var ci=0;ci<allspans.length;ci++) {
        if (allspans[ci].className == 'sortarrow') {
            if (getParent(allspans[ci],"table") == getParent(lnk,"table")) { // in the same table as us?
                allspans[ci].innerHTML = '&nbsp;&nbsp;&nbsp;';
            }
        }
    }
        
    span.innerHTML = ARROW;
        alternateRowColors();
}

function getParent(el, pTagName) {
    if (el == null) return null;
    else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())    // Gecko bug, supposed to be uppercase
        return el;
    else
        return getParent(el.parentNode, pTagName);
}

// convert BF2Stats time to seconds -NM
function ts_sort_bf2time(a,b) {

    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);

    var aaSplit = aa.split(":");
    var aaSeconds = ((aaSplit[2]-0) + ((aaSplit[1]-0) * 60) + ((aaSplit[0]-0) * 3600))-0; //Added -0 to strip off leading zeros..

    var bbSplit = bb.split(":");
    var bbSeconds = ((bbSplit[2]-0) + ((bbSplit[1]-0) * 60) + ((bbSplit[0]-0) * 3600))-0; //Added -0 to strip off leading zeros..

//dump("aaSeconds: " + aaSeconds);
//dump("bbSeconds: " + bbSeconds);

    //we now have the seconds
    aa = parseFloat(aaSeconds);
    bb = parseFloat(bbSeconds);

    if (isNaN(aa)) aa = 0;
    if (isNaN(bb)) bb = 0;

    return aa-bb;
}

function ts_sort_date(a,b) {
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    if (aa.length == 10) {
        dt1 = aa.substr(6,4)+aa.substr(3,2)+aa.substr(0,2);
    } else {
        yr = aa.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt1 = yr+aa.substr(3,2)+aa.substr(0,2);
    }
    if (bb.length == 10) {
        dt2 = bb.substr(6,4)+bb.substr(3,2)+bb.substr(0,2);
    } else {
        yr = bb.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt2 = yr+bb.substr(3,2)+bb.substr(0,2);
    }
    if (dt1==dt2) return 0;
    if (dt1<dt2) return -1;
    return 1;
}

function ts_sort_currency(a,b) { 
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    return parseFloat(aa) - parseFloat(bb);
}

function ts_sort_numeric(a,b) { 
    aa = parseFloat(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
    bb = parseFloat(ts_getInnerText(b.cells[SORT_COLUMN_INDEX]));

    if (isNaN(aa)) aa = 0;
    if (isNaN(bb)) bb = 0;

    return aa-bb;
}

function ts_sort_caseinsensitive(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();

    s1 = new String(aa);
    s2 = new String(bb);
    
    aa = s1.replace(/,/g,"");
    bb = s2.replace(/,/g,"");

    if (!isNaN(aa) && !isNaN(bb)) {
    return aa-bb;
    }
    
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}

function ts_sort_default(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);

    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}



function alternateRowColors() {
    var className = 'sortable';
    var rowcolor = '#dddddd';
    var defaultrowcolor = '#ffffff';
    var rows, arow;
    var tables = document.getElementsByTagName("table");
    var rowCount = 0;
    for(var i=0;i<tables.length;i++) {
        //dump(tables.item(i).className + " " + tables.item(i).nodeName + "\n");
        if(tables.item(i).className == className) {
            atable = tables.item(i);
            rows = atable.getElementsByTagName("tr");
            for(var j=0;j<rows.length;j++) {
                arow = rows.item(j);
                if(arow.nodeName == "TR") {
                    if(rowCount % 2) {
                        arow.style.backgroundColor = rowcolor;
                    } else {
                        // default case
                        arow.style.backgroundColor = defaultrowcolor;
                    }
                    rowCount++;
                }
            }
            rowCount = 0;
        }
    }
}

function ts_get_cookie(table_id)
{
  var cookie="; "+document.cookie.toString()+"; "
    var searchString = "; ts_" + table_id + "=";
    var start=cookie.indexOf(searchString);
    var slut;
    var cookieArray = cookie.split(";");

    if(start>-1)
    {
        slut = cookie.indexOf("; ",start+searchString.length);
        tsCookie = cookie.substring(start+searchString.length,slut);
        
        return tsCookie;
        /*
        tsCookie = tsCookie.split("%2C");

        if(tsCookie.length > 0)
        {
            return tsCookie[0];
        }
        */
    }
}


function ts_get_cookie_index(table_id)
{
  var cvalue = ts_get_cookie(table_id);
  if(cvalue)
  {
    cvalue = cvalue.split("%2C");
    if(cvalue.length > 0)
      {
        return cvalue[0];
    }
  }
}

function ts_get_cookie_direction(table_id)
{
  var cvalue = ts_get_cookie(table_id);
  if(cvalue)
  {
    cvalue = cvalue.split("%2C");
    if(cvalue.length > 0)
      {
        return cvalue[1];
    }
  }
}

function ts_set_cookie(table_id, cvalue)
{
  var d = new Date();
  d.setDate(d.getDate() + 365);
  var cookie_string = "ts_" + table_id + "=" + escape(cvalue);
  cookie_string += "; expires=" + d.toGMTString();
     document.cookie = cookie_string;
}



/* =================================================================================================
* NiceTitles
* 20th September 2004
* http://neo.dzygn.com/code/nicetitles
*
* NiceTitles turns your boring (X)HTML tags into a dynamic experience
* Modified by Kevin Hale for unfoldedorigami.com use. 
* Nicetitles now works with images, form fields and follows the mouse.
*
* Copyright (c) 2003 - 2004 Stuart Langridge, Paul McLanahan, Peter Janes, 
* Brad Choate, Dunstan Orchard, Ethan Marcotte, Mark Wubben, Kevin Hale
* 
*
* Licensed under MIT - http://www.opensource.org/licenses/mit-license.php
==================================================================================================*/

function NiceTitles(sTemplate, nDelay, nStringMaxLength, nMarginX, nMarginY, sContainerID, sClassName){
    var oTimer;
    var isActive = false;
    var showingAlready = false; //added var to start cursor tracking only AFTER delay occurs in show();
    var sNameSpaceURI = "http://www.w3.org/1999/xhtml";
    
    if(!sTemplate){ sTemplate = "attr(nicetitle)";}
    if(!nDelay || nDelay <= 0){ nDelay = false;}
    if(!nStringMaxLength){ nStringMaxLength = 80; }
    if(!nMarginX){ nMarginX = 15; }
    if(!nMarginY){ nMarginY = 35; }
    if(!sContainerID){ sContainerID = "nicetitlecontainer";}
    if(!sClassName){ sClassName = "nicetitle";}

    var oContainer = document.getElementById(sContainerID);
    if(!oContainer){
        oContainer = document.createElementNS ? document.createElementNS(sNameSpaceURI, "div") : document.createElement("div");
        oContainer.setAttribute("id", sContainerID);
        oContainer.className = sClassName;
        oContainer.style.display = "none";
        document.getElementsByTagName("body").item(0).appendChild(oContainer);
    }
    
    //=====================================================================
    // Method addElements (Public)
    //=====================================================================
    this.addElements = function addElements(collNodes, sAttribute){
        var currentNode, sTitle;
        
        for(var i = 0; i < collNodes.length; i++){
            currentNode = collNodes[i];
        
            sTitle = currentNode.getAttribute(sAttribute);
            if(sTitle){
                currentNode.setAttribute("nicetitle", sTitle);
                currentNode.removeAttribute(sAttribute);
                addEvent(currentNode, 'mouseover', show);
                addEvent(currentNode, 'mouseout', hide);
                addEvent(currentNode, 'mousemove', reposition); //added to allow cursor tracking
                addEvent(currentNode, 'focus', show);
                addEvent(currentNode, 'blur', hide);
                addEvent(currentNode, 'keypress', hide);
            }
        }

    }
    
    //=====================================================================
    // Other Methods (All Private)
    //=====================================================================
    function show(e){
        if (isActive){ hide(); }
        
        var oNode = window.event ? window.event.srcElement : e.currentTarget;
        if(!oNode.getAttribute("nicetitle")){ 
            while(oNode.parentNode){
                oNode = oNode.parentNode; // immediately goes to the parent, thus we can only have element nodes
                if(oNode.getAttribute("nicetitle")){ break;    }
            }
        }

        var sOutput = parseTemplate(oNode);
        setContainerContent(sOutput);
        var oPosition = getPosition(e, oNode);
        oContainer.style.left = oPosition.x;
        oContainer.style.top = oPosition.y;

        //added showingAlready. cursor tracks only after delay occurs
        if(nDelay){    
            oTimer = setTimeout(function(){oContainer.style.display = "block"; showingAlready = true;}, nDelay);
        } else {
            oContainer.style.display = "block";
        }

        isActive = true;        
        // Let's put this event to a halt before it starts messing things up
        window.event ? window.event.cancelBubble = true : e.stopPropagation();
    }
    
    function hide(){
        clearTimeout(oTimer);
        oContainer.style.display = "none";
        removeContainerContent();
        isActive = false;
        showingAlready = false;
    }
    
    //function added to allow cursor tracking by Kevin Hale
    function reposition(e){
        var oNode = window.event ? window.event.srcElement : e.currentTarget;

        var oPosition = getPosition(e, oNode);
        oContainer.style.left = oPosition.x;
        oContainer.style.top = oPosition.y;
        
        if(showingAlready){
        oContainer.style.display = "block";}
        else{
        oContainer.style.display = "none";}
        
        isActive = true;
        // Let's put this event to a halt before it starts messing things up
        window.event ? window.event.cancelBubble = true : e.stopPropagation();
    }

    function setContainerContent(sOutput){
        sOutput = sOutput.replace(/&/g, "&amp;");
        /*
        if(document.createElementNS && window.DOMParser){
            var oXMLDoc = (new DOMParser()).parseFromString("<root xmlns=\""+sNameSpaceURI+"\">"+sOutput+"</root>", "text/xml");
            var oOutputNode = document.importNode(oXMLDoc.documentElement, true);
            var oChild = oOutputNode.firstChild;
            var nextChild;
            while(oChild){
                nextChild = oChild.nextSibling; // Once the child is appended, the nextSibling reference is gone
                oContainer.appendChild(oChild);
                oChild = nextChild;
            }
        } else {
        */
            oContainer.innerHTML = sOutput;
        //}
    }
    
    function removeContainerContent(){
        var oChild = oContainer.firstChild;
        var nextChild;

        if(!oChild){ return; }
        while(oChild){
            nextChild = oChild.nextSibling;
            oContainer.removeChild(oChild);
            oChild =  nextChild;
        }
    }
    
    function getPosition(e, oNode){
        var oViewport = getViewport();
        var oCoords;
        var commonEventInterface = window.event ? window.event : e;

        if(commonEventInterface.type == "focus"){
            oCoords = getNodePosition(oNode);    
            oCoords.x += nMarginX;
            oCoords.y += nMarginY;            
        } else {
            oCoords = { x : commonEventInterface.clientX + oViewport.x + nMarginX, y : commonEventInterface.clientY + oViewport.y + nMarginY};
        }
        
        // oContainer needs to be displayed before width and height can be retrieved
        if(showingAlready == false) // if statement prevents flickering in os x firefox when tracking cursor
            {oContainer.style.visiblity = "hidden"; 
             oContainer.style.display =  "block";}
        var containerWidth = oContainer.offsetWidth;
        var containerHeight = oContainer.offsetHeight;
        if(showingAlready == false)
            {oContainer.style.display = "none"; 
             oContainer.style.visiblity = "visible";}

        if(oCoords.x + containerWidth + 10 >= oViewport.width + oViewport.x){
            oCoords.x = oViewport.width + oViewport.x - containerWidth - 10;
        }
        if(oCoords.y + containerHeight + 10 >= oViewport.height + oViewport.y){
            oCoords.y = oViewport.height + oViewport.y - containerHeight - oNode.offsetHeight - 10;
        }

        oCoords.x += "px";
        oCoords.y += "px";

        return oCoords;
    }

    function parseTemplate(oNode){
        var sAttribute, collOptionalAttributes;
        var oFound = {};
        var sResult = sTemplate;
        
        if(sResult.match(/content\(\)/)){
            sResult = sResult.replace(/content\(\)/g, getContentOfNode(oNode));
        }
        
        var collSearch = sResult.split(/attr\(/);
        for(var i = 1; i < collSearch.length; i++){
            sAttribute = collSearch[i].split(")")[0];
            oFound[sAttribute] = oNode.getAttribute(sAttribute);
            if(oFound[sAttribute] && oFound[sAttribute].length > nStringMaxLength){
                oFound[sAttribute] = oFound[sAttribute].substring(0, nStringMaxLength) + "...";
            }
        }
        
        var collOptional = sResult.split("?")
        for(var i = 1; i < collOptional.length; i += 2){
            collOptionalAttributes = collOptional[i].split("attr(");
            for(var j = 1; j < collOptionalAttributes.length; j++){
                sAttribute = collOptionalAttributes[j].split(")")[0];

                if(!oFound[sAttribute]){ sResult = sResult.replace(new RegExp("\\?[^\\?]*attr\\("+sAttribute+"\\)[^\\?]*\\?", "g"), "");    }
            }
        }
        sResult = sResult.replace(/\?/g, "");
        
        for(sAttribute in oFound){
            sResult = sResult.replace("attr\("+sAttribute+"\)", oFound[sAttribute]);
        }
        
        return sResult;
    }    
        
    function getContentOfNode(oNode){
        var sContent = "";
        var oSearch = oNode.firstChild;

        while(oSearch){
            if(oSearch.nodeType == 3){
                sContent += oSearch.nodeValue;
            } else if(oSearch.nodeType == 1 && oSearch.hasChildNodes){
                sContent += getContentOfNode(oSearch);
            }
            oSearch = oSearch.nextSibling
        }

        return sContent;
    }
    
    function getNodePosition(oNode){
        var x = 0;
        var y = 0;

        do {
            if(oNode.offsetLeft){ x += oNode.offsetLeft }
            if(oNode.offsetTop){ y += oNode.offsetTop }
        }    while((oNode = oNode.offsetParent) && !document.all) // IE gets the offset 'right' from the start

        return {x : x, y : y}
    }
    
    // Idea from 13thParallel: http://13thparallel.net/?issue=2002.06&title=viewport
    function getViewport(){
        var width = 0;
        var height = 0;
        var x = 0;
        var y = 0;
        
        if(document.documentElement && document.documentElement.clientWidth){
            width = document.documentElement.clientWidth;
            height = document.documentElement.clientHeight;
            x = document.documentElement.scrollLeft;
            y = document.documentElement.scrollTop;
        } else if(document.body && document.body.clientWidth){
            width = document.body.clientWidth;
            height = document.body.clientHeight;
            x = document.body.scrollLeft;
            y = document.body.scrollTop;
        }
        // we don't use an else if here, since Opera 7 tends to get the height on the documentElement wrong
        if(window.innerWidth){ 
            width = window.innerWidth - 18;
            height = window.innerHeight - 18;
        }
        
        if(window.pageXOffset){
            x = window.pageXOffset;
            y = window.pageYOffset;
        } else if(window.scrollX){
            x = window.scrollX;
            y = window.scrollY;
        }
        
        return {width : width, height : height, x : x, y : y };        
    }
}

//=====================================================================
// Event Listener
// by Scott Andrew - http://scottandrew.com
// edited by Mark Wubben, <useCapture> is now set to false
//=====================================================================
function addEvent(obj, evType, fn){
    if(obj.addEventListener){
        obj.addEventListener(evType, fn, false); 
        return true;
    } else if (obj.attachEvent){
        var r = obj.attachEvent('on'+evType, fn);
        return r;
    } else {
        return false;
    }
}

//=====================================================================
// Here the default nice titles are created
//=====================================================================
NiceTitles.autoCreation = function(){
    if(!document.getElementsByTagName){ return; }

    NiceTitles.autoCreated = new Object();

    NiceTitles.autoCreated.anchors = new NiceTitles("<p class=\"titletext\">attr(nicetitle)</p><p class=\"destination\">attr(href)</p>", 600);
    NiceTitles.autoCreated.acronyms = new NiceTitles("<p class=\"titletext\">content(): attr(nicetitle)</p>", 600);
    //for image nicetitles based off alt tags
    NiceTitles.autoCreated.images = new NiceTitles("<p class=\"destination\">attr(nicetitle)</p>", 100,99999);
    //for form nicetitles. just add title to input and textarea tags
    NiceTitles.autoCreated.input = new NiceTitles("<p class=\"destination\">attr(nicetitle)</p>", 600);
    
    
    NiceTitles.autoCreated.anchors.addElements(document.getElementsByTagName("a"), "title");
    NiceTitles.autoCreated.images.addElements(document.getElementsByTagName("img"), "alt"); //added
    
    NiceTitles.autoCreated.images.addElements(document.getElementsByTagName("li"), "alt"); //added
    NiceTitles.autoCreated.images.addElements(document.getElementsByTagName("span"), "alt"); //added
    
    NiceTitles.autoCreated.acronyms.addElements(document.getElementsByTagName("acronym"), "title");
    NiceTitles.autoCreated.acronyms.addElements(document.getElementsByTagName("abbr"), "title");
    NiceTitles.autoCreated.input.addElements(document.getElementsByTagName("input"), "title"); //added
    NiceTitles.autoCreated.input.addElements(document.getElementsByTagName("textarea"), "title"); //added
}

addEvent(window, "load", NiceTitles.autoCreation);
addEvent(window, "load", sortables_init);