<div class="height-sm" style="padding: 0px; position: relative;">
    <table class="table">
        <thead>
        <tr>
            @foreach ($services['hdr'] as $hdr)
                <th>{{$hdr}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach ($services['row'] as $i => $row)
            <tr class = "clickable {{$services['clr'][$i]}}" data-toggle="collapse" data-target=".{{$i}}collapsedservice">
                @foreach ($row as $j => $data)
                    @if($j)
                        <td class='f-s-13'>{{$data}}</td>
                    @elseif(count($services['perf'][$i]))
                        <td class='f-s-13'><i class="fa fa-plus"></i>{{$data}}</td>
                    @else
                        <td class='f-s-13'><i class="fa fa-info"></i>{{$data}}</td>
                    @endif
                @endforeach
            </tr>
            @foreach ($services['perf'][$i] as $perf)
                <tr class="collapse {{$i}}collapsedservice">
                    <td colspan="4">
                        @if($perf['per'] !== null)
                                <div class="progress progress-striped">
                                    <?php $cls = ($perf['cls'] !== null) ? $perf['cls'] : $services['clr'][$i]; ?>
                                    <div class="progress-bar progress-bar-{{$cls}}" style="width: {{$perf['per']}}%"><span class='text-inverse'>{{$perf['text']}}</span></div>
                                </div>
                        @else
                            {{$perf['text']}}: {{$perf['val']}}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>
