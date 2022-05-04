<!doctype html>
<html lang="{{ getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-with, initial-scale=1.0">

		<meta name="author" content="{{ env('APP_AUTHOR') }}">
		<meta name="description" content="{{ env('APP_DESCRIPTION') }}">
		<meta name="tags" content="{{ env('APP_TAGS') }}">

		@if ((isset($additional_meta)) && (is_array($additional_meta)))
			@foreach ($additional_meta as $key => $value)
				<meta name="{{ $key }}" content="{{ $value }}">
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
		<link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}"/>

		@if (env('APP_DEBUG'))
		<script src="{{ asset('js/vue.js') }}"></script>
		@else
		<script src="{{ asset('js/vue.min.js') }}"></script>
		@endif
		<script src="{{ asset('js/fontawesome.js') }}"></script>

		@if (env('APP_GOOGLEANALYTICS') !== null)
			<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('APP_GOOGLEANALYTICS') }}"></script>
			<script>
				var gaProperty = '{{ env('APP_GOOGLEANALYTICS') }}';
				var disableStr = 'ga-disable-' + gaProperty;
				window.gaOptOut = function() {
					document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
					window[disableStr] = true;
				};
				if (document.cookie.indexOf(disableStr + '=true') > -1) {
					window[disableStr] = true;
				}
				window.dataLayer = window.dataLayer || [];
				window.gtag = function(){dataLayer.push(arguments);};
				gtag('js', new Date());
				gtag('config', '{{ env('APP_GOOGLEANALYTICS') }}', { 'anonymize_ip': true} );
			</script>
		@else
			<script>
				window.gtag = function(){};
			</script>
		@endif
	</head>
	
	<body>
		<div id="main">
			<div class="container">
				<div class="columns">
					<div class="column is-2 non-mobile"></div>

					<div class="column is-8 no-bottom-padding">
						{%navbar%}

						{%cookies%}
						{%info%}
						{%content%}

						{%footer%}

						<div class="modal" :class="{'is-active': bShowAgeConsent}">
							<div class="modal-background is-almost-not-transparent"></div>
							<div class="modal-card">
								<header class="modal-card-head is-stretched">
									<p class="modal-card-title">Please verify your age</p>
									<!--button class="delete" aria-label="close" onclick="vue.bShowAgeConsent = false;"></button-->
								</header>
								<section class="modal-card-body is-stretched">
									{!! AppSettingsModel::getAgeConsent() !!}
								</section>
								<footer class="modal-card-foot is-stretched">
									<button class="button is-success" onclick="window.vue.markAgeConsent();">Confirm and continue</button>
								</footer>
							</div>
						</div>
					</div>

					<div class="column is-2 non-mobile"></div>
				</div>
			</div>
		</div>

		<script src="{{ asset('js/app.js') }}"></script>
		<script>
			document.addEventListener('DOMContentLoaded', function(){
				window.vue.appName = '{{ env('APP_NAME') }}';

				window.vue.initNavbar();
				window.vue.handleCookieConsent();

				window.fetch_item_after = null;

				@if (env('APP_SHOWAGECONSENT'))
					if (!window.vue.isAgeConsentMarked()) {
						window.vue.bShowAgeConsent = true;
					}
				@endif

				@if ((isset($show_sub)) && (is_string($show_sub)) && (strlen($show_sub) > 0))
					document.getElementById('media-content').innerHTML = '';
					document.getElementById('current-sub').innerHTML = '{{ $show_sub }}';
					window.vue.fetchPosts('{{ $show_sub }}/', window.vue.getPostSorting(), document.getElementById('media-content'));
				@else
					if (document.getElementById('media-cards')) {
						window.vue.renderCardImages();
					}
				@endif

				if (window.vue.getPostSorting() == 'hot') {
					document.getElementsByClassName('link-sorting-hot')[0].style.textDecoration = 'underline';
					document.getElementsByClassName('link-sorting-hot')[1].style.textDecoration = 'underline';
				} else if (window.vue.getPostSorting() == 'top') {
					document.getElementsByClassName('link-sorting-top')[0].style.textDecoration = 'underline';
					document.getElementsByClassName('link-sorting-top')[1].style.textDecoration = 'underline';
				} else if (window.vue.getPostSorting() == 'new') {
					document.getElementsByClassName('link-sorting-new')[0].style.textDecoration = 'underline';
					document.getElementsByClassName('link-sorting-new')[1].style.textDecoration = 'underline';
				}
			});
		</script>
	</body>
</html>