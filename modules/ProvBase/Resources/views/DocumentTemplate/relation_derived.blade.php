@if (isset($derived_documenttemplates) && is_array($derived_documenttemplates))
    @DivOpen(12)
    <table class="table">
        @foreach($derived_documenttemplates as $key => $type)
            <tr class="success">
                <td> {!! \Modules\ProvBase\Entities\DocumentTemplate::view_icon() !!} {!! HTML::linkRoute('DocumentTemplate.edit', $type, $key) !!}</td>
            @endforeach
            </tr>
    </table>
    @DivClose()
@endif

