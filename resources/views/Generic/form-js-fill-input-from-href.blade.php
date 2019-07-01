{{--

This blade is used for jquery (java script) setting of input content from href.

NOTE: will be used from form-js blade and must be called inside javascript context


@author Patrick Reichel

--}}

{{--
Currently used with fixed ids.
If useful for other forms, too: Try to make a generic function.
--}}

@if (isset($load_input_from_href_filler_for_free_numbers))

    $('#free_numbers_return a').click(function() {
        var value = $(this).text();
        var parts = value.split('/');
        var input_prefix_number = $('#prefix_number');
        var input_number = $('#number');
        input_prefix_number.val(parts[0]);
        input_number.val(parts[1]);
        return false;
    });

@endif
