
(function ( $, window, undefined ) {

    $( function () {

        var $postFormatRadio = $( '#post-formats-select [name="post_format"]' );

        if ( $postFormatRadio.length ) {

            var $allBoxes = $();

            $postFormatRadio.each( function () {

                var $metaBox = $( '#fluxus_' + $( this ).val() + '_meta_box' );

                $allBoxes = $allBoxes.add( $metaBox );

            });

            $allBoxes.hide();

            $postFormatRadio.change( function () {

                if ( $( this ).is( ':checked' ) ) {

                    $allBoxes.hide();

                    var $metaBox = $( '#fluxus_' + $( this ).val() + '_meta_box' );
                    $metaBox.show();
                }

            }).change();

        }


    })


})( jQuery, window );
