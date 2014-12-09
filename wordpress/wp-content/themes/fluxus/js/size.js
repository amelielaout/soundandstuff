
/**
 * Various size adjustments.
 */

;(function ($, $window, isUndefined) {

    $(function () {

        var $html       = $('html'),
            $main       = $('#main'),
            $header     = $('#header'),
            $footer     = $('#footer'),

            // Pages
            $pagePortfolio       = $('.portfolio-list'),
            $pageGridPortfolio   = $('.portfolio-grid'),
            $pageAttachment      = $('.single-attachment'),
            $pageContacts        = $('.page-contacts'),

            // Components
            $scrollContainer     = $('.scroll-container');

        // Content area height = window.height - header.height - footer.height;
        window.getContentHeight = function () {
            return $window.height() - $header.outerHeight() - $header.offset().top - $footer.outerHeight();
        }

        /**
         * Horizontal page adjustements on window resize.
         */
        function horizontal() {

            var windowWidth = $window.width(),
                windowHeight = $window.height(),

                // The header is position:fixed
                // we have to calculate the offset for main page dynamically.
                headerHeight = $header.outerHeight() + $header.offset().top;
                footerHeight = $footer.outerHeight();


            /**
             * If we are on a small screen.
             */
            if ( windowWidth <= 480 ) {

                if ( $html.is( '.no-scroll' ) ) {

                    $main.css({
                        height: windowHeight - headerHeight,
                        top: 0
                    });

                } else {

                    $main.css({
                        height: 'auto',
                        top: 0
                    });

                }

                if ( $pagePortfolio.length ) {

                    $pagePortfolio.find( '.info' ).css({
                        height: 'auto'
                    });

                }

                if ( $pageGridPortfolio.length ) {

                    var grid = $pageGridPortfolio.data( 'grid' );
                    if ( grid != isUndefined ) {
                        grid.disable();
                    }

                }

                /**
                 * Page: Contacts
                 */
                if ( $pageContacts.length ) {
                    contactsPageResizer();
                }

                return;

            }

            var mainHeight = getContentHeight();

            $main.css({
                height: mainHeight,
                top: headerHeight
            });

            /**
             * Resizes images, so that post blocks can fit in available window height.
             */
            if ( $html.is( '.horizontal-posts' ) ) {

                var $postSummaries = $('.post-with-media .text-contents'),
                    minSummaryHeight = $postSummaries.highestElement().outerHeight();

                if ( minSummaryHeight ) {

                    var maxMediaHeight = $main.height() - minSummaryHeight;
                    maxMediaHeight = maxMediaHeight > 328 ? 328 : maxMediaHeight;

                    $('.resizable').each( function () {

                        $(this).css('height', maxMediaHeight);

                        var containerWidth = Math.round(maxMediaHeight / 1.777439024);
                        containerWidth = containerWidth < 583 ? 583 : containerWidth;

                        $(this).closest('.post')
                               .css('width', containerWidth);

                    });

                    $( '.wrap-embed-video' ).each( function () {

                        var $t = $(this),
                            $article = $t.closest('article'),
                            $object = $t.children('iframe:first'),
                            ratio = $object.width() / $object.height();

                        $article.css( 'width', Math.round( maxMediaHeight * ratio ) );

                    });

                }

            }


            /**
             * Page: Horizontal portfolio
             */
            if ($pagePortfolio.length) {

                if (windowWidth > 768) {

                    var $info = $pagePortfolio.find('.info'),
                        highestHeight = $info.highestElement().outerHeight();

                    $pagePortfolio.find('.featured-image').each(function () {

                        var $t = $(this);

                        if ($t.is('img')) {

                            $t.css('height', mainHeight - highestHeight);

                        }

                    });

                    $('.hover-box-contents').vcenter();

                    if ($html.is('.ipad-ios4')) {

                        var totalWidth = 0;

                        $pagePortfolio.children().each(function () {
                            totalWidth += $(this).width();
                        });

                        $pagePortfolio.css({
                            width: totalWidth
                        });

                    }

                } else {

                    if ($html.is('.ipad-ios4')) {

                        $pagePortfolio.css({
                            width: 'auto'
                        });

                    }

                }

            }


            /**
             * Page: Grid portfolio
             */
            if ( $pageGridPortfolio.length ) {

                var grid = $pageGridPortfolio.data( 'grid' );
                if ( grid != isUndefined ) {
                    grid.resize();
                }

            }


            /**
             * Page: Contacts
             */
            if ( $pageContacts.length ) {
                contactsPageResizer();
            }

        }


        /**
         * General size adjustments on resize.
         */
        function general() {

            var windowWidth  = $window.width();
            var windowHeight = $window.height();

            /**
             * Update tinyscrollbar values.
             */
            $scrollContainer.each( function () {

                var tsb = $( this ).data( 'tsb' );

                $( this ).find( '.scrollbar,.track' ).css({
                    height: $( this ).height()
                });

                if ( tsb != isUndefined ) {
                    tsb.update();
                }

            });


            if ( windowWidth <= 768 ) {

                /**
                 * For performance reasons initialize mobile menu only
                 * if we have a small sceen size.
                 */
                if ( window.mobileNav == isUndefined ) {

                    /**
                     * Make mobile menu item array.
                     */
                    var $siteNavigation = $( '.site-navigation' );
                    var $mobileNavItems = $siteNavigation.find( 'a' ).filter( function () {

                        var $t      = $(this);
                        var level   = $t.parents( 'ul' ).length;
                        $t.data( 'level', level );

                        if ( level == 1 ) {
                            return true;
                        } else {
                            if ( $t.closest('.current-menu-item, .current_page_ancestor').length ) {
                                return true;
                            }
                        }
                        return false;

                    });

                    /**
                     * Initialize mobile menu.
                     */
                    window.mobileNav = new MobileNav($mobileNavItems, {
                        openButtonTitle: $siteNavigation.data('menu'),
                        active: $siteNavigation.find('.current-menu-item > a')
                    });

                }

            }

            /**
             * Trigger vertical center plugin.
             */
            setTimeout( function () {
                $( '.js-vertical-center' ).verticalCenter();
            }, 100 );

        }


        function contactsPageResizer() {

            var $infobox   = $pageContacts.find('.page'),
                $viewport  = $infobox.children('.viewport'),
                infoHeight = $infobox.outerHeight(),
                iscroll    = $infobox.data('iscroll'),
                mainHeight = parseInt($window.height()) - parseInt($header.height()) - parseInt($footer.height());

            if ( ! iscroll ) {

                iscroll = new iScroll($infobox.get(0), {
                                    hideScrollbar: false,
                                    scrollbarClass: 'iscrollbar'
                                });

                $infobox.data( 'iscroll', iscroll );

            }

            if ($viewport.height() > mainHeight) {

                $infobox.css({
                    top: 0,
                    height: '100%'
                });

                if (iscroll.enabled == false) {
                    iscroll.enable();
                }
                iscroll.refresh();

            } else {

                var top = Math.round( ( mainHeight - infoHeight ) / 2 );
                top = top < 0 ? 0 : top;

                $infobox.css({
                            top: top,
                            height: 'auto'
                        });

                if (iscroll.enabled == true) {
                    iscroll.disable();
                }

            }

        }


        /**
         * Bind horizontal-resize event if we are on a horizontal page.
         */
        if ( $html.is( '.horizontal-page' ) ) {

            $window.on( 'resize.fluxus.horizontal-page', debounce( horizontal ) );
            horizontal();

        }


        /**
         * Also bind window resize event to general function.
         */
        $(window).on( 'resize.fluxus.general', debounce( general ) );
        general();

    });

}(jQuery, jQuery(window)));

