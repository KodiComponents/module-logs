<div class="panel panel-info">
    <div class="panel-heading">
        <a href="{{ route('log-viewer::logs.download', [$log->date]) }}" class="btn btn-xs btn-success">
            <i class="fa fa-download"></i> @lang('logs::core.button.download')
        </a>
        <a href="#delete-log-modal" class="btn btn-xs btn-danger" data-toggle="modal">
            <i class="fa fa-trash-o"></i> @lang('logs::core.button.delete')
        </a>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th>@lang('logs::core.field.file_path'):</th>
            <th colspan="7">{{ $log->getPath() }}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>@lang('logs::core.field.entries'):</td>
            <td>
                <span class="label label-primary">{{ $entries->total() }}</span>
            </td>
            <td>@lang('logs::core.field.size'):</td>
            <td>
                <span class="label label-primary">{{ $log->size() }}</span>
            </td>
            <td>@lang('logs::core.field.created_at'):</td>
            <td>
                <span class="label label-primary">{{ $log->createdAt() }}</span>
            </td>
            <td>@lang('logs::core.field.updated_at'):</td>
            <td>
                <span class="label label-primary">{{ $log->updatedAt() }}</span>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-md-2">
        @include('log-viewer::_partials.menu')
    </div>
    <div class="col-md-10">
        <div class="panel panel-default">
            <table id="entries" class="table table-stripedtable-hover ">
                <thead>
                    <tr>
                        <th>ENV</th>
                        <th style="width: 120px;">Level</th>
                        <th style="width: 65px;">Time</th>
                        <th>@lang('logs::core.field.header')</th>
                        <th class="text-right">@lang('logs::core.field.actions')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($entries as $key => $entry)
                    <tr>
                        <td>
                            <span class="label label-env">{{ $entry->env }}</span>
                        </td>
                        <td>
                            <span class="level level-{{ $entry->level }}">
                                {!! $entry->level() !!}
                            </span>
                        </td>
                        <td>
                            {{ $entry->datetime->format('H:i:s') }}
                        </td>
                        <th>
                            {{ $entry->header }}
                        </th>
                        <td class="text-right">
                            @if ($entry->hasStack())
                                <a class="btn btn-xs btn-default" role="button" data-toggle="collapse" href="#log-stack-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                    <i class="fa fa-toggle-on"></i> Stack
                                </a>
                            @endif
                        </td>
                    </tr>
                    @if ($entry->hasStack())
                        <tr>
                            <td colspan="5" class="stack">
                                <div class="stack-content collapse" id="log-stack-{{ $key }}">
                                    {!! preg_replace("/\n/", '<br>', $entry->stack) !!}
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>

            @if ($entries->hasPages())
                <div class="panel-footer">
                    {!! $entries->render() !!}
                </div>
            @endif
        </div>
    </div>
</div>


@include('logs::delete', [
    'date' => $log->date,
    'message' => "Are you sure you want to <span class=\"label label-danger\">DELETE</span> this log file <span class=\"label label-primary\">{$log->date}</span> ?"
])

@push('scripts')
<script>
    $(function () {
        var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm = $('form#delete-log-form'),
                submitBtn = deleteLogForm.find('button[type=submit]');

        deleteLogForm.submit(function (event) {
            event.preventDefault();
            submitBtn.button('loading');

            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                dataType: 'json',
                data: $(this).serialize(),
                success: function (data) {
                    submitBtn.button('reset');
                    if (data.result === 'success') {
                        deleteLogModal.modal('hide');
                        location.replace("{{ route('log-viewer::logs.list') }}");
                    }
                    else {
                        alert('OOPS ! This is a lack of coffee exception !')
                    }
                },
                error: function (xhr, textStatus, errorThrown) {
                    alert('AJAX ERROR ! Check the console !');
                    console.error(errorThrown);
                    submitBtn.button('reset');
                }
            });

            return false;
        });
    });
</script>
@endpush
