// Simplified Highway Browser JavaScript - No Polarization or TRAX
// Core WebGL and rendering functions

var gl;
var m_ShaderProgram;
var colOrder = [];
var numOrder = [];
var m_TextShaderProgram;
var storage_ColumnData; 
var m_Font;
var m_WhiteText;
var m_BlackText;
var m_GreenkText;
var m_AllTextArray;
var m_module = "{{ module }}";
var m_chr = {{ chr }};

var ROW_BATCH_SIZE = 200; 
var m_FileName = false;
var m_BookmarksCookieName = m_FileName + "_bookmarks";
var m_SelectedBookmarks = [];
var cruiseControlCookieName = "cruiseCookie";
var highSpeedCookieName = "highSpeedCookie";
var mf;
var rowDensity = [];

// Core WebGL and rendering functions
function initShaders() {
    var fragmentShader = getShader(gl, "shader-fs");
    var vertexShader = getShader(gl, "shader-vs");

    m_ShaderProgram = gl.createProgram();
    gl.attachShader(m_ShaderProgram, vertexShader);
    gl.attachShader(m_ShaderProgram, fragmentShader);
    gl.linkProgram(m_ShaderProgram);

    if (!gl.getProgramParameter(m_ShaderProgram, gl.LINK_STATUS)) {
        alert("Could not initialise shaders");
    }

    gl.setShader(m_ShaderProgram);

    m_ShaderProgram.vertexPositionAttribute = gl.getAttribLocation(m_ShaderProgram, "aVertexPosition");
    m_ShaderProgram.vertexColorAttribute = gl.getAttribLocation(m_ShaderProgram, "aVertexColor");
    m_ShaderProgram.vertexNormalAttribute = gl.getAttribLocation(m_ShaderProgram, "aVertexNormal");

    gl.enableVertexAttribArray(m_ShaderProgram.vertexPositionAttribute);
    gl.enableVertexAttribArray(m_ShaderProgram.vertexColorAttribute);
    gl.enableVertexAttribArray(m_ShaderProgram.vertexNormalAttribute);

    m_ShaderProgram.projMatrixUniform = gl.getUniformLocation(m_ShaderProgram, "uPMatrix");
    m_ShaderProgram.modelViewMatrixUniform = gl.getUniformLocation(m_ShaderProgram, "uMVMatrix");
    m_ShaderProgram.nMatrixUniform = gl.getUniformLocation(m_ShaderProgram, "uNMatrix");

    m_ShaderProgram.ambientColorUniform = gl.getUniformLocation(m_ShaderProgram, "uAmbientColor");
    m_ShaderProgram.lightingDirectionUniform = gl.getUniformLocation(m_ShaderProgram, "uLightingDirection");
    m_ShaderProgram.directionalColorUniform = gl.getUniformLocation(m_ShaderProgram, "uDirectionalColor");

    gl.uniform3f( m_ShaderProgram.ambientColorUniform, 0.5, 0.5, 0.5 );
    var lightDir = vec3.create( [ 5.0,  5.0, 15.0 ] );
    vec3.normalize( lightDir );
    gl.uniform3f( m_ShaderProgram.lightingDirectionUniform, lightDir[0], lightDir[1], lightDir[2] );
    gl.uniform3f( m_ShaderProgram.directionalColorUniform, 0.7, 0.7, 0.7 );

    var textFragmentShader = getShader(gl, "text-shader-fs");
    var textVertexShader = getShader(gl, "text-shader-vs");

    m_TextShaderProgram = gl.createProgram();
    gl.attachShader(m_TextShaderProgram, textFragmentShader);
    gl.attachShader(m_TextShaderProgram, textVertexShader);
    gl.linkProgram(m_TextShaderProgram);

    if (!gl.getProgramParameter(m_TextShaderProgram, gl.LINK_STATUS)) {
        alert("Could not initialise shaders");
    }

    gl.setShader(m_TextShaderProgram);

    m_TextShaderProgram.projMatrixUniform = gl.getUniformLocation(m_TextShaderProgram, "uPMatrix");
    m_TextShaderProgram.modelViewMatrixUniform = gl.getUniformLocation(m_TextShaderProgram, "uMVMatrix");
    m_TextShaderProgram.sampler = gl.getUniformLocation(m_TextShaderProgram, "uSampler");    
    m_TextShaderProgram.color = gl.getUniformLocation(m_TextShaderProgram, "uTextColor");    

    m_TextShaderProgram.vertexPositionAttribute = gl.getAttribLocation(m_TextShaderProgram, "aVertexPosition");
    m_TextShaderProgram.vertexTexCoordAttribute = gl.getAttribLocation(m_TextShaderProgram, "aTexCoords");

    gl.setShader(m_ShaderProgram);
}

