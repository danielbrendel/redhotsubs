<!doctype html>
<html lang="{{ getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<meta name="author" content="{{ env('APP_AUTHOR') }}">
		<meta name="description" content="{{ env('APP_DESCRIPTION') }}">
		<meta name="keywords" content="{{ env('APP_TAGS') }}">

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
		{!! ThemeModule::includeThemeAsHtml() !!}
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
				window.gaOptIn = function() {
					document.cookie = disableStr + '=false; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
					window[disableStr] = false;
				}
				if (document.cookie.indexOf(disableStr + '=true') > -1) {
					window[disableStr] = true;
				} else if (document.cookie.indexOf(disableStr + '=false') > -1) {
					window[disableStr] = false;
				} else if (document.cookie.indexOf(disableStr) == -1) {
					window.gaOptOut();
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

		{!! AppSettingsModel::getHeadCode() !!}
	</head>
	
	<body>
		<div id="main">
			<div class="container">
				<div class="columns">
					<div class="column is-2 non-mobile"></div>

					<div class="column is-8 no-bottom-padding">
						{%navbar%}
						{%navdesktop%}

						{%cookies%}
						{%info%}
						{%content%}

						{%footer%}

						@if (env('APP_ENABLEBOTTOMNAV'))
							@include('bottomnav.php')
						@endif

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

						<div class="modal" :class="{'is-active': bShowFavToken}">
							<div class="modal-background is-almost-not-transparent"></div>
							<div class="modal-card">
								<header class="modal-card-head is-stretched">
									<p class="modal-card-title">Generate share token</p>
									<button class="delete" aria-label="close" onclick="vue.bShowFavToken = false;"></button>
								</header>
								<section class="modal-card-body is-stretched">
									<div class="field">
										<label class="label">Generate token and share it</label>
										<div class="control">
											<input type="text" class="input has-small-button-next" id="txtShareToken" value="{{ ((isset($share_token)) ? $share_token : '') }}"/>
											<a class="button" href="javascript:void(0);" onclick="window.vue.copyToClipboard(document.getElementById('txtShareToken').value);"><i class="far fa-copy"></i></a>
										</div>
									</div>
								</section>
								<footer class="modal-card-foot is-stretched">
									<button class="button is-success" onclick="vue.genFavToken('txtShareToken');">Generate</button>
									<button class="button" onclick="vue.bShowFavToken = false;">Close</button>
								</footer>
							</div>
						</div>

						<div class="modal" :class="{'is-active': bShowImportFavs}">
							<div class="modal-background is-almost-not-transparent"></div>
							<div class="modal-card">
								<header class="modal-card-head is-stretched">
									<p class="modal-card-title">Import favorites</p>
									<button class="delete" aria-label="close" onclick="vue.bShowImportFavs = false;"></button>
								</header>
								<section class="modal-card-body is-stretched">
									<div class="field">
										<label class="label">Enter share token</label>
										<div class="control">
											<input type="text" class="input" id="txtImportToken"/>
										</div>
									</div>
								</section>
								<footer class="modal-card-foot is-stretched">
									<button class="button is-success" onclick="vue.importFavs('txtImportToken');">Import</button>
									<button class="button" onclick="vue.bShowImportFavs = false;">Close</button>
								</footer>
							</div>
						</div>
					</div>

					<div class="column is-2 non-mobile"></div>
				</div>
			</div>
		</div>

		<script src="{{ asset('js/app.js', true) }}"></script>
		<script>
			document.addEventListener('DOMContentLoaded', function(){
				window.vue.appName = '{{ env('APP_NAME') }}';

				window.vue.initNavbar();
				window.vue.handleCookieConsent();

				window.vue.defaultSub = '{{ env('APP_DEFAULTSUB') }}';

				window.fetch_item_after = null;
				window.statsChart = null;

				window.isInSubsDropdown = false;
				window.isInDeskNavbar = false;

				window.enableVideoSwipe = {{ env('APP_ENABLEVIDEOSWIPE', false) ? 'true' : 'false' }};

				let elMain = document.getElementById('main');
				if (elMain) {
					elMain.onclick = function() {
						if ((window.innerWidth >= 1088) && (!window.isInSubsDropdown) && (!window.isInDeskNavbar)) {
							let elNavbarDesktop = document.getElementById('navbar-desktop');
							if (elNavbarDesktop) {
								if (elNavbarDesktop.style.display === 'block') {
									elNavbarDesktop.style.display = 'none';
								}
							}
						}
					};
				}

				let elDropdown = document.getElementById('navbar-subs-dropdown');
				if (elDropdown) {
					elDropdown.onclick = function() {
						if (window.innerWidth >= 1088) {
							let elNavbarDesktop = document.getElementById('navbar-desktop');
							if (elNavbarDesktop) {
								if ((elNavbarDesktop.style.display === 'none') || (elNavbarDesktop.style.display == '')) {
									elNavbarDesktop.style.display = 'block';
								} else {
									elNavbarDesktop.style.display = 'none';
								}
							}
						}
					};
					elDropdown.onmouseover = function() {
						if (window.innerWidth >= 1088) {
							window.isInSubsDropdown = true;
						}
					};
					elDropdown.onmouseout = function() {
						if (window.innerWidth >= 1088) {
							window.isInSubsDropdown = false;
						}
					};
				}

				let elNavbarDesktop = document.getElementById('navbar-desktop');
				if (elNavbarDesktop) {
					elNavbarDesktop.onmouseover = function() {
						if (window.innerWidth >= 1088) {
							window.isInDeskNavbar = true;
						}
					};
					elNavbarDesktop.onmouseout = function() {
						if (window.innerWidth >= 1088) {
							window.isInDeskNavbar = false;
						}
					};
				}

				@if (env('APP_SHOWAGECONSENT'))
					if (!window.vue.isAgeConsentMarked()) {
						window.vue.bShowAgeConsent = true;
					}
				@endif

				window.vue.bScrollAutoLoad = {{ (env('APP_SCROLLAUTOLOAD', false)) ? 'true' : 'false' }};

				if (window.vue.getAllEnabledVideoCategories().length == 0) {
					window.vue.toggleVideoCategoryCookie('{{ env('APP_DEFAULTCAT') }}');
				}

				let vidCats = document.getElementsByClassName('video-category');
				if (vidCats.length > 0) {
					for (let i = 0; i < vidCats.length; i++) {
						if (window.vue.isVideoCategoryEnabled(vidCats[i].id.substr(vidCats[i].id.indexOf('-') + 1))) {
							vidCats[i].classList.add('video-category-enabled');
						}
					}
				}

				if ((document.getElementById('video-content')) && (document.getElementById('view-post'))) {
					window.vue.fetchNextVideo('video-content', 'view-post');

					if (!window.enableVideoSwipe) {
						document.getElementById('video-swiper').remove();
					}
				}

				if (document.getElementById('favorites')) {
					window.vue.fetchFavorites('favorites');
				}

				@if ((isset($show_sub)) && (is_string($show_sub)) && (strlen($show_sub) > 0))
					document.getElementById('media-content').innerHTML = '';
					document.getElementById('current-sub').innerHTML = '{{ $show_sub }}';
					window.vue.setSubSelection('{{ $show_sub }}/');
					window.vue.fetchPosts('{{ $show_sub }}', window.vue.getPostSorting(), document.getElementById('media-content'));
				@else
					if (document.getElementById('media-cards')) {
						window.vue.renderCardImages();
					}
				@endif

				if (window.vue.getPostSorting() == 'hot') {
					let linkSortingHot = document.getElementsByClassName('link-sorting-hot');
					if ((linkSortingHot) && (linkSortingHot.length == 2)) {
						linkSortingHot[0].style.textDecoration = 'underline';
						linkSortingHot[1].style.textDecoration = 'underline';
					}
				} else if (window.vue.getPostSorting() == 'top') {
					let linkSortingTop = document.getElementsByClassName('link-sorting-top');
					if ((linkSortingTop) && (linkSortingTop.length == 2)) {
						linkSortingTop[0].style.textDecoration = 'underline';
						linkSortingTop[1].style.textDecoration = 'underline';
					}
				} else if (window.vue.getPostSorting() == 'new') {
					let linkSortingNew = document.getElementsByClassName('link-sorting-new');
					if ((linkSortingNew) && (linkSortingNew.length == 2)) {
						linkSortingNew[0].style.textDecoration = 'underline';
						linkSortingNew[1].style.textDecoration = 'underline';
					}
				}

				@if ((isset($render_stats_to)) && (isset($render_stats_pw)))
					window.vue.renderStats('{{ $render_stats_pw }}', '{{ $render_stats_to }}', '{{ $render_stats_start }}');

					setTimeout(function() {
						window.vue.updateOnlineCount('stats-online-count', '{{ $render_stats_pw }}');
					}, 10000);
				@endif
			});
		</script>
	</body>
</html>