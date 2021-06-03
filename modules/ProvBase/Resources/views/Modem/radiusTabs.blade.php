@if ($radius)
    @foreach($radius as $tablename => $tableData)
        {{-- ['DT_Last Sessions', 'DT_Replies', 'DT_Authentications'] --}}
        <div class="tab-pane fade in" id="{{ $tablename }}">

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
                                    <td class="text-center"><font color="grey">n/a</font></td>
                                    @continue
                                @endif
                                <td class="text-center"><font color="grey">{!! $colarray[$i] !!}</font></td>
                            @endforeach
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    @endforeach
@endif
