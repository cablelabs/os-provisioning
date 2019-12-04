<script type="text/javascript">

    function showFields() {
        if ($('#apartment_id').val() == '') {
            $(".noApartment").show();
        } else {
            $(".noApartment").hide();
        }
    }

    showFields();

    $('#apartment_id').change(function() {
        showFields();
    });
</script>