var m_ViewMatrix = mat4.create();
var m_ModelViewMatrix = mat4.create();
var m_ProjMatrix = mat4.create();

function setMatrixUniforms() {
    gl.uniformMatrix4fv(gl.currentShader.projMatrixUniform, false, m_ProjMatrix);
    gl.uniformMatrix4fv(gl.currentShader.modelViewMatrixUniform, false, m_ModelViewMatrix);

    if ( gl.currentShader.nMatrixUniform ) {
        var normalMatrix = mat3.create();
        mat4.toInverseMat3(m_ModelViewMatrix, normalMatrix);
        mat3.transpose(normalMatrix);
        
        gl.uniformMatrix3fv( gl.currentShader.nMatrixUniform, false, normalMatrix);
    }
}

var m_InvertView = false;
var m_ColumnData;
var m_NumRows;
var m_CurrentRow = {{ UrlRow }};
var m_HoverItem;
var tab;
var m_RowCacheList = [];
var m_DyingRowCacheList = [];
var m_LookAt = vec3.create( [0, 0, 0] );
var m_Eye = vec3.create( {{ eyepos }} );
var m_AngleX = {{ anglex }};
var m_Distance = {{ distance }};
var m_OffsetX = {{ offsetx }};
var m_CruiseControl = false;
var CurrentURL = "{{ CurrentURL }}";

// Essential helper functions
function getRowData( row ) {
    for ( var i = 0; i < m_RowCacheList.length; ++i ) {
        var rc = m_RowCacheList[i];
        if ( ( row >= rc.startRow)  && ( row < rc.startRow + rc.rowCount ) ) {
            if ( rc.data ) {
                var relativeRow = row - rc.startRow;
                return rc.data["Rows"][ relativeRow ];
            }
            break;
        }
    }
    return null;
}

function getRowName( row ) {
    var data = getRowData( row );
    var d = ''
    if ( data ) {	
        if(data["Gene"] == null) data["Gene"] = ''
        if(data["Gene"]) d = ' - ' 
        if(data["Coords"] != 0) var coords = " - " + addCommas(  data["Coords"] )
        else {var coords = ""}
     
        return data["Name"] + "" + coords + "" + d + data["Gene"] ;
    }
    return "Loading...<img src='images/loader.gif'/>";
}

function getRowCoords( row ) {
    var data = getRowData( row );
    if ( data ) {	
        return data["Coords"]
    }
}

function getRowGeneName( row ) {
    var data = getRowData( row );
    if ( data ) {
        return data["Gene"]  ;
    }
    return "";
}

function updateRowName( ) {
    get("currentRowDisplay").innerHTML = getRowName( m_CurrentRow )
}

function setCurrentRow( row ) {
    m_CurrentRow = Math.max( 0, ( Math.min( m_NumRows - 1, row ) ) );
    $("#slider").slider( "value", m_CurrentRow );
    updateRowName();
    hideHoverText();
    closeLinkBox();
}

function jumpToRow( row ) {
    disableCruiseControl();
    setCurrentRow( row );
}

function changeThreshold( pvalue ) {
    var eyePosString = '[' + m_Eye[0] + ',' + m_Eye[1] + ',' + m_Eye[2] + ']';
    var url = CurrentURL + "?module=" + m_module + "&chr=" + m_chr;
    url += '&row=' + m_CurrentRow;
    url += '&distance=' + m_Distance;
    url += '&anglex=' + m_AngleX;
    url += '&offsetx=' + m_OffsetX;
    url += '&eyepos=' + escape( eyePosString );
    window.open(url + '&threshold=' + pvalue, "_self")
}

