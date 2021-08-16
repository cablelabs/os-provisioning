@if ($publish)
let tmp;
let values = {};
const columns = [@foreach ($columns as $column)'{{$column}}',@endforeach];
@endif

@foreach ($entries as $entry)
@if ($entry[2])
tmp = declare('{{$entry[0]}}', {value: Date.now()}); if (tmp && tmp.value) { values['{{$entry[2][0]}}'] = tmp.value[0] {{$entry[2][1]}} {{$entry[2][2]}}; }
@else
declare('{{$entry[0]}}', {value: Date.now()});
@endif
@endforeach

@if ($publish)
log(columns.map(k => (k in values) ? values[k] : 'null').join(','));
@endif
