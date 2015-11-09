/*!
 * jQuery styleBar: An advanced style bar jQuery plugin
 * Copyright 2011 Damion Yeatman for Audentio Design. ( http://audentio.com/ )
 *
 */
;(function( $, document, window, undefined ) {

$.fn.styleBar = function( options ) {

    var options = $.extend( {
        position      : 'left',
        theme         : 'default',
        disableMobile : true,
        overlay       : false
    }, options );

    // disable for mobile mobile devices
    if ( window['matchMedia'] ) {
        var mobile = window.matchMedia( "(max-width:500px)" );
        if ( mobile.matches && options.disableMobile ) return;
    }

    return this.each(function() {

        var thiz        = $( this ),
            // Grab settings
            sb_layout   = sb_settings[0],
            preset      = sb_settings[1],
            googleFonts = sb_settings[2],
            prefix      = sb_settings[3],
            // Set the html vaiable to the styleBar's head
            presetBar   = '<div id="sbPresets"><div class="dropdown"><div class="current">Choose a preset...<span></span></div><ul></ul></div></div>',
            buttons     = '<div id="sbUpdateButtons"><a href="#" class="update">Update</a><a href="#" class="reset">Reset</a></div>',
            headBefore  = '<div id="sbMainTitle"><a href="#" id="sbClose"><span></span></a><span id="sbLogo"></span>',
            headAfter   = '</div>';
            html        = ( options.overlay ) ? headBefore + presetBar + buttons + headAfter: headBefore + headAfter + buttons + presetBar;

        // If position = right add position_right class
        if ( options.position === 'right' ) {
            thiz.addClass( 'position_right' );
            $( 'html' ).addClass( 'sb_position_right' );
        }

        // If theme does not = default add the theme name as a class
        if ( options.theme !== 'default' ) {
            thiz.addClass( options.theme );
        }

        // If theme does not = default add the theme name as a class
        if ( options.overlay ) {
            thiz.addClass( 'overlay' );
        }

        // Load google fonts
        if ( googleFonts ) {
            var fonts   = googleFonts.replace( /,(\s?)+/g, ',' ).split( ',' ),
                fontUrl = '';
            $( fonts ).each(function( i ) {
                fontUrl += fonts[i].replace( /\s/g, '+' ) + '|';
            });
            $( 'head' ).append( '<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=' + fontUrl + '" />' );
        }

        // Add all elements
        $( sb_layout ).each(function( i ) {
            var htmlBefore = '<div class="overlay_segment">',
                htmlAfter  = '</div>';
            html += '<div class="section"><h3 class="sb-title">' + sb_layout[i][0] + '</h3>';

            for( var propertyName in sb_layout[i][1] ) {
                var prop = sb_layout[i][1][propertyName];

                if ( options.overlay ) html += htmlBefore;

                // If is color
                if ( prop === 'color' ) {
                    html += '<input type="hidden" id="' + propertyName + '" title="' + propertyName.replace( /_/g, ' ' ) + '" />';
                }
                // Else if is font
                else if ( prop[0] === 'font' ) {
                    html += '<div class="dropdown" id="' + propertyName + '"><div class="current"><span></span></div><ul>';

                    $( prop[1] ).each(function( i ) {
                        var font = ( prop[1][i].match( /\s/ ) ) ? "'" + prop[1][i] + "'" : prop[1][i];
                        html += '<li style="font-family:' + font.toLowerCase() + '">' + prop[1][i] + '</li>';
                    });

                    html += '</ul></div>';
                }
                // Else if is background
                else if ( prop[0] === 'background' ) {
                    html += '<a id="' + propertyName + '" title="' + propertyName.replace( /_/g, ' ' ) + '" class="pattern"><ul>';

                    $( prop[1] ).each(function( i ) {
                        html += '<li style="background:' + prop[1][i] + '"></li>';
                    });

                    html += '</ul></a>';
                }
                if ( options.overlay ) html += htmlAfter;
            }

            html += '</div>';
        });
        thiz.html(html);

        // Collect all changable objects
        var sections = $.makeArray( $( 'input[type=hidden][id], .pattern[id], .dropdown[id]', thiz ) );

        // Collect all sections ID's
        for ( var i = 0; i < sections.length; i++ ) { sections[i] = sections[i].id }

        var styleBar = {

            // Initial load
            loadStyleBar: function() {
                if ( ! options.overlay ) {
                    thiz.show();
                }
                else {
                    if ( Storage.get( prefix + 'sbClosed' ) != 'false' ) {
                        thiz.addClass( 'closed' );
                    }
                    else {
                        thiz.css( 'opacity' , '0' ).show(function() {
                            var styleBarHeight = thiz.outerHeight(),
                                newMargin = '-' + ( styleBarHeight / 2 ) + 'px';
                            thiz.css({ 'margin-top' : newMargin, opacity : 1 });
                        });
                    }
                }

                if ( ! Storage.get( prefix + sections[0] ) && preset['default'] ) {
                    this.loadPreset( 'default' );
                }
                else {
                    this._update.load();
                }

                $( 'input[type=hidden][id]', thiz ).miniColors({});

                // Load preset selector
                for ( var i = 0, presetLength = preset.length; i < presetLength; i++ ) {
                    $( '#sbPresets ul' ).append( '<li data-id="' + i + '">' + preset[i].name + '</li>' );
                }

                // If styleBar is closed: Hide styleBar
                if ( Storage.get( prefix + 'sbClosed' ) != 'false' && ! options.overlay ) {
                    var styleBarWidth = thiz.outerWidth(),
                        closeButtonWidth = $( '#sbClose' ).outerWidth();

                    if ( options.position === 'right' ) {
                        thiz.css( { right: - styleBarWidth + 'px' } ).addClass( 'closed' );
                        $( '#sbClose' ).animate( { left: -closeButtonWidth + 'px' } );
                    }
                    else {
                        thiz.css( { left: - styleBarWidth + 'px' } ).addClass( 'closed' );
                        $( '#sbClose' ).animate( { right: -closeButtonWidth + 'px' } );
                    }
                }
            },

            // Load a preset
            loadPreset: function( id ) {
                if ( ! preset[id] ) return;
                for ( var i = 0, sectionsLength = sections.length; i < sectionsLength; i++ ) {
                    var elm = $( '#' + sections[i], thiz ),
                        preElm = ( preset[id][sections[i]] ) ? preset[id][sections[i]] : false;

                    if ( elm.is( ':input' ) && preElm ) {
                        elm.miniColors( 'value', preElm );
                    }
                    else if ( elm.attr( 'class' ) === 'pattern' && preElm ) {
                        elm.css( 'background', preElm );
                    }
                    else if ( elm.attr( 'class' ) === 'dropdown' && preElm ) {
                        elm.find( '.current' ).html( preElm.replace( /'|"/g, '' ) + '<span></span>' ).css( 'font-family', preElm );
                        elm.find( 'li' ).removeClass( 'active' );
                        elm.find( 'li[style*=' + preElm + ']' ).addClass( 'active' );
                    }
                }
            },

            // Build href and update changes to website
            runUpdate: function( baseUrl ) {
                baseUrl = ( baseUrl ) ? baseUrl : $( '#sbcss' ).attr( 'href' ).replace( /\/css.php(.?)+/, '/css.php' );
                Storage.set( prefix + 'href', baseUrl + '?prefix=' + prefix );

                this._update.storage();
                this._update.href();
                $( '#sbcss' ).attr( 'href', Storage.get( prefix + 'href' ) );
            },

            // Reset all style
            resetStyle: function() {
                for ( var i = 0; i < sections.length; i++ ) {
                    Storage.remove( prefix + sections[i] );
                }
                Storage.remove( prefix + 'href' );
                $( '#sbcss' ).attr( 'href', $( '#sbcss' ).attr( 'href' ).replace( /\/css.php(.?)+/, '/css.php' ) );
                if ( preset['default'] ) styleBar.loadPreset( 'default' );
            },

            // Private update functions
            _update: {

                // Filter through sections
                storage: function() {
                    for ( var i = 0, sectionsLength = sections.length; i < sectionsLength; i++ ) {
                        var elm = $( '#' + sections[i] ),
                            val;

                        if ( elm.is( ':input' ) ) {
                            val = elm.attr( 'value' );
                        }
                        else if ( elm.attr( 'class' ) === 'pattern' ) {
                            val = elm.css( 'background-image' ) + ' ' + elm.css( 'background-position' ) + ' ' + elm.css( 'background-repeat' );
                        }
                        else if ( elm.attr( 'class' ) === 'dropdown' ) {
                            val = elm.find( '.current' ).css( 'font-family' );
                        }
                        Storage.set( prefix + sections[i], val );
                    }
                },

                // Load section propertys
                load: function() {
                    for ( var i = 0, sectionsLength = sections.length; i < sectionsLength; i++ ) {
                        var elm = $( '#' + sections[i], thiz ),
                            prefixed = prefix + sections[i];

                        if ( elm.is( ':input' ) ) {
                            val = ( Storage.get( prefixed ) ) ? Storage.get( prefixed ) : elm.val();
                            elm.val( val );
                        }
                        else if ( elm.attr( 'class' ) === 'pattern' ) {
                            val = ( Storage.get( prefixed ) ) ? Storage.get( prefixed ) : elm.css( 'background' );
                            elm.css( 'background', val );
                        }
                        else if ( elm.attr( 'class' ) === 'dropdown' ) {
                            val = ( Storage.get( prefixed ) ) ? Storage.get( prefixed ) : elm.find( '.current' ).css( 'font-family' );
                            elm.find( '.current' ).html( val.replace( /'|"/g, '' ) + '<span></span>' ).css( 'font-family', val );
                            elm.find( 'li[style*=' + val + ']', thiz ).addClass( 'active' );
                        }
                    }
                },

                // Update href link
                href: function() {
                    for ( var i = 0, sectionsLength = sections.length; i < sectionsLength; i++ ) {
                        Storage.set( prefix + 'href', Storage.get( prefix + 'href' ) + '&' + prefix + sections[i] + '=' + escape( Storage.get( prefix + sections[i] ) ) );
                    }
                }
            }
        };

        // Run the load
        styleBar.loadStyleBar();

        // Update/Reset Buttons
        $( '.update', thiz ).click(function() {
            styleBar.runUpdate();
            return false;
        });
        $( '.reset', thiz ).click(function() {
            styleBar.resetStyle();
            return false;
        });

        // Tooltips
        $( 'a.miniColors-trigger', thiz ).attr( 'tooltip', function() {
            return $( this ).prev().attr( 'title' );
        });
        $( '.pattern', thiz ).each(function() {
            $( this ).prepend( '<span></span>' )
                .attr( { 'tooltip': $( this ).attr( 'title' ), 'title': '' } );
        });
        $( '.pattern li', thiz ).each(function() {
            $( this ).html( '<span></span>' );
        });

        // Set dropdowns always in center
        $( '.dropdown div.current', thiz ).click(function() {
            $( '.pattern ul', thiz ).hide();
            $( '.dropdown ul', thiz ).css( 'left', '-9999px' );
            var middleElm = $( this ).parent().find( 'ul li' ).length / 2 - 1,
                top = $( this ).parent().find( 'ul li:eq(' + Math.round( middleElm ) + ')' ).position();
            $( this ).parent().find( 'ul' ).css( 'left', '0' ).css( 'top', '-' + top.top + 'px ' );
            return false;
        });
        // Change font when new font is picked
        $( '.dropdown li[style*=font]', thiz ).click(function() {
            $( '.dropdown li[style*=font]', thiz ).removeClass( 'active' );
            $( this ).addClass( 'active' );
            var font = $( this ).css( 'font-family' ),
                fontText = $( this ).text();
            $( this ).closest( '.dropdown' ).find( 'div.current' ).css( 'font-family', font ).html( fontText + '<span></span>' );
            $( this ).parent().css( 'left', '-9999px' );
        });
        // Run preset changer
        $( '#sbPresets .dropdown ul li' ).click(function() {
            $( '#sbPresets .dropdown ul li' ).removeClass( 'active' );
            var presetId = $( this ).attr( 'data-id' ),
                html = $( this ).html();
            $( this ).addClass( 'active' )
                .closest( '.dropdown' )
                .find( 'div.current' ).html( html + '<span></span>' );
            styleBar.loadPreset( presetId );
        });

        // Show pattern selector
        $( 'a.pattern', thiz ).click(function() {
            $( '.pattern ul', thiz ).hide();
            $( '.dropdown ul', thiz ).css( 'left', '-9999px' );
            $( this ).find( 'ul' ).show();
            return false;
        });

        // Change pattern background when pattern changed
        // THIS IS THE FUCKING PROBLEM :S
        $( 'a.pattern > ul > li span', thiz ).click(function() {
            var bgImg = $( this ).parent().css( 'background-image' ),
                bgPos = $( this ).parent().css( 'background-position' ),
                bgRep = $( this ).parent().css( 'background-repeat' );
            $( this ).closest( 'a.pattern' ).css( 'background', bgImg + ' ' + bgPos + ' ' + bgRep );
        });

        // Hide patterns & dropdown when document clicked
        $( document ).click(function() {
            $( '.pattern ul', thiz ).hide();
            $( '.dropdown ul', thiz ).css( 'left', '-9999px' );
        });

        // Open/Close StyleBar
        $( '#sbClose, #sbOpen' ).click(function() {
            if ( ! options.overlay ) {
                var closeButton = $( this ),
                    styleBarWidth = thiz.outerWidth(),
                    closeButtonWidth = closeButton.outerWidth();

                if ( thiz.hasClass( 'closed' ) ) { // Closed
                    if ( options.position === 'right' ) {
                        closeButton.animate( { left: ( styleBarWidth - 45 ) + 'px' }, function() {
                            thiz.removeClass( 'closed' );
                            thiz.animate( { right: 0 } );
                        });
                    }
                    else {
                        closeButton.animate( { right: 0 }, function() {
                            thiz.removeClass( 'closed' );
                            thiz.animate( { left: 0 } );
                        });
                    }
                    Storage.set( prefix + 'sbClosed', 'false' );
                }
                else { // Open
                    if ( options.position === 'right' ) {
                        thiz.animate( { right: - styleBarWidth + 'px' }, function() {
                            thiz.addClass( 'closed' );
                            closeButton.animate( { left: - closeButtonWidth + 'px' } );
                        });
                    }
                    else {
                        thiz.animate( { left: - styleBarWidth + 'px' }, function() {
                            thiz.addClass( 'closed' );
                            closeButton.animate( { right: - closeButtonWidth + 'px' } );
                        });
                    }
                    Storage.set( prefix + 'sbClosed', 'true' );
                }
            }
            else {
                if ( thiz.hasClass( 'closed' ) ) {
                    thiz.fadeIn( 'slow' ).removeClass( 'closed' );

                    var styleBarHeight = thiz.outerHeight(),
                        newMargin = '-' + ( styleBarHeight / 2 ) + 'px';
                    thiz.css({ 'margin-top' : newMargin });

                    Storage.set( prefix + 'sbClosed', 'false' );
                }
                else{
                    thiz.fadeOut( 'slow', function() {
                        thiz.addClass( 'closed' );
                    });
                    Storage.set( prefix + 'sbClosed', 'true' );
                }
            }
            return false;
        });

    });
}

})( jQuery, document, window );