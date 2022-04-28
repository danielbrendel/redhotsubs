<!doctype html>
<html lang="{{ getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-with, initial-scale=1.0">

		<meta name="author" content="{{ env('APP_AUTHOR') }}">
		<meta name="description" content="{{ env('APP_DESCRIPTION') }}">
		<meta name="tags" content="{{ env('APP_TAGS') }}">
		
		<title>{{ env('APP_TITLE') }}</title>

		<link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}"/>
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

					<div class="column is-8 no-bottom-padding">
						{%navbar%}

						{%cookies%}
						{%content%}

						{%footer%}
					</div>

					<div class="column is-2 non-mobile"></div>
				</div>
			</div>
		</div>

		<script src="{{ asset('js/app.js') }}"></script>
		<script>
			document.addEventListener('DOMContentLoaded', function(){
				window.vue.initNavbar();
				window.vue.handleCookieConsent();

				window.vue.defaultSub = '{{ env('APP_DEFAULTSUB') }}';
				window.fetch_item_after = null;

				if (window.vue.getPostSorting() == 'hot') {
					document.getElementById('link-sorting-hot').style.textDecoration = 'underline';
				} else if (window.vue.getPostSorting() == 'top') {
					document.getElementById('link-sorting-top').style.textDecoration = 'underline';
				} else if (window.vue.getPostSorting() == 'new') {
					document.getElementById('link-sorting-new').style.textDecoration = 'underline';
				}

				window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content'));
			});
		</script>
	</body>
</html>