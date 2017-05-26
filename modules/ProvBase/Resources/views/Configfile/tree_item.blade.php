<ul>
@foreach($items as $item)
	<li id="ids[{{$item->id}}]" class="f-s-14 p-t-5" data-jstree='{"opened":true,"disabled": {{in_array($item->id, $cf_used) ? "true" : "false"}} }'>
	{{ HTML::linkRoute('Configfile.edit', $item->view_index_label(), $item->id) }}
	@if(count( $item->children) > 0 )
		@include('provbase::Configfile.tree_item', array('items' => $item->children))
	@endif
</li>
@endforeach
</ul>
