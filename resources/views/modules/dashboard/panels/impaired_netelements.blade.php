<div class="height-sm" style="padding: 0px; position: relative;">
    <table class="table">
        <thead>
        <tr>
            @foreach ($netelements['hdr'] as $hdr)
                <th>{{$hdr}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach ($netelements['row'] as $row)
            <tr class = "{{array_shift($netelements['clr'])}}">
                @foreach ($row as $data)
                    <td>{{$data}}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
