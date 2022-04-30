<div class="media-frame">
	<div class="nav-sorting">
		<center>
		<div class="nav-sorting-item is-inline-block">
			<a id="link-sorting-hot" class="button is-danger" href="javascript:void(0);" onclick="window.vue.setPostSorting('hot'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('hot');">
				Hot
			</a>
		</div>

		<div class="nav-sorting-item nav-sorting-item-top is-inline-block">
			<a id="link-sorting-top" href="javascript:void(0);" onclick="window.vue.setPostSorting('top'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('top');">
				<i class="fas fa-star star-color"></i>&nbsp;Top
			</a>
		</div>

		<div class="nav-sorting-item nav-sorting-item-top is-inline-block">
			<a id="link-sorting-new" href="javascript:void(0);" onclick="window.vue.setPostSorting('new'); document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content')); window.vue.setSortingUnderline('new');">
				New
			</a>
		</div>
		</center>
	</div>

	<div class="media-settings">
		<a href="javascript:void(0);" onclick="document.getElementById('media-content').innerHTML = ''; window.fetch_item_after = null; window.vue.fetchPosts(window.vue.getSubSelection(), window.vue.getPostSorting(), document.getElementById('media-content'));"><i class="fas fa-sync-alt"></i>&nbsp;Refresh</a>&nbsp;<span id="current-sub"></span>
	</div>

	<div class="media-content" id="media-content"></div>

	<div class="scroll-to-top">
		<div class="scroll-to-top-inner">
			<a href="javascript:void(0);" onclick="window.scroll({top: 0, left: 0, behavior: 'smooth'});"><i class="fas fa-arrow-up fa-2x up-color"></i></a>
		</div>
	</div>
</div>