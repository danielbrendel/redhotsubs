<div class="page">
	<h1>{{ $page_title }}</h1>

    <div class="page-content">
        <p class="has-bottom-space">Note: Your favorites depend on your session ID and will be lost upon clearing browser cookies.</p>

        @if (env('APP_ENABLEFAVSHARE'))
        <p class="has-bottom-space">
            <a class="button is-success" href="javascript:void(0);" onclick="window.vue.bShowFavToken = true">Share</a>&nbsp;
            <a class="button is-link" href="javascript:void(0);" onclick="window.vue.bShowImportFavs = true">Import</a>&nbsp;
        </p>
        @endif

        <div id="favorites" class="favorites"></div>
    </div>
</div>