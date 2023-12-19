@if ($radius)
    @foreach($radius as $tablename => $tableData)
        {{-- ['DT_Last Sessions', 'DT_Replies', 'DT_Authentications'] --}}
        <div class="tab-pane fade in" id="{{ Str::slug($tablename, '_') }}">

            <div class="table-responsive">
                <table class="table streamtable table-bordered radius-table" width="auto">
                    <thead>
                        <!-- <th/> -->
                        @foreach ($tableData as $colHeader => $colData)
                            <th class="active text-center">{{ $colHeader }}</th>
                        @endforeach
                    </thead>
                    <tbody>
                        @foreach($colData as $i => $field)
                        <tr>
                            <!-- <td/> -->
                            @foreach ($tableData as $colHeader => $colarray)
                                @if (! isset($colarray[$i]))
                                    <td class="text-center"><span color="grey">n/a</span></td>
                                    @continue
                                @endif
                                <td class="text-center"><span color="grey">{!! $colarray[$i] !!}</span></td>
                            @endforeach
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    @endforeach
@endif
