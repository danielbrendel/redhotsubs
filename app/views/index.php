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
			<h2 class="is-index-headline">{{ env('APP_HEADLINE') }}</h2>

			@if (env('APP_INFOTEXTPOS', 'top') === 'top')
				<div class="is-infotext-top">{!! AppSettingsModel::getAbout() !!}</div>
			@endif
		</div>
		

		<div class="media-cards" id="media-cards">
			<h3>Featured subs</h3>

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

		@if ((isset($featUsers)) && (is_object($featUsers)) && (count($featUsers) > 0))
			<div class="media-cards" id="media-cards">
				<h3>Featured creators</h3>

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

		@if (env('APP_SHOWTRENDINGUSERS'))
			@if ((isset($trendUsers)) && (is_object($trendUsers)) && (count($trendUsers) > 0))
				<div class="media-cards" id="media-cards">
					<h3>Trending creators</h3>

					@foreach ($trendUsers as $trendUser)
						<a href="{{ url('/user/' . $trendUser->get('username')) }}">
							<div class="media-card-item">
								<div class="media-card-item-title">
									u/{{ $trendUser->get('username') }}
								</div>

								<div class="media-card-item-image" title="u/{{ $trendUser->get('username') }}">
									<i class="fas fa-spinner fa-spin"></i>
								</div>
							</div>
						</a>
					@endforeach
				</div>
			@endif
		@endif

		<div class="media-list">
			<?php $lastCat = ''; ?>
			@foreach ($subs as $sub)
				<?php 
					if ($lastCat !== $sub->get('category')) {
						$lastCat = $sub->get('category');

						echo '<div class="navbar-item is-nav-category media-list-title">' . $sub->get('category') . '</div>';
					} 
				?>

				<div class="media-list-item">
					@if (!env('APP_RENDERSUBTHUMBNAIL'))
						<div class="media-list-item-title">
							<a class="" href="{{ url('/' . $sub->get('sub_ident')) }}">
								{{ $sub->get('sub_ident') }}
							</a>
						</div>

						<div class="media-list-item-description">{{ (($sub->get('description')) ? $sub->get('description') : 'No description available') }}</div>
					@else
						<div class="media-list-item-image">
							<img src="{{ asset('img/placeholder.png') }}" data-id="{{ $sub->get('sub_ident') }}" alt="thumbnail"/>
						</div>

						<div class="media-list-item-info">
							<div class="media-list-item-title">
								<a class="" href="{{ url('/' . $sub->get('sub_ident')) }}">
									{{ $sub->get('sub_ident') }}
								</a>
							</div>

							<div class="media-list-item-description">{{ (($sub->get('description')) ? $sub->get('description') : 'No description available') }}</div>
						</div>
					@endif
				</div>
			@endforeach
		</div>

		@if (env('APP_INFOTEXTPOS', 'top') === 'bottom')
		<div class="media-info no-padding-top">
			{!! AppSettingsModel::getAbout() !!}
		</div>
		@endif
	</div>
</div>