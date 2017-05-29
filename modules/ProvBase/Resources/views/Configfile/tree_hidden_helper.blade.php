@foreach($items as $item)
<input type="hidden" id="myFieldids[{{$item->id}}]" name="" value="1" />
  @if(count( $item->children) > 0 )
    @include('provbase::Configfile.tree_hidden_helper', array('items' => $item->children))
  @endif
@endforeach
