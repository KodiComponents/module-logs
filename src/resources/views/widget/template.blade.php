<div class="panel panel-success panel-dark dashboard-widget" data-id="{{ $widget->getId() }}">
    <div class="panel-heading">
        <div class="panel-heading-controls" v-if="settings">
            <button type="button" class="btn btn-default btn-xs" data-icon="times"></button>
        </div>
        <div class="panel-title">
          @lang('logs::core.widget.title')
        </div>
    </div>
    <div class="stat-panel">
        <div class="stat-row">
            <div class="stat-counters no-padding text-center">
                @php($i = 0)
                @foreach($percents as $level => $item)
                    @if($i % 3 == 0)
                    </div>
                    <div class="stat-counters no-padding text-center">
                    @endif
                    <div class="stat-cell col-xs-4 padding-sm no-padding-hr @if($item['count'] > 0) text-warning @endif">
                        <span class="text-xs">
                            {!! Html::link(route('log-viewer::logs.list'), $item['name']) !!}
                            <br />
                            <strong>{{ $item['count'] }}</strong>
                        </span>
                        <i class="bg-icon" style="font-size: 50px; line-height: 50px; height: 50px; bottom: 12px; left: 0">{!! log_styler()->icon($level) !!}</i>
                    </div>
                    @php($i++)
                @endforeach
            </div>
        </div>
    </div>
</div>