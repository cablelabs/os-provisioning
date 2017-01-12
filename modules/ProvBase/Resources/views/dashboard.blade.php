@extends ('Layout.default')

@section('content')
    <div class="col-md-12">

        <h1 class="page-header">{{ $title }}</h1>

        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-aqua">
                    <div class="stats-icon">
                        <i class="fa fa-globe fa-fw"></i>
                    </div>
                    <div class="stats-info">
                        <h4>Verträge total</h4>
                        <p>{{ $contracts_all }}</p>
                        <p>{{ $contract_count }} valide Verträge</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="widget widget-stats bg-aqua">
                    <div class="stats-icon">
                        <i class="fa fa-globe fa-fw"></i>
                    </div>
                    <div class="stats-info">
                        <h4>Verträge {{ $name_of_current_month }}</h4>
                        <p>{{ $contract_count_current_month }} valide Verträge</p>
                    </div>
                </div>
            </div>


<!-- Nur für Tests
            <div class="col-md-3 col-sm-6">
                <ul>
                    @foreach ($contracts as $contract)
                        <li>{{ $contract->id }}: {{ $contract->contract_start }} - {{ $contract->contract_end }}</li>
                    @endforeach
                </ul>
            </div>
 -->
        </div>
    </div>
@stop