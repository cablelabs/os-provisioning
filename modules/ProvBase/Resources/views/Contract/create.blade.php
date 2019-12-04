@extends ('Generic.create')

@section ('javascript_extra')
    @if (Module::collections()->has('PropertyManagement'))
        @include('provbase::Contract.hideAddress')
    @endif
@stop
