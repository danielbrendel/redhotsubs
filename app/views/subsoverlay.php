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