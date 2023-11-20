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
            <div class="container">
				<div class="columns">
					<div class="column is-2 non-mobile"></div>

					<div class="column is-8">
                        <div class="auth">
                            <div class="auth-header">
                                <img src="{{ asset('img/logo.png') }}" alt="Logo"/>

                                <h1>{{ env('APP_NAME') }}</h1>
                            </div>

                            <div class="auth-hint">
                                Currently private mode is activated. Please enter your authentication token in order to view the content.
                            </div>

                            @if (FlashMessage::hasMsg('error'))
                            <div class="auth-info">
                                {{ FlashMessage::getMsg('error') }}
                            </div>
                            @endif

                            <div class="auth-form">
                                <form method="POST" action="{{ url('/auth') }}">
                                    @csrf

                                    <div class="field">
                                        <div class="control">
                                            <input type="text" class="input" name="token" required/>
                                        </div>
                                    </div>

                                    <div class="field">
                                        <div class="control">
                                            <input type="submit" class="button is-link" value="Submit"/>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="auth-contact">
                                Need help? Send us an e-mail: <a href="mailto:{{ env('APP_CONTACT') }}">{{ env('APP_CONTACT') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="column is-2 non-mobile"></div>
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