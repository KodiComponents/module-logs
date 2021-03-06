@if(config('app.log') != 'daily')
    <h4 class="alert alert-danger alert-dark">
        @lang('logs::core.message.log_type_must_be_daily')
    </h4>
@endif

<div class="panel">
    <div class="panel-heading">
        <div class="panel-title">
            @lang('logs::core.title.index')
        </div>
    </div>
    <table class="table table-hover table-stats">
        <thead>
            <tr>
                @foreach($headers as $key => $header)
                    <th class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                        @if ($key == 'date')
                            <span class="label label-info">{{ $header }}</span>
                        @else
                            <span class="level level-{{ $key }}">
                                {!! log_styler()->icon($key) . ' ' . $header !!}
                            </span>
                        @endif
                    </th>
                @endforeach
                <th class="text-right">@lang('logs::core.field.actions')</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $date => $row)
            <tr>
                @foreach($row as $key => $value)
                    <td class="{{ $key == 'date' ? 'text-left' : 'text-center' }}">
                        @if ($key == 'date')
                            <span class="label label-primary">{{ $value }}</span>
                        @elseif ($value == 0)
                            <span class="level level-empty">{{ $value }}</span>
                        @else
                            <a href="{{ route('log-viewer::logs.filter', [$date, $key]) }}">
                                <span class="level level-{{ $key }}">{{ $value }}</span>
                            </a>
                        @endif
                    </td>
                @endforeach
                <td class="text-right">
                    <a href="{{ route('log-viewer::logs.show', [$date]) }}" class="btn btn-xs btn-info">
                        <i class="fa fa-search"></i>
                    </a>
                    <a href="{{ route('log-viewer::logs.download', [$date]) }}" class="btn btn-xs btn-success">
                        <i class="fa fa-download"></i>
                    </a>
                    <a href="#delete-log-modal" class="btn btn-xs btn-danger" data-log-date="{{ $date }}">
                        <i class="fa fa-trash-o"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{!! $rows->render() !!}

{{-- DELETE MODAL --}}
@include('logs::delete')

@push('scripts')
<script>
    $(function () {
        var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm = $('form#delete-log-form'),
                submitBtn = deleteLogForm.find('button[type=submit]');

        $("a[href='#delete-log-modal']").click(function (event) {
            event.preventDefault();
            var date = $(this).data('log-date');
            deleteLogForm.find('input[name=date]').val(date);
            deleteLogModal.find('.modal-body p').html(
                    'Are you sure you want to <span class="label label-danger">DELETE</span> this log file <span class="label label-primary">' + date + '</span> ?'
            );

            deleteLogModal.modal('show');
        });

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
                        location.reload();
                    }
                    else {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(data);
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

        deleteLogModal.on('hidden.bs.modal', function (event) {
            deleteLogForm.find('input[name=date]').val('');
            deleteLogModal.find('.modal-body p').html('');
        });
    });
</script>
@endpush
