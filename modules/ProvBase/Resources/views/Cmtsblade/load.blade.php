{{--

This blade is for loading the correct CMTS company blade, like cisco

--}}

@if (View::exists('provbase::Cmtsblade.'.strtolower($view_var->company)))
	<pre>@include ('provbase::Cmtsblade.'.strtolower($view_var->company))</pre>
@else
	<b>Everything works fine! There is just no assigned configuration proposal for {{$view_var->type}} CMTS from {{$view_var->company}} until now.</b><br><br>
	Be the first one who creates a default proposal config in this
	<a href="https://github.com/nmsprime/nmsprime/tree/master/modules/ProvBase/Resources/views/Cmtsblade" target="_blank">Github folder</a>
	<br><br>
	The file must be called {{strtolower($view_var->company)}}.blade.php.
	For more information checkout cisco.blade.php and the function prep_cmts_config_page() in
	<a href="https://github.com/nmsprime/nmsprime/blob/master/modules/ProvBase/Entities/Cmts.php" target="_blank">Cmts.php at Github</a>
@endif
