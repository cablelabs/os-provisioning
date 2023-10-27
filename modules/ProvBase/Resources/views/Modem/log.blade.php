<div class="tab-pane fade in" id="{{ $id }}">
    @if ($log)
        <span class="text-green-600">
            <b>Modem Logfile</b>
        </span>
        <br>
        @foreach ($log as $line)
            <table>
                <tr>
                    <td>
                        <span color="grey">{{$line}}</span>
                    </td>
                </tr>
            </table>
        @endforeach
    @else
        <span class="text-red-600">{{ trans('messages.modem_log_error') }}</span>
    @endif
</div>