// Initialize the display page
function initDisplayPage() {
    resizeThings();
    updateRowName();
    
    $("#tabs").tabs();      
    $("#chkHighSpeed").button();      
    $("#btnForward").button( { 
        icons: { primary: "ui-icon-triangle-1-n" },
        text: false 
    });
    
    $("#btnBack").button( { 
        icons: { primary: "ui-icon-triangle-1-s" },
        text: false 
    });      
    
    $("#btnLeft").button( { 
        icons: { primary: "ui-icon-triangle-1-w" },
        text: false 
    }); 
    
    $("#btnMain").button( { 
        icons: { primary: "ui-icon-triangle-1-w" },
        text: true 
    });      

    $("#btnRight").button( { 
        icons: { primary: "ui-icon-triangle-1-e" },
        text: false 
    });      
    
    $("#searchButton").button( { 
        icons: { primary: "ui-icon-search" },
        text: false 
    }).click( doSearch );      

    $("#btnReset").button( ).click( function() {
        m_Eye = vec3.create( [0, 40, 70] );
        m_AngleX = 0;
        m_Distance = 80.0;
        m_OffsetX = 0.0;
        $("#zoomSlider").slider( "value", m_Distance );
        calcEyePos();
    });     

    $("#btnLink").button( { 
        icons: { primary: "ui-icon-link" }
    }).click( function() { 
        if ( get( 'linkBox' ) != null ) {
            return;
        }
        disableCruiseControl();
        $( "#btnLink" ).button( "option", "disabled", true );
        var eyePosString = '[' + m_Eye[0] + ',' + m_Eye[1] + ',' + m_Eye[2] + ']';
        var url = CurrentURL + "?module=" + m_module + "&chr=" + m_chr;
        url += '&row=' + m_CurrentRow;
        url += '&distance=' + m_Distance;
        url += '&anglex=' + m_AngleX;
        url += '&offsetx=' + m_OffsetX;
        url += '&eyepos=' + escape( eyePosString );
        
        var linkButton = $('#btnLink'); 
        var offset = linkButton.offset();
        
        $('body').append( '<div id="linkBox" class="ui-widget-header ui-corner-all">' 
            + 'Copy and paste the link below into email or IM.'
            + '<span id="linkBoxClose" class="ui-icon ui-icon-close" style="cursor:pointer; float:right; margin:0 7px 20px 0;"></span>' 
            + '<br/><input id="linkBoxInput"size="80" value="' + url + '"/></div>' );
        var box = $('div#linkBox');
        box.css('top', ( offset.top + linkButton.height() + 5 ) + 'px' ).
            css("left", ( ( offset.left  + linkButton.width()  ) - ( box.width() + 20 ) ) + 'px' ).fadeIn("fast");
        $('#linkBoxClose').click( function() {
            closeLinkBox();
        });
        
        var linkInput  = get("linkBoxInput");
        linkInput.focus();
        linkInput.select();
    } );
    
    $("#chkHelp").button( { 
        icons: { primary: "ui-icon-help" }
    }).click( function() {
        disableCruiseControl();
        vtip.enabled = this.checked;
        if ( this.checked ) {
            $("body").addClass( "helpMode");
        } else {
            $("body").removeClass( "helpMode");
        }
    } );
    
    $("#btnAddBookmark").button( { 
        icons: { primary: "ui-icon-plus" },
        text: false 
    }).click( function() {
        if  ( getRowData( m_CurrentRow ) != null ) {
            createBookmark( m_CurrentRow, getRowName( m_CurrentRow ), getRowColor( m_CurrentRow ) ); 
            writeBookmarksToCookie();
        }
    } );   
    
    $("#btnRemoveBookmark").button( { 
        icons: { primary: "ui-icon-minus" },
        text: false 
    }).click( function() {
        if ( m_SelectedBookmarks.length ) {
            $( "#dialog-confirm" ).dialog('open');
        }
    } );

    $("#chkCruiseControl").button().click( function() { 
        m_CruiseControl = this.checked;
    });      
    
    $("#chkInvert").button().click( function() { 
        m_InvertView = this.checked;
    });      

    $( "#bookmarks" ).selectable( 
        { cancel: 'a, span, input', 
            stop: function() {
                m_SelectedBookmarks = [];
                $( ".ui-selected", this ).each(function() {
                    m_SelectedBookmarks.push( this );
                });
            } 
        } 
    ); 

    $('fieldset legend').click(function() {
        $(this).next().fadeToggle("fast");
        resizeThings();
        return false;
    });

    $( "#dialog-confirm" ).dialog({
        resizable: false,
        modal: true,
        autoOpen: false, 
        buttons: {
            "Delete": function() {
                var bookmarks = get("bookmarks");
                for ( var i = 0; i <  m_SelectedBookmarks.length; ++i ) { 
                    bookmarks.removeChild( m_SelectedBookmarks[i] );
                }
                writeBookmarksToCookie();
                $( this ).dialog( "close" );
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        }
    });

    $("#reportTable a").button().click( function() { 
        var format = this.getAttribute("format");
        var reportType = this.getAttribute("reportType");
        var result = getUrlVar();
        window.open( 'report.php?module=' + m_module + "&chr=" + m_chr + '&row=' + m_CurrentRow + '&format=' + format 
            + '&reportType=' + reportType +  "&stageMask="+ m_ColumnData.columnMask  + "&threshold=" + result['threshold'] , '_blank' );
    } ); 
    
    $(window).resize(function () {  
        if( m_ResizeTimeout ) {
            clearTimeout( m_ResizeTimeout );
        }
        resizeThings( gl );
        m_ResizeTimeout = setTimeout( resizeCanvas, 200 );	
    });

    var cruiseCookie = $.cookie( cruiseControlCookieName );
    if ( cruiseCookie ) {
        $("#inputCruiseSpeed").attr("value", cruiseCookie );
    }

    var highSpeedCookie = $.cookie( highSpeedCookieName );
    if ( highSpeedCookie ) {
        $("#inputHighSpeed").attr("value", highSpeedCookie );
    }

    $("#inputCruiseSpeed").spinner({ min: -25, max: 25 }).change( function() {
        $.cookie( cruiseControlCookieName, $(this).spinner("value"), { expires: 10000000 } );
    } );
                       
    $("#inputHighSpeed").spinner({ min: 1, max: 25 }).change( function() { 
        $.cookie( highSpeedCookieName, $(this).spinner("value"), { expires: 10000000 } );
    } );
    
    generateBookmarksFromCookie();

    var request = new XMLHttpRequest();
    request.open("GET", "getColumns.php?module=" + m_module + "&chr=" + m_chr);
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            handleLoadedColumns(parseJSONAndCheckErrors(request.responseText) );
        }
    }
    request.send();
    
    var welcomeCookieName = "GWASWelcome";
    var welcomeCookie = $.cookie( welcomeCookieName );
    if (! welcomeCookie ) {
        $( "#welcomeDialog" ).dialog({
            resizable: false,
            modal: true,
            autoOpen:true, 
            minWidth: 400,
            close : function() {  
                $.cookie( welcomeCookieName, "true", { expires: 10000000 } ) 
                $("#chkHelp").click();
                $("body").addClass( "helpMode");
                vtip.enabled = true;
            },
            buttons: {
                "OK": function() {
                    $( this ).dialog( "close" );
                }		   
            } 
        });
    } else {
        $( "#welcomeDialog" ).remove();
    }
}

