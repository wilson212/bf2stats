;(function ($, window, document, undefined) {

    $(document).ready(function() {

        // Close buttons
        $('.close-bt').live('click', function()
        {
            $(this).parent().fadeOut(500);
        });

        /* Collapsible Panels */
        $( '.mws-panel.mws-collapsible' ).each(function(i, element) {
            var p = $( element ),
                header = p.find( '.mws-panel-header' );

            if( header && header.length) {
                var btn = $('<div class="mws-collapse-button mws-inset"><span></span></div>').appendTo(header);
                $('span', btn).on( 'click', function(e) {
                    var p = $( this ).parents( '.mws-panel' );
                    if( p.hasClass('mws-collapsed') ) {
                        p.removeClass( 'mws-collapsed' )
                            .children( '.mws-panel-inner-wrap' ).hide().slideDown( 250 );
                    } else {
                        p.children( '.mws-panel-inner-wrap' ).slideUp( 250, function() {
                            p.addClass( 'mws-collapsed' );
                        });
                    }
                    e.preventDefault();
                });
            }

            if( !p.children( '.mws-panel-inner-wrap' ).length ) {
                p.children( ':not(.mws-panel-header)' )
                    .wrapAll( $('<div></div>').addClass( 'mws-panel-inner-wrap' ) );
            }
        });

        /* Side dropdown menu */
        $("div#mws-navigation ul li a, div#mws-navigation ul li span")
            .on('click', function(event) {
                if(!!$(this).next('ul').length) {
                    $(this).next('ul').slideToggle('fast', function() {
                        $(this).toggleClass('closed');
                    });

                    // Close the rest
                    $("div#mws-navigation ul li").not($(this).parent()).find("ul").slideUp('fast');
                    event.preventDefault();
                }
            });

        /* Responsive Layout Script */
        $("div#mws-navigation").on("click", function(event) {
            if(event.target === this) {
                $(this).toggleClass('toggled');
            }
        });

        /* Hide validation messages on double click */
        $(".mws-form-message").on("dblclick", function() {
            $(this).animate({ opacity:0 }, function() {
                $(this).slideUp("normal", function() {
                    $(this).css("opacity", '');
                });
            });
        });

        /* Hide alerts on double click */
        $(".alert").on("dblclick", function() {
            $(this).animate({ opacity:0 }, function() {
                $(this).slideUp("normal", function() {
                    $(this).css("opacity", '');
                });
            });
        });

        // Checkable Tables
        $( 'table thead th.checkbox-column :checkbox' ).on('change', function() {
            var checked = $( this ).prop( 'checked' );
            $( this ).parents('table').children('tbody').each(function(i, tbody) {
                $(tbody).find('.checkbox-column').each(function(j, cb) {
                    $( ':checkbox', $(cb) ).prop( "checked", checked ).trigger('change');
                });
            });
        });

        // Bootstrap Dropdown Workaround
        $(document).on('touchstart.dropdown.data-api', '.dropdown-menu', function (e) { e.stopPropagation() });

        /* Side Menu Notification Class */
        $(".mws-nav-tooltip").addClass("mws-inset");

        if($.fn.placeholder) {
            $('[placeholder]').placeholder();
        }

        /* Cookies */
        function setCookie(name, value, days)
        {
            var expires = "";
            if( days )
            {
                var date = new Date();
                date.setTime( date.getTime() + (days * 24 * 60 * 60 * 1000) );
                expires = "; expires=" + date.toGMTString();
            }
            document.cookie = name + "=" + value + expires + "; path=/";
        }

        function getCookie(c_name)
        {
            var name = c_name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++)
            {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(name) == 0)
                {
                    return c.substring(name.length, c.length);
                }
            }
            return null;
        }

        function deleteCookie(name)
        {
            setCookie(name, "", -1);
        }
    })

})(jQuery, window, document);