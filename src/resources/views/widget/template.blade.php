<div class="stat-panel no-margin dashboard-widget darker" data-id="{{ $widget->getId() }}">
    <div class="stat-row">
        <div class="stat-cell bg-success">
            <span class="text-lg">Logs</span> <span class="text-sm">Error statistics</span>
            <button type="button" class="close" v-on:click="remove('{{ $widget->getId() }}')" data-icon="times"></button>
        </div>
    </div>
    <div class="stat-row">
        <div class="stat-counters no-padding text-center">
            @php($i = 0)
            @foreach($percents as $level => $item)
                @if($i % 3 == 0)
                </div>
                <div class="stat-counters no-padding text-center">
                @endif
                <div class="stat-cell col-xs-4 padding-sm no-padding-hr @if($item['count'] > 0) text-warning @endif">
                    <span class="text-xs">{!! log_styler()->icon($level) !!} {{ $item['name'] }}</span><br>
                    <span class="text-bg">
                        <strong>{{ $item['count'] }} - {!! $item['percent'] !!} %</strong>
                    </span>
                </div>

                @php($i++)
            @endforeach
        </div>
    </div>
</div>