// Additional helper functions
function disableCruiseControl() {
    if ( m_CruiseControl ) {
        $("#chkCruiseControl").click();
        m_CruiseControl = false;
    }
}

function closeLinkBox() {
    $("div#linkBox").fadeOut("fast", function() { $(this).remove(); } );       
    $( "#btnLink" ).button( "option", "disabled", false );
}

function hideHoverText() {
    $("#hoverText").fadeOut();
    clearTimeout( m_MouseHoverTimeout );
    m_HoverItem = null;
}

function getRowColor( row ) {
    return "white";
}

function createBookmark( row, name, color ) {
    var li = document.createElement( "li" );
    li.innerHTML = '<span style="background:' + color + ';width:16px;float:left;border:1px solid black">&nbsp;</span>&nbsp;<a href="javascript:jumpToRow( ' 
        + row +' ) ;">' + name + '</a></li>';
    
    li.row = row;
    li.name = name;
    li.color = color;
    var list = get("bookmarks");
    list.appendChild( li );
    li.anchor = li.lastChild;

    $( li ).dblclick( function() {
        editBookmarkName( li );
    } );
}

function generateBookmarksFromCookie() {
    var cookie = $.cookie( m_BookmarksCookieName );
    if ( !cookie ) return;
    
    var cookieData = null;
    try {
        cookieData = JSON.parse( cookie );
    } catch ( e ) {
        return;
    }
    
    for ( var i = 0; i < cookieData.length; ++i ) {
        var c = cookieData[i];
        createBookmark( c.row, c.name, c.color );
    }
}

