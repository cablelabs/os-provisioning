@extends('Generic.edit')

@include('Generic.relation', ['relations' => $view_var->ippools, 'view' => 'IpPool', 'key' =>'cmts_id' ])