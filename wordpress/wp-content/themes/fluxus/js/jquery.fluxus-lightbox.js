
(function ( $, $window, isUndefined ) {

    var namespace_index = 0;

    $.FluxusLightbox = function( $links, options ) {

        namespace_index++;
        this.namespace = 'fluxus-lightbox-' + namespace_index;

        var self = this;

        this.options = $.extend( {}, $.FluxusLightbox.defaults, options );
        this.allImagesLoaded = false;

        // Support for <a/> and <iframe/> tags.
        this.$media = $links.filter( 'a,iframe' );
        this.$html  = $('html');

        if ( 0 == this.$media.length ) {
            return false;
        }

        this.$media.filter('a').click(function () {

            var $t = $(this);

            self.showImage.call( self, $t, 0 );
            self.open.call( self, self.options.onShow );

            return false;

        });

        this.visible = false;

        this._init();

    }

    $.FluxusLightbox.defaults = {
        close: 'Close',
        resize: '',
        previous: '',
        next: '',
        loading: 'Please wait...',
        error: 'Unable to load.',
        loadAll: true,
        mode: 'fit'     // fit or full
    }

    $.FluxusLightbox.prototype = {


        open: function (callback) {

            // Don't move a finger if it's already visible.
            if (this.visible) {
                return;
            }

            var self = this;

            this.originalScrollPosition = {
                x: $window.scrollLeft(),
                y: $window.scrollTop()
            };

            this.$html.addClass('fluxus-lightbox-visible');

            // Animate background
            this.$lightbox.css({
                top: '-100%',
                display: 'block'
            }).animate({
                top: 0
            }, 500, callback);

            this.visible = true;

            // bind events
            $window.on( 'keydown.fluxus-lightbox', function ( e ) {

                if ( e.which == 39 ) {
                    self.next.call( self );
                    return false;
                }

                if ( e.which == 37 ) {
                    self.previous.call( self );
                    return false;
                }

                if ( e.which == 27 ) {
                    self.close.call( self );
                    return false;
                }

            });

            // Requires a touchwipe jQuery plugin
            if ( typeof $.fn.touchwipe == 'function' ) {

                this.$lightbox.touchwipe( {
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

            }

        },


        close: function () {

            if (!this.visible) {
                return false;
            }

            this.$lightbox.fadeOut(500, this.options.onHide);

            this.$contents.html('');

            this.$html.removeClass('fluxus-lightbox-visible');

            $(window).off('keydown.fluxus-lightbox')
                     .off('resize.fluxus-lightbox');

            window.scrollTo(this.originalScrollPosition.x, this.originalScrollPosition.y);

            this.visible = false;

        },


        _placeElementInCenter: function ($element) {

            $element.css('visibility', 'hidden').show();

            // Set a tiny delay, so that browser can calculate element size
            setTimeout(function () {

                var windowHeight = $(window).height(),
                    windowWidth = $(window).width(),
                    width = $element.width(),
                    height = $element.height();

                $element.css({
                    top: (windowHeight - height) / 2,
                    left: (windowWidth - width) / 2
                });

            }, 10);

            return $element.hide().css('visibility', 'visible');

        },


        showIframe: function ($iframe, fadeInTime) {

            // Show loading
            this.$error.hide();
            this._placeElementInCenter(this.$loading).show();

            // don't show resize button
            this.$resize.hide();

            var self             = this,
                $iframeContainer = $('<div class="iframe-container" />'),
                $iframeClone     = $iframe.clone(),
                resizeDebounce   = 0;

            $iframeContainer.append($iframeClone).hide();

            $iframeClone.error(function () {

                // iframe loading error
                self.$loading.hide();
                self._placeElementInCenter(self.$error).show();

            }).load(function () {

                // put iframe in position
                self.resizeIframe($iframeClone);

                // on resize, put iframe in position
                $(window).off('resize.fluxus-lightbox')
                         .on('resize.fluxus-lightbox', function () {

                            clearTimeout(resizeDebounce);
                            resizeDebounce = setTimeout(function () {
                                self.resizeIframe($iframeClone);
                            }, 100);

                    });

                self.$loading.hide();

                $iframeContainer.fadeIn(300);

            });

            this.$media.removeClass('lightbox-active');
            $iframe.addClass('lightbox-active');

            // Append contents with iFrame
            this.$contents.html('').append($iframeContainer);

        },


        resizeIframe: function ($iframe) {

            if (0 == $iframe.length) {
                return false;
            }

            var aspectRatio = false;

            if ($iframe.attr('width') && $iframe.attr('height')) {
                aspectRatio = $iframe.attr('width') / $iframe.attr('height');
            }

            if (false == /^\d+(\.\d+)?$/.test(String(aspectRatio))) {
                aspectRatio = false;
            }

            if (aspectRatio) {

                var windowWidth = $(window).width(),
                    windowHeight = $(window).height(),
                    windowRatio = windowWidth / windowHeight;

                if ( windowRatio > aspectRatio ) {

                    var height = $iframe.parent().innerHeight();

                    if (height > 1080) {
                        height = 1080;
                    }

                    height -= 100;
                    width = height * aspectRatio;

                    $iframe.css({
                        height: height,
                        width: width
                    });

                } else {

                    var width = $iframe.parent().innerWidth(),
                        height = width / aspectRatio;

                    if (width > 1920) {
                        width = 1920;
                        height = 1920 / aspectRatio;
                    }

                    $iframe.css({
                        width: width,
                        height: height
                    });

                }

                if (windowHeight > height) {
                    $iframe.css('top', (windowHeight - height) / 2);
                } else {
                    $iframe.css('top', 0);
                }

            } else {

                var height = $iframe.height(),
                    windowHeight = $(window).height();

                if (height < windowHeight) {
                    $iframe.css('top', (windowHeight - height) / 2);
                } else {
                    $iframe.css('top', 0);
                }

            }

        },


        /**
         * Show image in the Lightbox.
         */
        showImage: function ($image, fadeInTime) {

            this.$error.hide();

            this.$contents.children('img').fadeOut(400);

            // If image is not yet loaded
            if ($image.data('loaded') != true) {
                this._placeElementInCenter(this.$loading).show();
            }

            var self = this,
                img = new Image(),
                $newImage = $('<img />'),
                resizeDebounce = 0;

            this.$resize.show();

            $(img).error(function () {

                self.$loading.hide();
                self._placeElementInCenter(self.$error).show();

            }).load(function () {

                $image.data('loaded', true);

                $newImage.attr('src', img.src).hide();

                $window.off('resize.fluxus-lightbox')
                           .on('resize.fluxus-lightbox', function () {

                                clearTimeout(resizeDebounce);
                                resizeDebounce = setTimeout(function () {
                                    self.resizeImage($newImage);
                                }, 100);

                            });

                self.resizeImage($newImage, function () {

                    self.$contents.html($newImage);

                    fadeInTime = fadeInTime != isUndefined ? fadeInTime : 400;
                    self.$loading.fadeOut(100);
                    $newImage.fadeIn(fadeInTime);

                });

            } );

            this.$media.removeClass('lightbox-active');
            $image.addClass('lightbox-active');

            img.src = $image.attr('href');

        },


        resizeImage: function ($image, callback) {

            if (0 == $image.length) {
                return false;
            }

            var self         = this,
                windowHeight = $window.height(),
                windowWidth  = $window.width(),
                windowRatio  = windowWidth / windowHeight,
                img          = new Image();

            // Make sure the image is loaded
            $(img).load(function () {

                var imageRatio        = img.width / img.height,
                    scaledImageHeight = img.height;

                if ('fit' === self.mode) {

                    // Fit height
                    if (windowRatio > imageRatio) {

                        $image.css({
                            height: '100%',
                            width: 'auto',
                            maxHeight: img.height,
                            maxWidth: img.width
                        });

                    // Fit width
                    } else {

                        $image.css({
                            width: '100%',
                            height: 'auto',
                            maxHeight: img.height,
                            maxWidth: img.width
                        });

                        if (img.height > windowHeight) {
                            scaledImageHeight = windowWidth / imageRatio;
                        }

                    }

                    if ( windowHeight > scaledImageHeight ) {
                        $image.css('top', (windowHeight - scaledImageHeight) / 2);
                    } else {
                        $image.css('top', 0);
                    }

                } else {

                    $image.css({
                            width: '100%',
                            height: 'auto'
                        });

                }

                if (callback != isUndefined) {
                    callback.call(self);
                }

            });

            img.src = $image.attr('src');

        },


        next: function () {

            var $active     = this.$media.filter('.lightbox-active'),
                activeIndex = this.$media.index($active),
                count       = this.$media.length,
                newIndex    = 0;

            if ((activeIndex != -1) && (activeIndex + 1 != count)) {
                newIndex = activeIndex + 1;
            }

            var $nextItem = this.$media.eq(newIndex);

            if ($nextItem.is('a')) {
                this.showImage($nextItem, 400);
            } else {
                this.showIframe($nextItem, 400);
            }

        },


        previous: function () {

            var $active = this.$media.filter('.lightbox-active'),
                activeIndex = this.$media.index($active),
                count = this.$media.length,
                newIndex = count - 1;

            if ((activeIndex != -1) && (activeIndex != 0)) {
                newIndex = activeIndex - 1;
            }

            var $previousItem = this.$media.eq(newIndex);

            if ($previousItem.is('a')) {
                this.showImage($previousItem, 400);
            } else {
                this.showIframe($previousItem, 400);
            }

        },


        /**
         * Initialize Lightbox.
         */
        _init: function () {

            var self = this,
                template = '' +
                    '<div class="fluxus-lightbox ' + this.namespace + '">' +
                        '<div class="lightbox-content">' +
                        '</div>' +
                        '<span class="lightbox-close icon-cancel">%close</span>' +
                        '<span class="lightbox-resize icon-resize-small">%resize</span>' +
                        '<span class="lightbox-prev icon-left-open-big">%previous</span>' +
                        '<span class="lightbox-next icon-right-open-big">%next</span>' +
                        '<div class="lightbox-loading">%loading</div>' +
                        '<div class="lightbox-error">%error</div>' +
                    '</div>';

            template = template.replace( '%close', this.options.close );
            template = template.replace( '%resize', this.options.resize );
            template = template.replace( '%previous', this.options.previous );
            template = template.replace( '%next', this.options.next );
            template = template.replace( '%loading', this.options.loading );
            template = template.replace( '%error', this.options.error );

            this.$lightbox = $(template);
            this.$contents = this.$lightbox.find('.lightbox-content');

            this.$loading = this.$lightbox.find('.lightbox-loading');
            this.$error   = this.$lightbox.find('.lightbox-error');

            /**
             * Assign DOM events
             */
            this.$next = this.$lightbox.on('click', '.lightbox-next', function () {
                self.next.call( self );
                return false;
            }).find('.lightbox-next');

            this.$prev = this.$lightbox.on('click', '.lightbox-prev', function () {
                self.previous.call( self );
                return false;
            }).find('.lightbox-prev');;

            this.$close = this.$lightbox.on('click', '.lightbox-close', function () {
                self.close.call( self );
                return false;
            }).find('.lightbox-close');

            this.$resize = this.$lightbox.on('click', '.lightbox-resize', function () {
                if ( self.mode == 'fit' ) {
                    self.fullScreen.call( self );
                } else {
                    self.fitToScreen.call( self );
                }
                return false;
            }).find('.lightbox-resize');

            /**
             * Do not show resize on iPhone/iPad.
             */
            if ( this.isIOS() ) {
                this.$resize.hide();
                this.$resize = $('<div />');
                this.options.mode = 'fit';
            }

            if ( this.options.mode == 'fit' ) {
                this.options.mode = '';
                this.fitToScreen();
            } else {
                this.options.mode = '';
                this.fullScreen();
            }

            $('body').append(this.$lightbox);

        },


        fitToScreen: function () {

            if ( this.$resize.is( '.icon-resize-small' ) ) {
                this.$resize.removeClass( 'icon-resize-small' ).addClass( 'icon-resize-full' );
            }

            if ( this.mode == 'fit' ) {
                return;
            }

            this.$lightbox.removeClass( 'mode-full' ).addClass( 'mode-fit' );

            this.mode = 'fit';

            this.resizeImage( this.$lightbox.find( 'img' ) );

        },


        fullScreen: function () {

            if ( this.$resize.is( '.icon-resize-full' ) ) {
                this.$resize.removeClass( 'icon-resize-full' ).addClass( 'icon-resize-small' );
            }

            if ( this.mode == 'full' ) {
                return;
            }

            this.$lightbox.removeClass( 'mode-fit' ).addClass( 'mode-full' );

            this.mode = 'full';

            this.resizeImage( this.$lightbox.find( 'img' ) );

        },


        /**
         * Checks for iOS.
         */
        isIOS: function () {

          return /(iPad|iPhone)/i.test( navigator.userAgent );

        }

    }

    $.fn.fluxusLightbox = function( options ) {

        new $.FluxusLightbox( this, options );

        return this;

    }

})( jQuery, jQuery(window) );
