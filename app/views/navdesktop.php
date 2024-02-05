<div class="navbar-desktop" id="navbar-desktop">
    @if (isset($subs))
        <?php $lastCat = ''; ?>
        @for ($i = 0; $i < $subs->count(); $i++)
            <?php 
                if ($lastCat !== $subs->get($i)->get('category')) {
                    $lastCat = $subs->get($i)->get('category');

                    echo '<div class="navbar-item is-nav-category media-list-title">' . $subs->get($i)->get('category') . '</div>';
                } 
            ?>

            <div class="media-list-item">
                <div class="media-list-item-title">
                    <a class="" href="{{ url('/' . $subs->get($i)->get('sub_ident')) }}">
                        {{ $subs->get($i)->get('sub_ident') }}
                    </a>
                </div>

                <div class="media-list-item-description">{{ (($subs->get($i)->get('description')) ? $subs->get($i)->get('description') : 'No description available') }}</div>
            </div>
        @endfor
    @endif

    @if (env('APP_ALLOWCUSTOMSUBS'))
        <div class="navbar-item is-nav-category">Custom</div>
        <div>
            <form><div class="navbar-item is-nav-item">r/<input type="text" class="input-dark" placeholder="sub" onkeypress="if (event.which === 13) { event.preventDefault(); window.vue.customSub(this.value); }"></div></form>
            <form><div class="navbar-item is-nav-item">u/<input type="text" class="input-dark" placeholder="user" onkeypress="if (event.which === 13) { event.preventDefault(); window.vue.customUser(this.value); }"></div></form>
        </div>
    @endif
</div>