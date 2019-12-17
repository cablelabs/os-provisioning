@extends ('Generic.edit')

@section ('javascript_extra')
    @if (Module::collections()->has('PropertyManagement'))
        @include('provbase::Modem.hideAddress')
    @endif
@stop
