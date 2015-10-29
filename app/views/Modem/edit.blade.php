@extends ('Generic.edit')

@include ('Modem.header')

@include('Generic.relation', ['relations' => $view_var->mtas, 'view' => 'Mta', 'key' =>'modem_id' ])