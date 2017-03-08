<ul style="list-style-type:none;">
@foreach($items as $item)
	<li>
	{{ Form::checkbox('ids['.$item->id.']', 1, null, null, ['style' => 'simple', 'disabled' => in_array($item->id, $cf_used) ? 'disabled' : null]) }}
	{{ HTML::linkRoute('Configfile.edit', $item->view_index_label(), $item->id) }}
	@if(count( $item->children) > 0 )
		@include('provbase::Configfile.tree_item', array('items' => $item->children))
	@endif
	</li>
@endforeach
</ul>