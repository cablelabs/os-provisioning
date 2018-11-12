{{-- these are the 'real' checkboxes (behind the javascript checkboxes) of the formular to make the http post work --}}
@foreach($items->load('children') as $item)
<input type="hidden" id="myFieldids[{{$item->id}}]" name="" value="1" />
  @if($item->children->count())
    @include('Generic.tree_hidden_helper', array('items' => $item->children))
  @endif
@endforeach
