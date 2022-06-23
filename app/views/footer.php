<div class="footer">
    <div class="columns">
        <div class="column is-2"></div>

        <div class="column is-8">
            <div class="footer-frame">
                <div class="footer-content">
                    &copy; {{ date('Y') }} by {{ env('APP_NAME') }} | <a class="link-light-dark" href="{{ url('/imprint') }}">Imprint</a>&nbsp;&bull;&nbsp;<a class="link-light-dark" href="{{ url('/privacy') }}">Privacy policy</a> | Visits: {{ $view_count }}

                    @if (env('APP_TWITTERFEED') !== null)
                        | <a class="is-link-grey" title="Twitter" href="https://twitter.com/{{ env('APP_TWITTERFEED') }}"><i class="fab fa-twitter"></i></a>
                    @endif
                </div>
            </div>
        </div>

        <div class="column is-2"></div>
    </div>
</div>