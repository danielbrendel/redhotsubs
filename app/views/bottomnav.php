<div class="bottomnav">
    <div class="bottomnav-items">
        <div class="bottomnav-item">
            <a href="{{ url('/') }}">
                <div><i class="fas fa-home"></i></div>
                <div>Home</div>
            </a>
        </div>

        <div class="bottomnav-item">
            <a href="javascript:void(0);" onclick="window.vue.toggleSubsOverlay();">
                <div><i class="fas fa-th"></i></div>
                <div>Subs</div>
            </a>
        </div>

        <div class="bottomnav-item">
            <a href="{{ url('/video') }}">
                <div><i class="fas fa-video"></i></div>
                <div>Video</div>
            </a>
        </div>

        <div class="bottomnav-item">
            <a href="{{ url('/creators') }}">
                <div><i class="fas fa-users"></i></div>
                <div>Creators</div>
            </a>
        </div>

        <div class="bottomnav-item">
            <a href="{{ url('/favorites') }}">
                <div><i class="fas fa-heart"></i></div>
                <div>Favorites</div>
            </a>
        </div>
    </div>
</div>