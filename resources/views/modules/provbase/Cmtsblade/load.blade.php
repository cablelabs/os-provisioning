{{--

This blade is for loading the correct CMTS company blade, like cisco

--}}

<pre>
@include ('provbase::Cmtsblade.'.strtolower($view_var->company))
</pre>
