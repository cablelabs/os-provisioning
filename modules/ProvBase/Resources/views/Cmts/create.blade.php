{{--

@param $headline: the link header description in HTML

@param $form_path: the form view to be displayed inside this blade (mostly Generic.edit)
@param $route_name: the base route name of this object class which will be added

--}}

@include ('Generic.create')


{{-- Reload IP Pool calculation when ip pool company field changes --}}
<script language="javascript">

$('#company').change(function() {
	location.href = location.href + "?&company=" + document.getElementById("company").options[document.getElementById("company").selectedIndex].value;
});

</script>
