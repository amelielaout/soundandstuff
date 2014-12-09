
(function ( $, window, undefined ) {

    $(function () {

        var $body = $( 'body' ),
            $map = $( "#map" );

        // no map found.
        if ($map.length == 0) {
            return;
        }

        // Contacts page DOM
        var $mapDim     = $( '#map-dim' ),
            $content    = $( '#content, .contacts-background' ),
            $btnView    = $( '#view-map' ),
            $btnClose   = $( '#close-map' ),
            initialDimOpacity = $mapDim.css( 'opacity' );


        /**
         * Create a new StyledMapType object, passing it the array of styles,
         * as well as the name to be displayed on the map type control.
         */
        var styles = [
            {
                featureType: "road",
                elementType: "labels",
                stylers: [
                    { visibility: "off" }
                ]
            }
        ];
        var styledMap = new google.maps.StyledMapType( styles, { name: "Styled Map" } );
        var mapOptions = {
              center: new google.maps.LatLng( $map.data( 'latitude' ), $map.data( 'longitude' ) ),
              zoom: 15,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            };

        var map = new google.maps.Map( $("#map").get(0), mapOptions );

        map.setOptions({
                styles: styles
            });


        if ( ! $map.data( 'icon-latitude' ) && $map.data( 'latitude' ) ) {
            $map.data( 'icon-latitude', $map.data( 'latitude' ) );
        }

        if ( ! $map.data( 'icon-longitude' ) && $map.data( 'longitude' ) ) {
            $map.data( 'icon-longitude', $map.data( 'longitude' ) );
        }

        var markerPosition = new google.maps.LatLng(
                $map.data( 'icon-latitude' ),
                $map.data( 'icon-longitude' )
            );

        var marker = new google.maps.Marker({
            position: markerPosition,
            icon: $map.data( 'icon-image' )
        });

        marker.setMap( map );



        /**
         * View map button.
         */
        $btnView.click( function () {

            $body.addClass( 'map-active' );

            $content.fadeOut( 500, function () {

                $btnClose.delay( 500 ).show().animate({
                    left: 20
                }, 1000);

            });

            $mapDim.fadeOut( 1000, function () {

                // disable custom style
                map.setOptions({
                    styles: null
                });

            });

            $( window ).bind( 'keydown.fluxus-maps', function ( e ) {
                if ( e.which == 27 ) {
                    $btnClose.click();
                    return false;
                }
            });

            return false;

        });


        /**
         * Close map button.
         */
        $btnClose.click( function () {

            $body.removeClass( 'map-active' );

            $( this ).animate({
                left: -200
            }, 500, function () {

                $( this ).hide();

                // enable style
                map.setOptions({
                        styles: styles
                    });

                $mapDim.css( 'opacity', 0 ).show().animate({
                    opacity: initialDimOpacity
                }, 1000, function () {
                    $content.fadeIn( 500 );
                });

            });

            $( window ).unbind( 'keydown.fluxus-maps' );

            return false;

        });

    });

})( jQuery, window );
