<div class="footer">
    <div class="columns">
        <div class="column is-2"></div>

        <div class="column is-8">
            <div class="footer-frame">
                <div class="footer-content">
                    <div>
                        &copy; {{ date('Y') }} by {{ env('APP_NAME') }} |  

                        @if (env('APP_REDDITHOME') !== null)
                            &nbsp;<a class="is-link-grey" title="Reddit" href="https://www.reddit.com/r/{{ env('APP_REDDITHOME') }}"><i class="fab fa-reddit"></i></a>
                        @endif

                        @if (env('APP_TWITTERFEED') !== null)
                            &nbsp;<a class="is-link-grey" title="Twitter" href="https://twitter.com/{{ env('APP_TWITTERFEED') }}"><i class="fab fa-twitter"></i></a>
                        @endif

                        @if (env('APP_INSTAFEED') !== null)
                            &nbsp;<a class="is-link-grey" title="Instagram" href="https://www.instagram.com/{{ env('APP_INSTAFEED') }}"><i class="fab fa-instagram"></i></a>
                        @endif

                        @if (env('APP_DISCORDHOME') !== null)
                            &nbsp;<a class="is-link-grey" title="Discord" href="{{ env('APP_DISCORDHOME') }}"><i class="fab fa-discord"></i></a>
                        @endif
                    </div>

                    <div>
                        <a class="link-light-dark" href="{{ url('/imprint') }}">Imprint</a>&nbsp;&bull;&nbsp;<a class="link-light-dark" href="{{ url('/privacy') }}">Privacy policy</a>&nbsp;&bull;&nbsp;<a class="link-light-dark" href="{{ url('/contact') }}">Contact</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="column is-2"></div>
    </div>
</div>