<div class="page">
	<h1>{{ $page_title }}</h1>

    <div class="page-content">
        <div class="video-categories">
            @foreach ($categories as $cat)
                <div class="video-category" id="vcat-{{ $cat }}"><a href="javascript:void(0);" onclick="window.vue.toggleVideoCategoryCookie('{{ $cat }}');">{{ ucfirst($cat) }}</a></div>
            @endforeach
        </div>

        <div class="video-title">
            <a id="view-post" href=""></a>
        </div>

        <div class="video-swiper" id="video-swiper" ontouchstart="window.touchStartX = event.changedTouches[0].screenX; window.touchStartY = event.changedTouches[0].screenY;" ontouchend="window.touchEndX = event.changedTouches[0].screenX; window.touchEndY = event.changedTouches[0].screenY; if (((window.touchStartX < window.touchEndX) || (window.touchStartX > window.touchEndX)) && (Math.abs(window.touchEndY - window.touchStartY) < 32)) { window.vue.fetchNextVideo('video-content', 'view-post'); }"></div>
        <div class="video-content" id="video-content"><center><i class="fas fa-spinner fa-spin"></i></center></div>

        <div class="video-controls">
            <a class="button is-success" href="javascript:void(0);" onclick="window.vue.fetchNextVideo('video-content', 'view-post');">Next video</a>
        </div>
    </div>
</div>