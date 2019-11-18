<script type="text/javascript">

    function showFields() {
        if ($('#realty_id').val() == '' && $('#apartment_id').val() == '') {
            $(".noRealty, .noApartment").show();
        } else if ($('#realty_id').val() != '') {
            $(".noApartment").show();
            $(".noRealty").hide();
        } else {
            $(".noRealty").show();
            $(".noApartment").hide();
        }
    }

    showFields();

    $('#realty_id, #apartment_id').change(function() {
        showFields();
    });
</script>
