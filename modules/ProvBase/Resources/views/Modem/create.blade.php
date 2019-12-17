@extends ('Generic.create')

@section ('javascript_extra')
    @if (Module::collections()->has('PropertyManagement'))
        @include('provbase::Modem.hideAddress')
    @endif
@stop
