<div class="media-frame">
	<div class="nav-sorting">
		<center>
		<div class="nav-sorting-item is-inline-block">
			<a id="link-sorting-hot" class="link-sorting-hot button is-danger" class="button is-danger" href="javascript:void(0);" onclick="window.vue.setPostSorting('hot'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('hot');">
				Hot
			</a>
		</div>

		<div class="nav-sorting-item nav-sorting-item-top is-inline-block">
			<a id="link-sorting-top" class="link-sorting-top" href="javascript:void(0);" onclick="window.vue.setPostSorting('top'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('top');">
				<i class="fas fa-star star-color"></i>&nbsp;Top
			</a>
		</div>

		<div class="nav-sorting-item nav-sorting-item-top is-inline-block">
			<a id="link-sorting-new" class="link-sorting-new" href="javascript:void(0);" onclick="window.vue.setPostSorting('new'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('new');">
				New
			</a>
		</div>
		</center>
	</div>

	<div class="media-settings is-hidden" id="media-settings">
		<a href="javascript:void(0);" onclick="document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content'));"><i class="fas fa-sync-alt"></i>&nbsp;Refresh</a>&nbsp;<span id="current-sub" class="text-dark"></span>
	</div>

	<div class="media-content" id="media-content">
		
		<div class="media-info">
			<h2>{{ env('APP_HEADLINE') }}</h2>

			@if (env('APP_INFOTEXTPOS', 'top') === 'top')
				{!! AppSettingsModel::getAbout() !!}
			@endif
		</div>
		

		<div class="media-cards" id="media-cards">
			@foreach ($featured as $item)
				<a href="{{ url('/' . $item) }}">
					<div class="media-card-item">
						<div class="media-card-item-title">
							{{ $item }}
						</div>

						<div class="media-card-item-image" title="{{ $item }}">
							<i class="fas fa-spinner fa-spin"></i>
						</div>
					</div>
				</a>
			@endforeach
		</div>

		@if ((is_object($featUsers)) && (count($featUsers) > 0))
			<div class="media-cards" id="media-cards">
				@foreach ($featUsers as $featUser)
					<a href="{{ url('/user/' . $featUser->get('username')) }}">
						<div class="media-card-item">
							<div class="media-card-item-title">
								u/{{ $featUser->get('username') }}
							</div>

							<div class="media-card-item-image" title="u/{{ $featUser->get('username') }}">
								<i class="fas fa-spinner fa-spin"></i>
							</div>
						</div>
					</a>
				@endforeach
			</div>
		@endif

		<div class="media-list">
			<?php $lastCat = ''; ?>
			@foreach ($subs as $sub)
				<?php 
					if ($lastCat !== $sub->get('category')) {
						$lastCat = $sub->get('category');

						echo '<div class="navbar-item is-nav-category">' . $sub->get('category') . '</div>';
					} 
				?>

				<div class="media-list-item">
					<a class="" href="{{ url('/' . $sub->get('sub_ident')) }}">
						{{ $sub->get('sub_ident') }}
					</a>
				</div>
			@endforeach
		</div>

		@if (env('APP_INFOTEXTPOS', 'top') === 'bottom')
		<div class="media-info no-padding-top">
			{!! AppSettingsModel::getAbout() !!}
		</div>
		@endif
	</div>

	<div class="subs-overlay" id="subs-overlay">
		<div class="subs-overlay-content" id="subs-overlay-content">
			<a class="subs-overlay-content-close" href="javascript:void(0);" onclick="window.vue.toggleSubsOverlay();">Close</a>

			<?php $lastCat = ''; ?>
			@foreach ($subs as $sub)
				<?php 
					if ($lastCat !== $sub->get('category')) {
						$lastCat = $sub->get('category');

						echo '<div class="navbar-item is-nav-category">' . $sub->get('category') . '</div>';
					} 
				?>

				<div class="media-list-item">
					<a class="" href="{{ url('/' . $sub->get('sub_ident')) }}">
						{{ $sub->get('sub_ident') }}
					</a>
				</div>
			@endforeach
		</div>
	</div>

	<div class="scroll-to-top {{ ((env('APP_ENABLEBOTTOMNAV')) ? 'scroll-to-top-above-bottomnav' : '' ) }}">
		<div class="scroll-to-top-inner">
			<a href="javascript:void(0);" onclick="window.scroll({top: 0, left: 0, behavior: 'smooth'}); if (document.getElementById('subs-overlay-content')) { document.getElementById('subs-overlay-content').scroll({top: 0, left: 0, behavior: 'smooth'}); }"><i class="fas fa-arrow-up fa-2x up-color"></i></a>
		</div>
	</div>
</div>