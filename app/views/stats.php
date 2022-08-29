<div class="page">
	<h1>Stats</h1>

    <div class="paragraph">
        Currently online: <span id="stats-online-count">{{ $online_count }}</span>
    </div>

    <div class="visitor-summary">
        <div>
            Range: <input class="stats-input" type="date" id="inp-date-from"/>&nbsp;<input class="stats-input" type="date" id="inp-date-till"/>&nbsp;
            <select class="stats-input" onchange="window.vue.renderStats('{{ $render_stats_pw }}', '{{ $render_stats_to }}', this.value, '{{ $render_stats_end }}');">
                <option value="{{ $render_stats_start }}">- Select a range -</option>
                @foreach ($predefined_dates as $key => $value)
                    <option value="{{ $value }}">{{ $key }}</option>
                @endforeach
            </select>
            &nbsp;<a class="button is-dark" href="javascript:void(0);" onclick="window.vue.renderStats('{{ $render_stats_pw }}', '{{ $render_stats_to }}', document.getElementById('inp-date-from').value, document.getElementById('inp-date-till').value);">Go</a>
        </div>
        
        <div>Sum: <div class="is-inline-block" id="count-total"></div></div>
        <div>Avg per day: <div class="is-inline-block" id="count-avg-day"></div></div>
        <div>Avg per hour: <div class="is-inline-block" id="count-avg-hour"></div></div>
    </div>

    <div class="page-content">
        <canvas id="visitor-stats"></canvas>
    </div>
</div>