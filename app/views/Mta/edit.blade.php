@extends('Generic.edit')

{{--
@section('content_top')
	{{ HTML::linkRoute('Modem.edit', $view_var->modem->hostname, $view_var->modem->id) }} / 
	{{ HTML::linkRoute($model_name.'.edit', $view_var->get_view_link_title(), $view_var->id) }}
@stop
--}}

@include('Generic.relation', ['relations' => $view_var->phonenumbers, 'view' => 'Phonenumber', 'key' =>'mta_id' ])