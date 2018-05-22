{{--

@param $headline: the link header description in HTML

@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $route_name: the base route name of this object class which will be added

--}}

@include ('Generic.create')


{{-- Reload IP Pool calculation when ip pool netelementtype_id field [CM, MTA ..] changes --}}
<script language="javascript">

$('#netelementtype_id').change(function() {
	location.href = location.href +
		"{{ isset($_GET) ? '&' : '?&' }}netelementtype_id=" + 
		$("#netelementtype_id").options[$("#netelementtype_id").selectedIndex].value;
});

</script>
