
@extends('Generic.edit')

@section('content_left')
    @parent

    {{-- Check if a PDF creation test has run --}}
    @if (session()->has('pdf_creation_test'))
        <div class='mt-5 pt-2' style='border-top: solid 1px #ccc;'>

        {{-- If a PDF file could be generated: offer download --}}
        @if (session()->has('pdf_creation_test.success'))
            <div>
                <iframe width="0" height="0" style="visibility: hidden;" src='{!! session('pdf_creation_test.url') !!}'></iframe>
            </div>
        @else
            <h4>
                <?php echo trans('provbase::messages.documentTemplate.pdfCreateTestFailed'); ?>
            </h4>
        @endif

        @if (session()->has('pdf_creation_test.pdflatex_output'))
            {{-- The CLI output of pdflatex (stdout, stderr) --}}
            <h5>
                <a class='btn btn-info btn-sm' data-toggle='collapse' href='#collapse_pdflatex_output' role='button' aria-controls='collapse_pdflatex_output'>
                    <tt>{!! session('pdf_creation_test.cmd') !!}</tt>
                </a>
            </h5>

            <div class='collapse' id='collapse_pdflatex_output'>
                <pre style='border-left: solid #aaa 2px; padding: 20px; margin-left: 20px; background-color: #eee;'>{!! session('pdf_creation_test.pdflatex_output') !!}</pre>
            </div>
        @endif

        @if (session()->has('pdf_creation_test.other_files'))
            {{-- Contents of all files in working directory --}}
            <?php echo trans('provbase::messages.documentTemplate.pdfCreationWorkdirFiles'); ?>
            @foreach (session('pdf_creation_test.other_files') as $filename => $content)

                <h5>
                    <a class='btn btn-info btn-sm' data-toggle='collapse' href='#collapse_{{ $filename }}' role='button' aria-controls='collapse_{{ $filename }}'>
                        {{ $filename }}
                    </a>
                </h5>

                <div class='collapse' id='collapse_{{ $filename }}'>
                    <pre style='border-left: solid #aaa 2px; padding: 20px; margin-left: 20px; background-color: #eee;'>
                    Showing the content here leads to an empty site – see <?php echo __FILE__.':'.__LINE__; ?>
                    {{-- {!! $content !!} --}}
                    </pre>
                </div>

            @endforeach
        @endif

        @if (session()->has('pdf_creation_test.usable_placeholders'))
            {{-- Hint for template editors: list placeholders that can be used in a template --}}
            </div>
            <div class='mt-4 pt-2' style='border-top: solid 1px #ccc;'>

            <h5>
                <a class='btn btn-info btn-sm' data-toggle='collapse' href='#collapse_usable_placeholders' role='button' aria-controls='collapse_usable_placeholders'>
                    <?php echo trans('provbase::messages.documentTemplate.usablePlaceholders'); ?>
                </a>
            </h5>

            <div class='collapse' id='collapse_usable_placeholders'>
                {{-- <pre style='border-left: solid #aaa 2px; padding: 20px; margin-left: 20px; background-color: #eee;'> --}}
                {{-- {!! session('pdf_creation_test.usable_placeholders') !!} --}}
                <div style='margin-left: 20px; margin-right:20px'>
                <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>Placeholder</th><th>Example value</th>
                    </tr>
                </thead>
                <tbody>
                @foreach (session('pdf_creation_test.usable_placeholders') as $key => $value)
                    <tr>
                        <td>{!! $key !!}</td><td>{!! $value !!}</td>
                    </tr>
                @endforeach
                </tbody>
                </table>
                </div>
            </div>
        @endif
        </div>

    @endif

@stop
