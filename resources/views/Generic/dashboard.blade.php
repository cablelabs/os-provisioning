@extends('Layout.default')

@section('content')
    <div class="p-3">
        <h1 class="page-header dark:text-slate-100">{{ $title }}</h1>
        @yield('dashboard')
    </div>
@endsection
