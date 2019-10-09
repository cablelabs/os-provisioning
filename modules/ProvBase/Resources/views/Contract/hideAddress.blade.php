@if (\Module::collections()->has('PropertyManagement'))
<script type="text/javascript">
    if (! ($('#realty_id').val() == '' && $('#apartment_id').val() == '')) {
        $(".noProperty").hide();
    }

    $('#realty_id, #apartment_id').change(function() {
        if ($('#realty_id').val() == '' && $('#apartment_id').val() == '') {
            $(".noProperty").show();
        } else {
            $(".noProperty").hide();
        }
    });
</script>
@endif
