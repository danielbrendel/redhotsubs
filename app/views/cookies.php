<div id="cookie-consent" class="cookie-consent has-text-centered">
    <div class="cookie-consent-inner">
        This service uses cookies in order to provide functionality. By using our service you agree to this usage.
        @if (env('APP_GOOGLEANALYTICS') !== null)
        Also the services uses Google Analytics to record traffic. 
        Click <a class="link-light-dark" href="javascript:void(0);" onclick="window.gaOptOut(); alert('Google Analytics is now disabled as long as you keep your current cookies.'); location.reload();">here</a> to opt out.
        @endif
    </div>

    <div class="cookie-consent-button">
        <div class="is-pointer" onclick="window.vue.clickedCookieConsentButton()"><i class="fas fa-times" title="Accept and close"></i></div>
    </div>
</div>