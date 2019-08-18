{{--

This blade is used for jquery (java script) realtime based showing/hiding of fields depending on checkbox state.

NOTE: will be used from form-js blade and must be called inside javascript context

@param id the id of the triggering checkbox
@author Patrick Reichel

--}}

function par__toggle_class_visibility_depending_on_checkbox(id) {

    show_class = 'show_on_' + id;
    hide_class = 'hide_on_' + id;

    if ($('#' + id).prop('checked')) {
        $('.' + show_class).show();
        $('.' + hide_class).hide();
    }
    else {
        $('.' + show_class).hide();
        $('.' + hide_class).show();
    };
};
