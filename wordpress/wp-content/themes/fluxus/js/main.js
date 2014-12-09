;(function ($, window, $window, isUndefined) {

    $(function () {

        var $html       = $('html'),
            $main       = $('#main'),
            $header     = $('#header'),
            $footer     = $('#footer'),

            // Pages
            $pageHorizontalPosts = $('.horizontal-posts'),
            $pagePortfolio       = $('.portfolio-list'),
            $pageGridPortfolio   = $('.portfolio-grid'),
            $pagePortfolioSingle = $('.single-fluxus_portfolio'),
            $pageContacts        = $('.page-contacts'),

            // Components
            $keyRight            = $('#key-right'),
            $keyLeft             = $('#key-left'),
            $sharrreFooter       = $('#sharrre-footer'),
            $sharrreProject      = $('#sharrre-project');

        if (iPadWithIOS4()) {
            $html.addClass('ipad-ios4');
        }

        /**
         * Global navigation plugin.
         * Enables keyboard navigation.
         */
        window.globalNav = new Navigation({

            onSetItems: function () {
                this.$items.length && $('.nav-tip').show();
            }

        });


        /**
         * Full page slider
         */
        $slider = $('.slider');

        if ( $slider.length ) {

            $slider.fluxusSlider({
                onNextSlide: function () {
                    globalNav.options.onItemNext();
                },
                onPreviousSlide: function () {
                    globalNav.options.onItemPrevious();
                },
                slideshow: $slider.data('slideshow'),
                slideshowDuration: $slider.data('duration')
            });

            if ( $slider.data('slider').slideCount > 1 ) {
                $('.nav-tip').show();
            }

            globalNav.disableKeyboard();

            $keyRight.click( function () {
                $slider.data('slider').next();
                return false;
            });

            $keyLeft.click(function () {
                $slider.data('slider').previous();
                return false;
            });

        }


        /**
         * Appreciate plugin
         */
        var $appreciate = $('.btn-appreciate');
        $appreciate.appreciate();


        /**
         * Sharrre plugin
         */
        if ($sharrreFooter.length) {

            // retrieve social networks from DOM element.
            if ($sharrreFooter.data('services')) {

                var services = {},
                    buttonsTitle = $sharrreFooter.data('buttons-title');

                $.each($sharrreFooter.data('services').split(','), function () {
                    services[this] = true;
                });

                $sharrreFooter.sharrre({
                    share: services,
                    buttonsTemplate: buttonsTitle ? '<b>' + buttonsTitle + '<a href="#" class="close"></a></b>' : '',
                    urlCurl: $sharrreFooter.data('curl'),
                    template: '<b class="share">{title}</b>' +
                              '<span class="counts">' +
                                ( services.facebook ? '<b class="count-facebook">{facebook}</b>' : '') +
                                ( services.twitter ?'<b class="count-twitter">{twitter}</b>' : '') +
                                ( services.googlePlus ?'<b class="count-plus">{plus}</b>' : '') +
                              '</span>',
                    render: function( self, options ) {
                        var html = this.template.replace('{title}', options.title );
                        html = html.replace('{facebook}', options.count.facebook );
                        html = html.replace('{twitter}', options.count.twitter );
                        html = html.replace('{plus}', options.count.googlePlus );
                        $(self.element ).html(html);
                        $sharrreFooter.show();
                    }
                });

            }

        }

        if ($sharrreProject.length) {

            if ($sharrreProject.data('services')) {

                var services = {},
                    buttonsTitle = $sharrreProject.data('buttons-title'),
                    fadeOutdelay = 0;

                // retrieve social networks from DOM element.
                $.each($sharrreProject.data('services').split(','), function () {
                    services[this] = true;
                });

                $sharrreProject.sharrre({
                    share: services,
                    buttonsTemplate: buttonsTitle ? '<div class="arrow"></div><b>' + buttonsTitle + '<a href="#" class="close"></a></b>' : '',
                    urlCurl: $sharrreProject.data('curl'),
                    template: '<span class="icon"></span><div class="box">' +
                                '<a class="share" href="#">{title}</a>' +
                                '<b class="count-total">{total}</b>' +
                              '</div>',
                    render: function( self, options ) {
                        var total = options.shorterTotal ? self.shorterTotal(options.total) : options.total,
                            html = this.template.replace('{title}', options.title).replace('{total}', total);
                        $(self.element).html(html);
                        $sharrreProject.css('display', 'inline-block');
                    },
                    afterLoadButtons: function () {
                        var index = 0,
                            $buttons = this.$el.find('.button'),
                            count = $buttons.each( function () {
                                        index++;
                                        $(this).addClass('button-' + index);
                                    }).length;
                        this.$el.addClass('social-services-' + count);
                    }
                });

            }

        }


        /**
         * Fixes menu issue, when popup is outside the screen.
         */
        $('.site-navigation .has-children').hover( function () {

            var $t = $(this );
            var $submenu = $t.children('.sub-menu ');

            if ( $submenu.length ) {

                // if popup is outside the screen, then align it by the right side of the screen.
                if ( $submenu.offset().left + $submenu.outerWidth() - $(document ).scrollLeft() > $window.width() ) {
                    $submenu.addClass('sub-menu-right');
                }

            }

        }, function () {

            $(this ).children('.sub-menu').removeClass('sub-menu-right');

        });


        /**
         * If our page has horizontal layout.
         */
        if ( $html.is('.horizontal-page') ) {

            /**
             * Enable tinyscrollbar plugin.
             */
            $(".scroll-container").tinyscrollbar({
                axis: 'y'
            });

            /**
             * Enable keyboard navigation.
             */
            globalNav.options.onItemNext = function () {
                $keyRight.addClass('flash');
                setTimeout( function () {
                    $keyRight.removeClass('flash');
                }, 200);
            }

            globalNav.options.onItemPrevious = function () {
                $keyLeft.addClass('flash');
                setTimeout( function () {
                    $keyLeft.removeClass('flash');
                }, 200);
            }

            $keyRight.click( function () {
                globalNav.nextItem();
                return false;
            });

            $keyLeft.click( function () {
                globalNav.previousItem();
                return false;
            });

        }


        /**
         * --------------------------------------------------------------------------------
         * Specific pages
         * --------------------------------------------------------------------------------
         */


        /**
         * Page: Grid portfolio
         */
        if ( $pageGridPortfolio.length ) {

            /**
             * Enable Grid plugin.
             */
            $pageGridPortfolio.grid({

                minWindowWidth: 768,
                rows: $pageGridPortfolio.data('rows'),
                columns: $pageGridPortfolio.data('columns'),

            }, function () {

                $pageGridPortfolio.find('.inner').verticalCenter();

                $pageGridPortfolio.width() > $window.width() ? $('.nav-tip').show() : $('.nav-tip').hide();

            });

            /**
             * Sets first line of a grid (the longest one) as a source
             * for navigation plugin.
             */
            globalNav.setItems( $pageGridPortfolio.data('grid').getRowItems(0) );

            $pageGridPortfolio.width() > $window.width() ? $('.nav-tip').show() : $('.nav-tip').hide();

        }


        /**
         * Page: Portfolio
         */
        if ($pagePortfolio.length) {

            $projects = $('.project');

            /**
             * Set keyboard navigation items.
             */
            globalNav.setItems($projects);

            // Show project on image load, which prevents flickering.
            $projects.each(function () {

                var $t   = $(this),
                    $img = $t.find('.featured-image'),
                    img  = new Image();

                $(img).on('load error', function () {
                    $t.find('.hover-box-contents').vcenter();
                    $t.addClass('loaded');
                });
                img.src = $img.attr('src');

            });

        }


        /**
         * Page: Portfolio Single
         */
        if ($pagePortfolioSingle.length) {

            var $horizontalItems = $('.horizontal-item'),
                $lightboxItems   = $('');

            globalNav.setItems($horizontalItems.add($('.portfolio-navigation')));

            // Fade in images and videos once loaded.
            $horizontalItems.each(function () {

                var $t = $(this);

                if ($t.is('.wrap-image')) {

                    var $imageLink = $t.find('.project-image-link'),
                        img = new Image();

                    $lightboxItems = $lightboxItems.add($imageLink);

                    $(img).on('load', function () {

                        $t.transition({
                            opacity: 1
                        }, 500);

                    }).on('error', function () {
                        $t.remove();
                    });

                    img.src = $imageLink.attr('href');

                } else if ($t.is('.wrap-video')) {

                    var $iframe = $t.find('iframe');

                    $lightboxItems = $lightboxItems.add($iframe);

                    $t.transition({
                        opacity: 1
                    }, 500);

                }

            });

            // Bind Lightbox to image links and iframes
            $lightboxItems.fluxusLightbox({
                onShow: function () {
                    globalNav.disableKeyboard();
                },
                onHide: function () {
                    globalNav.enableKeyboard();
                },
                loading: $pagePortfolioSingle.data('loading')
            });

            var adjustPageHeight = function () {

                var contentHeight = getContentHeight(),
                    windowWidth = $window.width();

                $horizontalItems.each(function () {

                    var $t = $(this),
                        maxHeight = $t.data('height'),
                        maxWidth = $t.data('width'),
                        height = contentHeight > maxHeight ? maxHeight : contentHeight,
                        ratio = $t.data('ratio'),
                        $resizable = $t.is('.wrap-image') ? $t.find('img') : $t.find('iframe');

                    if (!ratio) {
                        ratio = maxWidth / maxHeight;
                        $t.data('ratio', ratio);
                    }

                    if (windowWidth <= 768) {
                        var width = $t.width() > maxWidth ? maxWidth : $t.width();

                        $resizable.css({
                            width: width,
                            height: Math.round(width / ratio)
                        });
                    } else {
                        $resizable.css({
                            height: contentHeight,
                            width: contentHeight * ratio
                        });
                    }

                });

                if ( windowWidth <= 768 ) {
                    $('#content').addTempCss('padding-top', $('.sidebar').outerHeight() + 30);
                } else {
                    $('#content').removeTempCss('padding-top');
                }

                if ($html.is('.ipad-ios4')) {

                    var totalWidth = 0;

                    $pagePortfolioSingle.children().each(function () {
                        totalWidth += $(this).width();
                    });

                    $pagePortfolioSingle.css({
                        width: totalWidth
                    });

                }

            };

            $window.on('resize.fluxus.single-portfolio', debounce( adjustPageHeight ) );
            adjustPageHeight();

        }

        /**
         * Page: Blog / Archive / Search
         */
        if ( $pageHorizontalPosts.length ) {

            globalNav.setItems( $pageHorizontalPosts.find('.post, .navigation-paging') );

        }


        /**
         * Page: Contacts
         */
        if ( $pageContacts.length ) {

            var $contactsForm = $('.wpcf7');

            if ($contactsForm.length) {
                $contactsForm.detach();
                $('#contacts-modal .modal-contents').append( $contactsForm );
            }

            $('#send-message').click( function () {
                $('#contacts-modal').reveal({
                    closeonbackgroundclick: true,
                    middle: true
                });
                return false;
            });

        }


        $('.link-to-image').fluxusLightbox();



        /**
         * --------------------------------------------------------------------------------
         * Shortcodes.
         * --------------------------------------------------------------------------------
         */

        /**
         * Shortcode: Tabs
         */
        $('.tabs').each( function () {

            var $t = $(this );

            $t.find('.tabs-menu a').click(function () {

                var $t = $(this ),
                    $p = $t.parent(),
                    index = $p.prevAll().length;

                if ( $p.is('.active') ) {
                    return false;
                }

                $p.parent().find('.active').removeClass('active');
                $p.addClass('active');

                $p.closest('.tabs').find('.tab').hide().end().find('.tab:eq(' + index + ')').show();

                return false;

            }).each( function ( index ) {

                var $t = $(this );

                $t.wrapInner( $('<span />') ).append( $('<b>' + (index + 1) + '</b class="index">') );

            })

        });


        /**
         * Shortcode: Accordion
         */
        $('.accordion').each( function () {

            var $accordion = $(this );

            $accordion.find('.panel-title a').click( function () {

                var $t = $(this );

                /**
                 * This is the active panel. Let's collapse it.
                 */
                if ( $t.closest('.panel-active').length ) {
                    $t.closest('.panel-active').find('.panel-content').slideUp( 500, function () {
                        $(this ).closest('.panel-active').removeClass('panel-active');
                    });
                    return false;
                }

                var $newPanel = $t.closest('.panel');
                var index = $newPanel.prevAll().length;

                $panelActive = $accordion.find('.panel-active');

                if ( $panelActive.length ) {

                    $panelActive.find('.panel-content').slideUp( 500, function () {
                        $(this ).closest('.panel').removeClass('panel-active');
                        $accordion.find('.panel:eq(' + index + ') .panel-content').slideDown( 300 )
                                  .closest('.panel').addClass('panel-active');

                    });

                } else {

                    $accordion.find('.panel:eq(' + index + ') .panel-content').slideDown( 300 )
                              .closest('.panel').addClass('panel-active');

                }

                return false;

            })

        });


        /**
         * Shortcode: Gallery
         */
        var $galleries = $('.gallery-link-file');

        if ( $galleries.length ) {

            $galleries.each( function () {

                $(this).find('a').fluxusLightbox();

            })

        }

    });


})(jQuery, window, jQuery(window));