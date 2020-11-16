@section ($title.'-chart')
    @foreach($data as $state => $infos)
        <div class="d-flex m-b-5 align-items-baseline">
            <i class="fa fa-circle text-{{ $state }} m-r-5"></i>
            {{ $infos['count'].' '.$infos['text'] }}
        </div>
    @endforeach
@endsection

@include ('HfcBase::troubledashboard.summarycard', [
    'title' => $title,
    'content' => $title.'-chart',
])
