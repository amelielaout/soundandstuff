/**
 * --------------------------------------------------------------------
 * Fluxus Full Page Slider jQuery plugin.
 * --------------------------------------------------------------------
 */
( function ( window, $, undefined ) {

    var dummyStyle = document.createElement('div').style,
        vendor = (function () {
            var vendors = 't,webkitT,MozT,msT,OT'.split(','),
                t,
                i = 0,
                l = vendors.length;

                for ( ; i < l; i++ ) {
                    t = vendors[i] + 'ransform';
                    if ( t in dummyStyle ) {
                        return vendors[i].substr(0, vendors[i].length - 1);
                    }
                }

                return false;
        })(),
        cssVendor = vendor ? '-' + vendor.toLowerCase() + '-' : '';

    $.FluxusSlider = function ( options, element, callback ) {

        this.$element          = $( element );
        this.$slides           = this.$element.find( '.slide' );
        this.slideCount        = this.$slides.length;
        this.slidesLoaded      = 0;
        this.options           = $.extend( {}, $.FluxusSlider.settings, options );
        this.isActivating      = false;
        this.slideshowTimeout  = 0;
        this.slideshowRunning  = false;

        if ( 0 != this.$slides.length ) {
            this.$slides.data( 'loaded', false );
            this._init();
        }

    };

    $.FluxusSlider.settings = {
        onNextSlide: undefined,
        onPreviousSlide: undefined,
        slideshow: false,
        slideshowDuration: 7
    }

    $.FluxusSlider.prototype = {

        _init: function () {

            var self = this,
                $firstSlide = this.$slides.first();

            /**
             * Setup infoboxes.
             */
            this.$element.find( '.info' ).each( function () {
                var $infobox = $( this ),
                    t = 0,
                    iscroll = new iScroll($infobox.get(0), {
                                                            hideScrollbar: false,
                                                            scrollbarClass: 'iscrollbar'
                                                        });

                iscroll.disable();

                $infobox.data( 'iscroll', iscroll );

                $( window ).on( 'resize.infobox.fluxus', function () {
                    clearTimeout( t );
                    t = setTimeout( function () {
                        self.setInfoboxPosition.call( self, $infobox );
                    }, 100 );
                });
                self.setInfoboxPosition( $infobox );

            });

            /**
             * Load first image.
             */
            this.load( $firstSlide, function () {

                /**
                 * Setup navigation items.
                 */
                if ( this.slideCount > 1 ) {

                    /**
                     * Navigation arrows.
                     */
                    $( '.slider-arrow-left,.slider-arrow-right' ).show().click( function () {

                        if ( $( this ).is( '.slider-arrow-right' ) ) {
                            self.next();
                        } else {
                            self.previous();
                        }

                        return false;

                    });

                    this.enableNavigationGestures();

                    /**
                     * Navigation bullets.
                     */
                    var $nav = $( '<nav class="slider-navigation">' );
                    var $ul = $('<ul />');

                    this.$slides.each( function () {
                        var $link = $( '<li><a href="#"><b></b></a></li>' );
                        var title = $( this ).find( '.slide-title' ).html();

                        if ( ( title != undefined ) && title ) {
                            $link.find( 'a' ).append( $( '<span />').html( title ) );
                        }
                        $ul.append( $link );
                    });

                    $nav.append($ul);

                    $nav.hide().appendTo( this.$element )
                        .find( 'a' ).click( function () {

                            var $t = $(this);
                            if ( $t.is( '.active' ) ) {
                                return false;
                            }

                            $nav.find( '.active' ).removeClass( 'active' );
                            $t.addClass( 'active' );

                            var index = $t.parent().prevAll().length;
                            self.activate( self.$slides.eq( index ) );
                            return false;

                        }).first().addClass( 'active' );

                    this.$nav = $nav;

                    this.$nav.show();

                    /**
                     * Slideshow.
                     */

                    if (this.options.slideshow) {
                        this.startSlideshow();
                    }

                } else {

                    this.$nav = $( '<div />' ); // foo object.

                }

                /**
                 * Show first slide.
                 */
                var $active = $firstSlide.addClass( 'active' );

                this.$slides.css( 'opacity', 1 );

                $active.css( 'visibility', 'visible' ).transition({
                    opacity: 1
                }, 1500 );

                this.loadAll();

                /**
                 * Bind keyboard events.
                 */
                $( window ).on( 'keydown.slider.fluxus', function ( e ) {

                    if ( self.slideCount < 2 ) {
                        return true;
                    }

                    // right arrow down
                    if ( e.which == 39 ) {
                        self.next();
                        return false;
                    }

                    // left arrow down
                    if ( e.which == 37 ) {
                        self.previous();
                        return false;
                    }

                });

            });

        },

        startSlideshow: function () {

            var self = this,
                duration = parseInt(this.options.slideshowDuration);

            this.slideshowRunning = true;

            $('html').addClass('slideshow-active');

            if (! /\d+/.test(String(duration))) {
                duration = $.FluxusSlider.settings.slideshowDuration;
            }

            this.$nav.addClass('auto-slideshow')
                     .find('b').css(cssVendor + 'animation-duration', String(duration) + 's');

            if (! /\d+/.test(String(duration))) {
                duration = $.FluxusSlider.settings.slideshowDuration * 1000;
            } else {
                duration = duration * 1000;
            }

            this.slideshowTimeout = setTimeout(function () {

                self.next(undefined, true);

            }, duration);

        },

        pauseSlideshow: function () {

            this.$nav.removeClass('auto-slideshow');
            clearTimeout(this.slideshowTimeout);

        },

        stopSlideshow: function () {

            this.slideshowRunning = false;
            this.pauseSlideshow();
            $('html').removeClass('slideshow-active');

        },

        _isCallable: function ( variable ) {

            return variable && ( typeof variable === 'function' );

        },

        _getSelectedText: function () {

            var t = '';
            if ( window.getSelection && this._isCallable( window.getSelection ) ) {
                t = window.getSelection();
            } else if ( document.getSelection && this._isCallable( document.getSelection ) ) {
                t = document.getSelection();
            }else if ( document.selection ){
                t = document.selection.createRange().text;
            }
            return t;

        },

        enableNavigationGestures: function () {

            var isDragging = false;
            var downPosition = false;
            var self = this;

            this.$slides.on( 'mousedown.slider.fluxus', function ( e ) {

                downPosition = e.screenX;

                $( window ).on( 'mousemove.slider.fluxus', function () {

                    isDragging = true;
                    $( window ).off( 'mousemove.slider.fluxus' );

                } );

            } ).on( 'mouseup', function ( e ) {

                var wasDragging = isDragging;
                isDragging = false;

                $( window ).off( 'mousemove.slider.fluxus' );

                if ( wasDragging ) {

                    var selectedText = self._getSelectedText();

                    if ( new String( selectedText ).length == 0 ) {

                        var delta = downPosition - e.screenX;

                        if ( Math.abs( delta ) > 150 ) {

                            if ( delta > 0 ) {
                                self.next();
                            } else {
                                self.previous();
                            }

                        }

                    }

                }

            } );

            // Requires a touchwipe jQuery plugin
            if ( typeof $.fn.touchwipe != 'function' ) {
                return;
            }

            this.$element.touchwipe( {
                wipeLeft: function() {
                    self.next( 300 );
                },
                wipeRight: function() {
                    self.previous( 300 );
                },
                min_move_x: 20,
                min_move_y: 20,
                preventDefaultEvents: true
            });

        },

        setCustomPosition: function ( $infobox ) {

            var width       = this.$element.width(),
                height      = this.$element.height(),
                infoHeight  = $infobox.outerHeight(),
                infoWidth   = $infobox.outerWidth(),
                top         = $infobox.data( 'top' ),
                left        = $infobox.data( 'left' );

            // Prevents infobox going out of bounds.
            if ( /%$/.test( top ) && /%$/.test( left ) ) {

                var topPx = Math.round( parseInt(top) * height / 100 );
                var leftPx = Math.round( parseInt(left) * width / 100 );

                if ( leftPx + infoWidth > width ) {
                    left = width - infoWidth;
                }

                if ( topPx + infoHeight > height ) {
                    top = height - infoHeight;
                    top = top < 0 ? 0 : top;
                }

            }

            $infobox.css({
                top: top,
                left: left
            });

        },

        setCenterPosition: function ( $infobox ) {

            var width       = this.$element.width(),
                height      = this.$element.height(),
                infoHeight  = $infobox.outerHeight(),
                infoWidth   = $infobox.outerWidth(),
                top = Math.round( ( height - infoHeight ) / 2 ),
                left = Math.round( ( width - infoWidth ) / 2 );

            if ( left + infoWidth > width ) {
                left = width - infoWidth;
            }

            if ( top + infoHeight > height ) {
                top = height - infoHeight;
                top = top < 0 ? 0 : top;
            }

            $infobox.css({
                        top: top,
                        left: left
                    });

        },

        setInfoboxPosition: function ( $infobox ) {

            var iscroll     = $infobox.data('iscroll'),
                width       = this.$element.width(),
                height      = this.$element.height(),
                infoHeight  = $infobox.outerHeight();


            if ( $infobox.data( 'position' ) == 'custom' ) {

                /**
                 * We use custom position only when screen is wider than 480px,
                 * otherwise we try to center it in the middle.
                 */
                if ( width > 480 ) {

                    this.setCustomPosition( $infobox );

                } else {

                    this.setCenterPosition( $infobox );

                }

            } else {

                this.setCenterPosition( $infobox );

            }

            if (infoHeight > height) {

                $infobox.css({
                    height: '100%'
                });

                if (iscroll.enabled == false) {
                    iscroll.enable();
                    iscroll.refresh();
                }

            } else {

                $infobox.css({
                    height: 'auto'
                });

                if (iscroll.enabled == true) {
                    iscroll.disable();
                }

            }

        },

        activate: function ( $slide, direction, transitionSpeed, initiatedBySlideshow ) {

            if ( this.isActivating || $slide.is( '.active' ) ) {
                return false;
            }

            this.isActivating = true;

            if (this.options.slideshow) {
                this.pauseSlideshow();
            }

            var self = this,
                $active = this.$slides.filter( '.active' ),
                index = this.$slides.index( $slide ),
                activeIndex = this.$slides.index( $active ),
                $infoboxParts = $slide.find( '.animate-1, .animate-2' ).css( 'opacity', 0 );

            /**
             * Set CSS .active classes
             */
            $active.removeClass( 'active' );
            $slide.addClass( 'active' );

            this.$nav.find( '.active' ).removeClass( 'active' );
            this.$nav.find( 'a:eq(' + index + ')' ).addClass( 'active' );

            // Set the z-index so that new slide is under the old one.
            $active.css( 'z-index', 50 );
            $slide.css({
                    zIndex: 30,
                    visibility: 'visible',  // make it visible
                    x: 0                    // bring it back to original offset
                });

            if ( direction == undefined ) {
                direction = activeIndex > index ? 1 : -1;
            }

            var speed = transitionSpeed == undefined ? 1000 : transitionSpeed;

            $active.transition({
                x: $( window ).width() * direction
            }, speed, 'ease-in', function () {

                $active.css( 'visibility', 'hidden' );

                self.isActivating = false;

                if ($infoboxParts.length) {
                    $infoboxParts.eq( 0 ).css({
                        x: -100
                    }).delay( 200 ).transition({
                        x: 0,
                        opacity: 1
                    }, 500 );

                    $infoboxParts.eq( 1 ).css({
                        x: 100
                    }).delay( 500 ).transition({
                        x: 0,
                        opacity: 1
                    }, 500 );
                }

                if (self.options.slideshow) {
                    if (initiatedBySlideshow) {
                        self.startSlideshow();
                    } else {
                        if (self.slideshowRunning) {
                            self.stopSlideshow();
                        }
                    }
                }

            });

        },

        next: function ( transitionSpeed, initiatedBySlideshow ) {

            var index = this.$slides.filter( '.active' ).prevAll().length;
            index = this.slideCount - 1 == index ? 0 : index + 1;
            this.activate( this.$slides.eq( index ), -1, transitionSpeed, initiatedBySlideshow );

            if ( this.options.onNextSlide != undefined ) {
                this.options.onNextSlide.call( this );
            }

        },

        previous: function ( transitionSpeed, initiatedBySlideshow ) {

            var index = this.$slides.filter( '.active' ).prevAll().length;
            index = 0 == index ? this.slideCount - 1 : index - 1;
            this.activate( this.$slides.eq( index ), 1, transitionSpeed, initiatedBySlideshow );

            if ( this.options.onPreviousSlide != undefined ) {
                this.options.onPreviousSlide.call( this );
            }

        },

        load: function ( $slideToLoad, onFinish ) {

            if ( true === $slideToLoad.data( 'loaded' ) ) {
                onFinish.call( this );
                return;
            }

            var self = this;
            var img  = new Image();

            $( img ).on( 'load error', function () {

                $slideToLoad.data( 'loaded', true )
                            .css( 'background-image', 'url(' + img.src + ')' );

                self.slidesLoaded++;
                onFinish.call( self );

            });

            img.src = $slideToLoad.data( 'image' );

        },

        loadAll: function ( callback ) {

            var self = this;

            this.slidesLoaded = 0;

            this.$slides.each( function () {

                var $t = $( this );
                if ( false === $t.data( 'loaded' ) ) {

                    self.load( $t, function () {
                        self.slidesLoaded++;

                        if ( ( self.slidesLoaded == self.slideCount ) && ( typeof callback == 'function' ) ) {
                            callback.call( self );
                        }
                    } );

                } else {

                    self.slidesLoaded++;

                    if ( ( self.slidesLoaded == self.slideCount ) && ( typeof callback == 'function' ) ) {
                        callback.call( self );
                    }

                }

            });

        }

    }

    $.fn.fluxusSlider = function ( options, callback ) {

        this.data( 'slider', new $.FluxusSlider( options, this, callback ) );
        return this;

    }

}( window, jQuery ));

