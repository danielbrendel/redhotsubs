<!-- Error 404 yield file -->

<div class="media-frame">
	<div class="page">
        <h1>Oops!</h1>

        <p>We could not find <strong class="text-bright">{{ $_SERVER['REQUEST_URI'] }}</strong> here on {{ env('APP_NAME') }}</p>

        <small>Error 404</small>
    </div>
</div>