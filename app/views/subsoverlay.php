<div class="subs-overlay" id="subs-overlay">
    <div class="subs-overlay-content" id="subs-overlay-content">
        <a class="subs-overlay-content-close" href="javascript:void(0);" onclick="window.vue.toggleSubsOverlay();">Close</a>

        <?php $lastCat = ''; ?>
        @foreach ($subs as $sub)
            <?php 
                if ($lastCat !== $sub->get('category')) {
                    $lastCat = $sub->get('category');

                    echo '<div class="navbar-item is-nav-category media-list-title">' . $sub->get('category') . '</div>';
                } 
            ?>

            <div class="media-list-item">
                <div class="media-list-item-title">
                    <a class="" href="{{ url('/' . $sub->get('sub_ident')) }}">
                        {{ $sub->get('sub_ident') }}
                    </a>
                </div>

                <div class="media-list-item-description">{{ (($sub->get('description')) ? $sub->get('description') : 'No description available') }}</div>
            </div>
        @endforeach
    </div>
</div>