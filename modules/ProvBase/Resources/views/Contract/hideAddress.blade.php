<script type="text/javascript">

    function showFields() {
        if ($('#contact_id').val() == '' && $('#apartment_id').val() == '') {
            $(".noContact, .noApartment").show();
        } else if ($('#contact_id').val() != '') {
            $(".noApartment").show();
            $(".noContact").hide();
        } else {
            $(".noContact").show();
            $(".noApartment").hide();
        }
    }

    showFields();

    $('#contact_id, #apartment_id').change(function() {
        showFields();
    });
</script>
