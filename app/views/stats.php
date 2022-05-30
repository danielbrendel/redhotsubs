<div class="page">
	<h1>Stats</h1>

    <div class="visitor-summary">
        <div>Range: <input class="stats-input" type="date" id="inp-date-from"/>&nbsp;<input class="stats-input" type="date" id="inp-date-till"/>&nbsp;<a class="button is-dark" href="javascript:void(0);" onclick="window.vue.renderStats('{{ $render_stats_pw }}', '{{ $render_stats_to }}', document.getElementById('inp-date-from').value, document.getElementById('inp-date-till').value);">Go</a></div>
        <div>New visitors: <div class="is-inline-block" id="count-new"></div></div>
        <div>Recurring visitors: <div class="is-inline-block" id="count-recurring"></div></div>
        <div>Total visitors: <div class="is-inline-block" id="count-total"></div></div>
    </div>

    <div class="page-content">
        <canvas id="visitor-stats"></canvas>
    </div>
</div>