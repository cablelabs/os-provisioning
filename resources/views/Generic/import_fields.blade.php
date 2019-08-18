@extends('Layout.default')

@section('content')
    <div class="container">
        <div class="row">
            <div class="">
                <div class="panel panel-default">
                    <div class="panel-heading">CSV Import</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route($route_name.'.import_process') }}">
                            {{ csrf_field() }}
                            <input type="hidden" name="csv_data_file_id" value="{{ $csv_data_file->id }}" />

                            <table class="table">
                                @if (isset($csv_header_fields))
                                <tr>
                                    @foreach ($csv_header_fields as $csv_header_field)
                                        <th>{{ $csv_header_field }}</th>
                                    @endforeach
                                </tr>
                                @endif
                                @foreach ($csv_data as $row)
                                    <tr>
                                    @foreach ($row as $key => $value)
                                        <td>{{ $value }}</td>
                                    @endforeach
                                    </tr>
                                @endforeach
                                <tr>
                                    @foreach ($csv_data[0] as $key => $value)
                                        <td>
                                            <select name="fields[{{ $key }}]">
                                                @foreach ($db_fields as $db_field)
                                                    <option value="{{ (\Request::filled('header')) ? $db_field : $loop->index }}"
                                                        {{-- $loop will work with > L5.3 --}}
                                                        @if ($key === $db_field) selected @endif>{{ $db_field }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            </table>

                            <button type="submit" class="btn btn-primary">
                                Import Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
