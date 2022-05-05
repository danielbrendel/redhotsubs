<div class="page">
	<h1>Quick News</h1>

    <div class="page-content">
        @if (env('APP_TWITTERFEED') !== null)
        <a class="twitter-timeline" data-theme="dark" href="https://twitter.com/{{ env('APP_TWITTERFEED') }}?ref_src=twsrc%5Etfw">Tweets by {{ env('APP_TWITTERFEED') }}</a> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        @endif
    </div>
</div>           