@foreach($items as $item)
<input type="hidden" id="myFieldids[{{$item->id}}]" name="" value="1" />
  @if($item->children()->count() > 0 )
    @include('Generic.tree_hidden_helper', array('items' => $item->children))
  @endif
@endforeach
