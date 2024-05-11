<!doctype html>
<html lang="{{ getLocale() }}">
    <head>
        <meta charset="utf-8"/>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

		<meta name="author" content="{{ env('APP_AUTHOR') }}">
		<meta name="description" content="{{ env('APP_DESCRIPTION') }}">
		<meta name="keywords" content="{{ env('APP_TAGS') }}">

		@if ((isset($additional_meta)) && (is_array($additional_meta)))
			@foreach ($additional_meta as $key => $value)
				<meta name="{{ $key }}" property="{{ $key }}" content="{{ $value }}">
			@endforeach
		@endif
		
		<title>
			@if ((isset($page_title)) && (is_string($page_title)) && (strlen($page_title) > 0))
				{{ env('APP_NAME') . ' - ' . $page_title }}
			@else
				{{ env('APP_TITLE') }}
			@endif
		</title>

		<link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}"/>
		{!! ThemeModule::includeThemeAsHtml() !!}
		<link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}"/>

		@if (env('APP_DEBUG'))
		<script src="{{ asset('js/vue.js') }}"></script>
		@else
		<script src="{{ asset('js/vue.min.js') }}"></script>
		@endif
		<script src="{{ asset('js/fontawesome.js') }}"></script>
    </head>

    <body>
        <div id="main">
            <div class="auth">
                <div class="auth-header">
                    <img src="{{ asset('img/logo.png') }}" alt="Logo"/>

                    <h1>{{ env('APP_NAME') }}</h1>
                </div>

                @if (FlashMessage::hasMsg('error'))
                <div class="auth-info">
                    {{ FlashMessage::getMsg('error') }}
                </div>
                @endif

                <div class="auth-hint">
                    Set a new password here.
                </div>

                <div class="auth-form">
                    <form method="POST" action="{{ url('/user/reset') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}"/>

                        <div class="field">
                            <div class="control">
                                <input type="password" class="input" name="password" placeholder="Enter your password" required/>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input type="password" class="input" name="password_confirmation" placeholder="Confirm your password" required/>
                            </div>
                        </div>

                        <div class="field">
                            <div class="control">
                                <input type="submit" class="button is-link" value="Reset"/>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="auth-footer">
                    <div class="auth-footer-social">
                        @if (env('APP_REDDITHOME') !== null)
                            &nbsp;<a title="Reddit" href="https://www.reddit.com/r/{{ env('APP_REDDITHOME') }}"><i class="fab fa-reddit"></i></a>
                        @endif

                        @if (env('APP_TWITTERFEED') !== null)
                            &nbsp;<a title="Twitter" href="https://twitter.com/{{ env('APP_TWITTERFEED') }}"><i class="fab fa-twitter"></i></a>
                        @endif

                        @if (env('APP_INSTAFEED') !== null)
                            &nbsp;<a title="Instagram" href="https://www.instagram.com/{{ env('APP_INSTAFEED') }}"><i class="fab fa-instagram"></i></a>
                        @endif

                        @if (env('APP_DISCORDHOME') !== null)
                            &nbsp;<a title="Discord" href="{{ env('APP_DISCORDHOME') }}"><i class="fab fa-discord"></i></a>
                        @endif
                    </div>

                    <div class="auth-footer-info">
                        <div>&copy; {{ date('Y') }} by {{ env('APP_NAME') }}</div>

                        <div>
                            @if (env('APP_ENABLEAPPPAGE'))
                                <a href="{{ url('/getapp') }}">App</a>&nbsp;&bull;&nbsp;
                            @endif

                            <a href="{{ url('/imprint') }}">Imprint</a>&nbsp;&bull;&nbsp;
                            <a href="{{ url('/privacy') }}">Privacy policy</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal" :class="{'is-active': bShowRegister}">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">Sign Up</p>
                        <button class="delete" aria-label="close" onclick="window.vue.bShowRegister = false;"></button>
                        </header>
                        <section class="modal-card-body is-stretched">
                            <form id="regform" method="POST" action="{{ url('/register') }}">
                                @csrf

                                <div class="field">
                                    <label class="label">E-Mail</label>
                                    <div class="control">
                                        <input class="input" type="email" name="email" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Password</label>
                                    <div class="control">
                                        <input class="input" type="password" name="password" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Password (confirmation)</label>
                                    <div class="control">
                                        <input class="input" type="password" name="password_confirmation" required>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">{{ getGlobalCaptcha()[0] }} + {{ getGlobalCaptcha()[1] }} = ?</label>
                                    <div class="control">
                                        <input class="input" type="text" name="captcha" required>
                                    </div>
                                </div>

                                <div class="field">
                                    By signing up you agree to our <a href="{{ url('/privacy') }}">privacy policy</a>.
                                </div>
                            </form>
                        </section>
                    <footer class="modal-card-foot is-stretched">
                        <button class="button is-success" onclick="this.innerHTML = '<i class=\'fas fa-spinner fa-spin\'></i>&nbsp;' + this.innerHTML; document.getElementById('regform').submit();">Sign Up</button>
                        <button class="button" onclick="vue.bShowRegister = false;">Close</button>
                    </footer>
                </div>
            </div>

            <div class="modal" :class="{'is-active': bShowResetPassword}">
                <div class="modal-background is-almost-not-transparent"></div>
                <div class="modal-card">
                    <header class="modal-card-head is-stretched">
                        <p class="modal-card-title">Reset password</p>
                        <button class="delete" aria-label="close" onclick="vue.bShowResetPassword = false;"></button>
                    </header>
                    <section class="modal-card-body is-stretched">
                        <form id="frmUpdateUserSettings" method="POST" action="{{ url('/user/reset') }}">
                            @csrf 

                            <div class="field">
                                <label class="label">E-Mail Address</label>
                                <div class="control">
                                    <input type="email" class="input" name="email" required/>
                                </div>
                            </div>
                        </form>
                    </section>
                    <footer class="modal-card-foot is-stretched">
                        <button class="button is-success" onclick="this.innerHTML = '<i class=\'fas fa-spinner fa-spin\'></i>&nbsp;' + this.innerHTML; document.getElementById('frmUpdateUserSettings').submit();">Save</button>
                        <button class="button" onclick="vue.bShowResetPassword = false;">Close</button>
                    </footer>
                </div>
            </div>
        </div>

        <script src="{{ asset('js/app.js', true) }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
            });
        </script>
    </body>
</html>