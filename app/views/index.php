<div class="media-frame">
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