function writeBookmarksToCookie() {
    var cookieData = [];
    var i = 0;
    $("#bookmarks li").each( function() {
        var c = new Object();
        c.name = this.name;
        c.row = this.row;
        c.color = this.color;
        cookieData[i] = c;
        ++i;
    } );
    
    if ( i == 0 ) {
        $.cookie( m_BookmarksCookieName, null );
    } else {
        var cookieString = JSON.stringify( cookieData );
        $.cookie( m_BookmarksCookieName, cookieString,  { expires: 10000000 }  );
    }
}

function doSearch() {
    var text = get("searchBox").value;

    if ( !text) {
        get("searchBox").focus();
        return;
    }
    
    $("#searchResults").hide( "fade" );
    get("waitingSearchResults" ).style.display="inline";
    get("searchButton" ).style.display="none";
    var request = new XMLHttpRequest();
    request.open("GET", "search.php?module=" + m_module + "&chr=" + m_chr + "&search=" + text + "&type=" + get("searchType").value );
    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            handleSearchResults(parseJSONAndCheckErrors(request.responseText) );
        }
    }
    request.send();
}

function handleSearchResults( data ) {	 
    get("waitingSearchResults" ).style.display="none";
    get("searchButton" ).style.display="inline";
    $("#searchResults").show( "fade" );
    var results = get("searchResults" );
    var resultsHTML = "";
    
    if ( data == -1 ) {
        resultsHTML += "<div style='cursor:pointer'  > Not found </div>";
    } else {
        for ( var i = 0; i < data.length; ++i ) {
            if( data[i].length == 2 || data[i][2] == m_chr) {
                resultsHTML += "<div style='cursor:pointer'  onClick='jumpToRow(" + data[i][1] + ");'>" + data[i][0] + "</div>";
            } else {
                resultsHTML += "<div style='cursor:pointer;color:grey;'  onClick='jumpToRowAndChr(" + data[i][1] + ", " + data[i][2] + ");'>" + data[i][0] + "</div>";
            }
        }
    }

    results.innerHTML = resultsHTML; 
}

function jumpToRowAndChr( row, chrm ) {
    window.open("/display.php?module="+ m_module +"&chr="+ chrm +"&row="+ row);
}

function getUrlVar(){
    var urlVar = window.location.search; 
    var arrayVar = []; 
    var valueAndKey = []; 
    var resultArray = []; 
    arrayVar = (urlVar.substr(1)).split('&'); 
    if(arrayVar[0]=="") return false; 
    for (i = 0; i < arrayVar.length; i ++) { 
        valueAndKey = arrayVar[i].split('='); 
        resultArray[valueAndKey[0]] = valueAndKey[1];
    }
    return resultArray; 
}

function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

// Placeholder functions for missing functionality
function drawRowCache( rowCache, gl, eyeMatrix ) {
    // Implementation needed from original
}

function drawDyingRowCache( rowCache, gl, eyeMatrix ) {
    // Implementation needed from original
}

function updateRowCaches() {
    // Implementation needed from original
}

function handleLoadedColumns( columnData ) {
    m_ColumnData = columnData;
    m_ColumnData.numVisibleColumns = m_ColumnData["NumColumns"];
    m_NumRows = m_ColumnData["NumRows"];
    createStageCheckboxes();
    webGLStart();
    var size = m_ColumnData.numVisibleColumns * 0.5;
    m_OffsetX = Math.max( m_OffsetX, -size );       
    m_OffsetX = Math.min( m_OffsetX,  size );       
    var canvas = get("canvas");
    canvas.focus();
    canvas.className = "canvasBorder";
    jumpToRow( m_CurrentRow );
}

function createStageCheckboxes() {
    // Implementation needed from original
}

function webGLStart() {
    // Implementation needed from original
}

function resizeThings() {
    // Implementation needed from original
}

function resizeCanvas() {
    // Implementation needed from original
}

function calcEyePos() {
    closeLinkBox();
    m_Eye[0] = ( Math.sin( m_AngleX ) * m_Distance ) + m_LookAt[0];
    m_Eye[2] = ( Math.cos( m_AngleX ) * m_Distance ) + m_LookAt[2];
}

function editBookmarkName( li ) {
    // Implementation needed from original
}

// Initialize when page loads
$(document).ready(function() {
    if (typeof initDisplayPage !== 'undefined') {
        initDisplayPage();
    }
});
