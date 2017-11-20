{{--

@param $headline: the link header description in HTML

@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $route_name: the base route name of this object class which will be added

--}}

@include ('Generic.create')


{{-- Reload IP Pool calculation when ip pool type field [CM, MTA ..] changes --}}
<script language="javascript">

$('#type').change(function() {
	location.href = location.href + "&type=" + document.getElementById("type").options[document.getElementById("type").selectedIndex].value;
});

</script>
