<div id="cookie-consent" class="cookie-consent has-text-centered">
    <div class="cookie-consent-inner">
        This service uses cookies in order to provide its functionality. By using our service you agree to this usage.
        @if (env('APP_GOOGLEANALYTICS') !== null)
        Also we want to use Google Analytics to record traffic. 
        @endif
    </div>

    <div class="cookie-consent-button">
        @if (env('APP_GOOGLEANALYTICS') !== null)
            <div class="is-pointer is-underline" onclick="window.gaOptIn(); window.vue.clickedCookieConsentButton();">Allow cookies and tracking</div>
            <div class="is-pointer" onclick="window.vue.clickedCookieConsentButton();">Allow essential cookies only</div>
        @else
            <div class="is-pointer" onclick="window.vue.clickedCookieConsentButton();">Accept and close</div>
        @endif 
    </div>
</div>