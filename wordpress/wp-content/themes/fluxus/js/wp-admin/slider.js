
(function ( $, window, isUndefined ) {

    $(function () {

        var $setPosition = $( '#slider-set-position' );

        if ( $setPosition.length ) {

            /**
             * We are in set position mode.
             */

            var $box = $( ".slide .info" );
            var $parent = $box.parent();

            $box.draggable({
                containment: $parent,
                scroll: false
            });

            $( "#slider-set-position .save-position" ).click( function () {

                /**
                 * Position in %
                 */
                var x = $box.position().left / $parent.width();
                var y = $box.position().top / $parent.height();

                x = Math.round( x * 10000 ) / 100;
                y = Math.round( y * 10000 ) / 100;

                var slideId = $parent.attr('id').match(/\d+/);

                var $opener = window.opener.jQuery( '#slide_' + slideId + '_details' );

                $opener.find('[name=slide_info_box_left]').val( x + '%' );
                $opener.find('[name=slide_info_box_top]').val( y + '%' );

                var $selectedOption = $opener.find( '[name=slide_position] option:selected' ).removeAttr( 'selected' )
                                      .end().find( 'option[value=custom]' ).attr( 'selected', 'selected' );

                $selectedOption.html( $selectedOption.data('custom') + ' (' + x + '% ' + y +'%)' );

                window.close();

                return false;

            });

            return; // dont do anything else

        }

        var $slideTable = $( '#slides' ),
            $tab        = $( '#fluxus-slider-tab' ),
            post_id     = $tab.data( 'post-id' );

        /**
         * Toggles slide details on button click
         * Note: we have to detach() it from DOM,
         * because jQuery sortable tend to flickr if there
         * is hidden elements in the list being sorted.
         */
        $slideTable.find( '.show-toggle' ).each( function () {

            var $t = $(this);
            var $details = $t.closest( 'tr' ).find( '.details' );
            $details = $details.detach().show().wrap( '<tr class="wrap-details" />' ).wrap( '<td colspan="4" />' ).closest( 'tr' );
            $t.data( 'details', $details );

        }).click( function () {

            var $t = $( this );
            var $details = $t.data( 'details' );

            if ($details.is( ':visible' )) {
                $details.detach();
                $t.html( $t.data('show') );
            } else {
                $details.insertAfter( $t.closest('tr') ).slideDown();
                $t.html( $t.data('hide') );
            }

            return false;

        });

        var savingTimeout = 0;

        /**
         * Saves slide information.
         */
        $slideTable.find('.fluxus-save-slide').live( 'click', function () {

            clearTimeout( savingTimeout );

            var $t = $(this);
            var $form = $t.closest('form');
            var data = $form.serialize();
            var $status = $form.find('.saving-status').removeClass('ok failed');

            $status.html($status.data('saving')).show();

            $.post(ajaxurl, data, function (response) {

                if (response == '1') {

                    var $slide_info_row = $t.closest('tr').prev();
                    var $wrap_slide_status   = $slide_info_row.find('.wrap-slide-status');
                    var $wrap_slide_title    = $slide_info_row.find('.wrap-slide-title');
                    var new_slide_title = $form.find('[name=slide_title]').val();

                    $status.html($status.data('ok')).addClass('ok');

                    if (new_slide_title) {
                        $wrap_slide_title.removeClass('slide-title-empty').find('.slide-title').html(new_slide_title);
                    } else {
                        $wrap_slide_title.addClass('slide-title-empty').find('.slide-title').html('');
                    }


                    if ($form.find('[name=slide_published]').is(':checked')) {
                        $wrap_slide_status.removeClass('slide-unpublished')
                                          .addClass('slide-published');
                    } else {
                        $wrap_slide_status.removeClass('slide-published')
                                          .addClass('slide-unpublished');
                    }

                } else {
                    $status.html($status.data('failed')).addClass('failed');
                }
                savingTimeout = setTimeout(function () {
                    $status.fadeOut(300);
                }, 2000);
            });

            return false;

        });

        /**
         * Enable JS sorting of slides.
         */
        $("#slides tbody").sortable({
            handle: '.slide-move',
            start: function () {
                $slideTable.find('.wrap-details').each(function () {
                    // close any open slides
                    $(this).detach();
                });
                $slideTable.find('.show-toggle').each(function () {
                    $(this).html($(this).data('show'));
                })
            },
            stop: function () {

                var $form = $('#fluxus-slider-order-form');

                var order = [];
                $slideTable.find('.slide-id').each(function () {
                    order.push($(this).val());
                })
                $form.find('[name="order"]').val(order.join(','));

                var data = $form.serialize();

                $.post(ajaxurl, $form.serialize(), function (data) {
                    if (data != '1') {
                        var $unableToSave = $('<div class="error">Unable to save ordering</div>');
                        $tab.append($unableToSave);
                        setTimeout(function () {
                            $unableToSave.remove();
                        }, 10000);
                    }

                });

            }
        });

        $(".set-infobox-position").click(function () {
            var $t = $(this);
            window.open($t.attr('href'), 'positionWindow');
            return false;
        });

        var inputChangeDebounce = 0;

        $('#fluxus-slider-options-form input').change(function () {

            clearTimeout(inputChangeDebounce);

            var $t = $(this),
                $form = $('#fluxus-slider-options-form'),
                security = $form.find('[name="security"]').val(),
                action = $form.find('[name="action"]').val(),
                key = $t.attr('name');

            if ('fluxus_slider_slideshow' === $t.attr('name')) {

                value = $t.is(':checked') ? 1 : 0;

            }

            if ('fluxus_slider_slideshow_interval' === $t.attr('name')) {

                temp = $t.val();

                if (/^\d+$/.test(temp)) {
                    if ((temp > 0) && (temp < 4000)) {
                        value = temp;
                    }
                }

            }

            if ( typeof value == 'undefined' ) {
                return;
            }

            var data = {
                security: security,
                action: action,
                key: key,
                value: value,
                post_id: post_id
            }

            inputChangeDebounce = setTimeout(function () {

                $.post(ajaxurl, data, function (response) {
                });

            }, 300);

        });


    });

})( jQuery, window )
