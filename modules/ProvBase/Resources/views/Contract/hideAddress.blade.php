@if (\Module::collections()->has('PropertyManagement'))
<script type="text/javascript">

    function showFields() {
        if ($('#realty_id').val() == '') {
            $(".noRealty").show();
        } else {
            $(".noRealty").hide();
        }
    }

    showFields();

    $('#realty_id').change(function() {
        showFields();
    });
</script>
@endif
