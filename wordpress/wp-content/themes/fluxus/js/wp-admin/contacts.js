
(function ($) {

    $(function () {

        var $btnAdd = $('#fluxus-add-contact');
        var $container = $('.fluxus-contact-information tbody');

        $btnAdd.click(function () {

                var $title = $('input[name="fluxus_contact_info_add_title"]');
                var $content = $('textarea[name="fluxus_contact_info_add_content"]');

                var $newElement = $('<tr class="fluxus-contact" />');
                $('<input type="text" name="fluxus_contact_info_title[]" />').val($title.val())
                                                                             .appendTo($newElement)
                                                                             .wrap('<td />');

                $('<textarea name="fluxus_contact_info_content[]" />').val($content.val())
                                                                      .appendTo($newElement)
                                                                      .wrap('<td />');

                $newElement.insertBefore($container.find('.add-element'));

                $title.val('');
                $content.val('');

                return false;

            });

    });

})(jQuery);